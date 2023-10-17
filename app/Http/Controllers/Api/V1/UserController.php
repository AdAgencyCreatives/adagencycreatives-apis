<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Jobs\SendEmailJob;
use App\Models\Agency;
use App\Models\Creative;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public $cache_expiration_time = 60;

    public function index(Request $request)
    {
        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                'first_name',
                'last_name',
                'username',
                'email',
                'role',
                'status',
                'is_visible',
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

            $str = Str::uuid();
            if (in_array($user->role, ['agency'])) {
                $agency = new Agency();
                $agency->uuid = $str;
                $agency->user_id = $user->id;
                $agency->name = $request->agency_name;
                $agency->save();

                // if()
                Link::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'label' => 'linkedin',
                    'url' => $request->linkedin_profile ?? '',
                ]);

            } elseif (in_array($user->role, ['creative'])) {
                $creative = new Creative();
                $creative->uuid = $str;
                $creative->user_id = $user->id;
                $creative->save();

                Link::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'label' => 'portfolio',
                    'url' => $request->linkedin_profile ?? '',
                ]);
            }

            $admin = User::find(1);
            SendEmailJob::dispatch([
                'receiver' => $admin, 'data' => $user,
            ], 'new_user_registration');

            return new UserResource($user);
        } catch (\Exception $e) {
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
                SendEmailJob::dispatch([
                    'receiver' => $user, 'data' => $user,
                ], 'account_approved');
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

        return $username;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'The provided email does not correspond to a registered user. Please check your email or register for an account.'], 404);
        }

        $custom_wp_hasher = new PasswordHash(8, true);

        if (! $custom_wp_hasher->CheckPassword($request->password, $user->password)) { //$plain_password, $password_hashed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->status != 'active') {
            return response()->json(['message' => 'Account not approved'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
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
            'user' => new UserResource($user),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function get_users_for_posts()
    {
        $cacheKey = 'all_users_with_posts';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::select('id', 'uuid', 'first_name', 'last_name', 'role', 'is_visible')->where('role', '!=', 1)->withCount('posts')->get();
        });

        return $users;
    }

    public function get_users_for_attachments()
    {
        $cacheKey = 'all_users_with_attachments';
        $users = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return User::select('id', 'uuid', 'first_name', 'last_name', 'role', 'is_visible')
                ->where('role', '!=', 1)
                ->whereHas('attachments')
                ->withCount('attachments')
                ->orderByDesc('attachments_count') // Order by first name
                ->get();
        });

        return $users;
    }
}