<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Jobs\ProcessPortfolioVisuals;
use App\Jobs\SendEmailJob;
use App\Models\Activity;
use App\Models\Agency;
use App\Models\Attachment;
use App\Models\Creative;
use App\Models\Job;
use App\Models\Link;
use App\Models\User;
use App\Models\PortfolioCaptureLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public $cache_expiration_time = 60;

    public function index(Request $request)
    {
        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                'last_name',
                'username',
                'email',
                AllowedFilter::exact('role'),
                'status',
                'is_visible',

                //Agency Filters
                AllowedFilter::scope('company_slug'),
                AllowedFilter::scope('agency_name'),
                AllowedFilter::scope('first_name'),

                //Creative Filters
                AllowedFilter::scope('category_id'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city_id'),

                AllowedFilter::scope('is_featured'),
            ])

            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $users = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new UserCollection($users);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = new User();
            $user->uuid = Str::uuid();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $this->get_username_from_email($request->email);
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role = $request->role;
            $user->save();

            $role = Role::findByName($request->role);
            $user->assignRole($role);

            $admin = User::where('email', env('ADMIN_EMAIL'))->first();

            $str = Str::uuid();
            if (in_array($user->role, ['agency'])) {
                $agency = new Agency();
                $agency->uuid = $str;
                $agency->user_id = $user->id;
                $agency->name = $request->agency_name;
                $agency->save();

                Link::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'label' => 'linkedin',
                    'url' => $request->linkedin_profile ?? '',
                ]);

                SendEmailJob::dispatch([
                    'receiver' => $admin,
                    'data' => [
                        'user' => $user,
                        'url' => $request->linkedin_profile ?? '',
                    ],
                ], 'new_user_registration_agency_role');
            } elseif (in_array($user->role, ['creative'])) {
                $creative = new Creative();
                $creative->uuid = $str;
                $creative->user_id = $user->id;
                $creative->save();

                Link::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'label' => 'portfolio',
                    'url' => $request->portfolio_site ?? '',
                ]);

                SendEmailJob::dispatch([
                    'receiver' => $admin,
                    'data' => [
                        'user' => $user,
                        'url' => $request->portfolio_site ?? '',
                    ],
                ], 'new_user_registration_creative_role');
            }

            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
            throw new ApiException($e, 'US-01');
        }
    }

    public function show($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function update(UpdateUserRequest $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $oldStatus = $user->status;
            $newStatus = $request->input('status');

            if ($newStatus === 'active' && $oldStatus === 'pending') {


                if ($user->role == 'agency') {
                    SendEmailJob::dispatch([
                        'receiver' => $user, 'data' => $user,
                    ], 'account_approved_agency');
                }


                /**
                 * Generate portfolio website preview
                 */
                if ($user->role == 'creative') {

                    SendEmailJob::dispatch([
                        'receiver' => $user, 'data' => $user,
                    ], 'account_approved');


                    $portfolio_website = $user->portfolio_website_link()->first();
                    if ($portfolio_website) {
                        Attachment::where('user_id', $user->id)->where('resource_type', 'website_preview')->delete();
                        ProcessPortfolioVisuals::dispatch($user->id, $portfolio_website->url);
                    }

                    $this->send_notification_to_agency($user);
                }
            }

            if ($newStatus === 'inactive' && $oldStatus === 'pending') {
                SendEmailJob::dispatch([
                    'receiver' => $user, 'data' => $user,
                ], 'account_denied');
            }
            $user->update($request->all());

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function destroy($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $user->delete();

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFound($e);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function get_username_from_email($email)
    {
        $username = Str::before($email, '@');
        $username = Str::slug($username);

        $user = User::withTrashed()->where('username', $username)->first();
        if ($user) {
            $username = $username . '-' . Str::random(5);
        }

        return $username;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'The provided email does not correspond to a registered user. Please check your email or register for an account.'], 404);
        }

        $custom_wp_hasher = new PasswordHash(8, true);

        if (!$custom_wp_hasher->CheckPassword($request->password, $user->password)) { //$plain_password, $password_hashed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->status != 'active') {
            return response()->json(['message' => 'Account not approved'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $activityData = [
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'type' => 'login',
            'message' => "Logged In",
            'body' => [
                'model' => 'User',
                'action' => 'Logged in',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ],
        ];
        Activity::create($activityData);

        return response()->json([
            'token' => $token,
            'subscription_status' => get_subscription_status_string($user),
            'user' => new UserResource($user),
        ], 200);
    }

    public function re_login(Request $request)
    {
        $user = $request->user();

        if ($user->status != 'active') {
            return response()->json(['message' => 'Account not approved'], 401);
        }

        return response()->json([
            'token' => $request->bearerToken(),
            'subscription_status' => get_subscription_status_string($user),
            'user' => new UserResource($user),
        ], 200);
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->user();

        $custom_wp_hasher = new PasswordHash(8, true);

        if (!$custom_wp_hasher->CheckPassword($request->input('old_password'), $user->password)) {
            return response()->json(['message' => 'Incorrect old password'], 401);
        }

        // Update the user's password using your custom password hashing method
        $user->password = $custom_wp_hasher->HashPassword($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function confirm_password(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        $custom_wp_hasher = new PasswordHash(8, true);

        if (!$custom_wp_hasher->CheckPassword($request->password, $user->password)) { //$plain_password, $password_hashed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['message' => 'Password confirmed successfully'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $activityData = [
            'uuid' => Str::uuid(),
            'user_id' => $request->user()->id,
            'type' => 'logout',
            'message' => "Logged Out",
            'body' => [
                'model' => 'User',
                'action' => 'Logged in'
            ],
        ];
        Activity::create($activityData);

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function get_users_for_posts()
    {
        $cacheKey = 'all_users_with_posts';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::select('id', 'uuid', 'first_name', 'last_name', 'email', 'role', 'is_visible')->withCount('posts')->get();
        });

        return $users;
    }

    public function get_users_for_attachments()
    {
        $cacheKey = 'all_users_with_attachments';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::select('id', 'uuid', 'first_name', 'last_name', 'email', 'role', 'is_visible')
                ->where('role', '!=', 1)
                ->whereHas('attachments')
                ->withCount('attachments')
                ->orderByDesc('attachments_count') // Order by first name
                ->get();
        });

        return $users;
    }

    public function get_creatives()
    {
        $cacheKey = 'all_creatives';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::select('id', 'uuid', 'first_name', 'last_name', 'email')
                ->where('role', 4)
                ->get();
        });

        return $users;
    }

    public function get_all_users()
    {
        $cacheKey = 'all_users';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::toBase()->select('id', 'uuid', 'first_name', 'last_name', 'email')
                ->get();
        });

        return $users;
    }

    public function contact_us_form_info(Request $request)
    {
        $admin = User::where('email', env('ADMIN_EMAIL'))->first();

        SendEmailJob::dispatch([
            'receiver' => $admin,
            'data' => $request->all(),
        ], 'contact_us_inquiry');
    }

    public function send_notification_to_agency($user) //send notifications to all agency users about new creative user joined the site, those agency users who have active jobs with similar industry title.
    {
        try {
            $category_id = $user->creative->category_id;
            $creative_url = sprintf('%s/creative/%s', env('FRONTEND_URL'), $user->username);
            $agencies = Job::where('category_id', $category_id)->where('status', 1)->pluck('user_id');
            foreach ($agencies as $agency_id) {
                create_notification($agency_id, sprintf("New creative user <a href='%s'>%s</a> has joined the website.", $creative_url, $user->full_name));
            }
        } catch (\Exception $e) {
        }
    }

    public function capturePortfolioSnapshot(Request $request, $uuid)
    {
        $log = null;
        $time_diff = 0;
        $user = User::where('uuid', $uuid)->first();

        if ($user) {
            /**
             * Generate portfolio website preview
             */
            if ($user->role == 'creative') {

                $log = PortfolioCaptureLog::where('user_id', $user->id)->first();

                $portfolio_website = $user->portfolio_website_link()->first();
                if ($portfolio_website) {
                    $att_query = Attachment::where('user_id', $user->id)->where('resource_type', 'website_preview');
                    $att_rec = $att_query->first();

                    if ($log) {

                        $capture = $att_rec ? getAttachmentBasePath() . $att_rec->path : '';
                        $checked_at = date('Y-m-d H:i:s', time());
                        $initiated_at = $log->initiated_at ? $log->initiated_at : $checked_at;

                        $time_diff = strtotime($checked_at) - strtotime($initiated_at); // in seconds

                        $status = strlen($capture) > 0 ? "success" : ($time_diff > 15 ? "failed" : "pending");

                        $log->update([
                            'capture' => $capture,
                            'status' => $status,
                            'checked_at' => $checked_at
                        ]);
                        $log = PortfolioCaptureLog::where('user_id', $user->id)->first();
                    } else {
                        $att_query->delete();

                        ProcessPortfolioVisuals::dispatch($user->id, $portfolio_website->url);
                        PortfolioCaptureLog::create([
                            'user_id' => $user->id,
                            'url' => $portfolio_website->url,
                            'capture' => '',
                            'status' => 'pending',
                            'initiated_at' => date('Y-m-d H:i:s', time()),
                            'checked_at' => date('Y-m-d H:i:s', time())
                        ]);

                        $log = PortfolioCaptureLog::where('user_id', $user->id)->first();
                    }
                }
            }

            return response()->json(['time_diff' => $time_diff, 'data' => $log], 200);
        }
    }
}
