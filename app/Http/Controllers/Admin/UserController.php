<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Api\V1\PasswordHash;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAdminUserRequest;
use App\Http\Resources\User\UserResource;
use App\Jobs\ProcessPortfolioVisuals;
use App\Jobs\SendEmailJob;
use App\Models\Agency;
use App\Models\Attachment;
use App\Models\Creative;
use App\Models\JobAlert;
use App\Models\Link;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.users.index');
    }

    public function create()
    {
        return view('pages.users.add');
    }

    public function createCreative()
    {
        return view('pages.users.add_creative');
    }

    public function createAgency()
    {
        return view('pages.users.add_agency');
    }

    public function details(Request $request, $user_id)
    {
        if ($request?->show == 'deleted') {
            $user = User::onlyTrashed()->where('id', '=', $user_id)->first();
        } else {
            $user = User::where('id', '=', $user_id)->first();
        }

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $str = Str::uuid();
        if (in_array($user->role, ['agency', 'advisor', 'recruiter'])) {
            if (!$user->agency && !($request?->show == 'deleted')) {
                $agency = new Agency();
                $agency->uuid = $str;
                $agency->user_id = $user->id;
                $agency->save();
            }
            $user->load(['agency', 'links', 'addresses.city', 'addresses.state', 'agency_logo', 'latest_subscription']);
            $subscription = Subscription::where('user_id', $user->id)->latest();
        } elseif ($user->role == 'creative' && !($request?->show == 'deleted')) {
            if (!$user->creative) {
                $creative = new Creative();
                $creative->uuid = $str;
                $creative->user_id = $user->id;
                $creative->save();
            }

            $user->load(['creative', 'phones', 'links', 'addresses.city', 'addresses.state', 'profile_picture', 'educations', 'experiences', 'portfolio_spotlights', 'portfolio_items']);
        }

        // dump($user->addresses[0]);
        // dd($user->toArray());

        return view('pages.users.profile', compact('user'));
    }

    public function details_deleted(Request $request, $user_id)
    {
        $user = User::onlyTrashed()->where('id', '=', $user_id)->first();

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $str = Str::uuid();
        if (in_array($user->role, ['agency', 'advisor', 'recruiter'])) {
            $user->load(['agency', 'links', 'addresses.city', 'addresses.state', 'agency_logo', 'latest_subscription']);
            $subscription = Subscription::where('user_id', $user->id)->latest();
        } elseif ($user->role == 'creative' && !($request?->show == 'deleted')) {
            $user->load(['creative', 'phones', 'links', 'addresses.city', 'addresses.state', 'profile_picture', 'educations', 'experiences', 'portfolio_spotlights', 'portfolio_items']);
        }

        return view('pages.users.profile', compact('user'));
    }

    public function store(StoreAdminUserRequest $request)
    {
        try {
            $user = new User();
            $user->uuid = Str::uuid();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            if (empty($request->username)) {
                if ($request->role == 'agency') {
                    $user->username = $this->get_agency_username($request->agency_name, $request->first_name);
                } else {
                    $user->username = $this->get_username_from_email($request->email);
                }
            } else {
                $user->username = $request->username;
            }
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role = $request->role;
            $user->status = $request->status;
            $user->save();

            $role = Role::findByName($request->role);
            $user->assignRole($role);

            if (in_array($user->role, ['agency', 'advisor', 'recruiter'])) {
                $agency = new Agency();
                $agency->uuid = Str::uuid();
                $agency->user_id = $user->id;
                $agency->name = $request->agency_name ?? 'Default Agency';
                $agency->size = '10';
                $agency->about = '';

                $user->username = $this->get_agency_username($user, $agency);

                $agency->save();

                Link::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'label' => 'linkedin',
                    'url' => $request->linkedin_profile ?? '',
                ]);
            } elseif (in_array($user->role, ['creative'])) {
                $creative = new Creative();
                $creative->uuid = Str::uuid();
                $creative->user_id = $user->id;
                $creative->title = 'Default Creative';
                $creative->years_of_experience = 'Junior 0-2 years';
                $creative->about = '';
                $creative->employment_type = 'Full-Time';
                $creative->save();
            }

            return new UserResource($user);
        } catch (\Exception $e) {

            throw new ApiException($e, 'US-01');
        }
    }

    public function updatePassword(Request $request)
    {
        if (!auth()->user()->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = $request->input('user_id');
        $custom_wp_hasher = new PasswordHash(8, true);

        $hashed = $custom_wp_hasher->HashPassword(trim($request->input('password')));
        User::find($userId)->update([
            'password' => $hashed,
        ]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function get_username_from_email($email)
    {
        $baseUsername = Str::before($email, '@');
        $baseUsername = Str::slug($baseUsername);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    public function get_agency_username($agency_name, $contact_first_name)
    {

        $proposed_name = $agency_name;
        $proposed_slug = Str::slug($proposed_name);
        $username = $proposed_slug; // check if only agency name is unique
        $user = User::withTrashed()->where('username', $username)->first();

        if ($user) {
            $proposed_name = $agency_name . '-' . $contact_first_name;
            $proposed_slug = Str::slug($proposed_name);
            $username = $proposed_slug; // check if agency name and contact first name is unquie
            $user = User::withTrashed()->where('username', $username)->first();

            $slug_postfix = 1;
            while ($user) {
                $username = $proposed_slug . '-' . $slug_postfix;
                $user = User::withTrashed()->where('username', $username)->first();
                $slug_postfix++;
            }
        }

        return $username;
    }

    public function impersonate(User $user)
    {
        $token = $user->createToken('impersonation_token')->plainTextToken;
        $url = sprintf('Location: %s/%s', env('FRONTEND_IMPERSONATE_URL'), $token);
        header($url);
        exit();
    }

    public function advisor_impersonate($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $token = $user->createToken('impersonation_token')->plainTextToken;
        $url = sprintf('Location: %s/%s?role=advisor', env('FRONTEND_IMPERSONATE_URL'), $token);
        header($url);
        exit();
    }

    public function update_profile_picture(Request $request, $id)
    {
        if (!auth()->user()->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($id);

        if ($request->has('file') && is_object($request->file)) {

            //Delete Previous profile picture
            if ($user->attachments->where('resource_type', 'profile_picture')->count()) {
                Attachment::where('user_id', $id)->where('resource_type', 'profile_picture')->delete();
            }
            storeImage($request, $id, 'profile_picture');
        }

        Session::flash('success', 'Profile picture updated successfully');

        return redirect()->back();
    }

    public function activate($uuid)
    {
        $user = User::where('uuid', $uuid)->first();

        if ($user) {
            $user->status = 'active';
            $user->approved_at = now();

            $user->save();

            if ($user->role == 'agency') {
                SendEmailJob::dispatch([
                    'receiver' => $user,
                    'data' => $user,
                ], 'account_approved_agency');
            }

            /**
             * Generate portfolio website preview
             */
            if ($user->role == 'creative') {

                SendEmailJob::dispatch([
                    'receiver' => $user,
                    'data' => $user,
                ], 'account_approved');

                $portfolio_website = $user->portfolio_website_link()->first();
                if ($portfolio_website) {
                    Attachment::where('user_id', $user->id)->where('resource_type', 'website_preview')->delete();
                    ProcessPortfolioVisuals::dispatch($user->id, $portfolio_website->url);
                }
            }

            return redirect()->route('users.index');
        }
    }

    public function deactivate($uuid)
    {
        $user = User::where('uuid', $uuid)->first();

        if ($user) {
            $user->status = 'inactive';
            $user->save();

            SendEmailJob::dispatch([
                'receiver' => $user,
                'data' => $user,
            ], 'account_denied');

            return redirect()->route('users.index');
        }
    }

    public function deleteRelatedRecordsPermanently($user_id)
    {
        $tables = DB::select('SHOW TABLES');
        $db = "Tables_in_" . env('DB_DATABASE');

        foreach ($tables as $table) {

            $table = $table->$db;

            try {
                $sql = "DELETE FROM {$table} WHERE user_id = ? OR deleted_at IS NOT NULL";
                DB::statement($sql, [$user_id]);
            } catch (\Exception $e) {
                continue;
            }
        }

        $sql = "DELETE FROM users WHERE id = ? OR deleted_at IS NOT NULL";
        DB::statement($sql, [$user_id]);

        Session::flash('success', 'Profile picture updated successfully');

        return redirect()->route('users.index');
    }
}
