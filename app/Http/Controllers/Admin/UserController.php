<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Api\V1\PasswordHash;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAdminUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Agency;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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

    public function details(User $user)
    {
        $str = Str::uuid();
        if (in_array($user->role, ['agency', 'advisor'])) {
            if( !$user->agency ){
                $agency = new Agency();
                $agency->uuid = $str;
                $agency->user_id = $user->id;
                $agency->save();
            }
            $user->load(['agency', 'links', 'addresses.city', 'addresses.state',  'attachments' => function ($query) use ($user) {
                $query->where('resource_id', $user->agency->id)
                    ->latest()->take(1);
            }]);
        } elseif ($user->role == 'creative') {
            if( !$user->creative ){
                $creative = new Creative();
                $creative->uuid = $str;
                $creative->user_id = $user->id;
                $creative->save();
            }

            $user->load(['creative', 'phones', 'links', 'addresses.city', 'addresses.state', 'profile_picture', 'educations', 'experiences', 'portfolio_spotlights', 'portfolio_items']);
        }

        // dump($user->addresses[0]->state->name);
        // dd($user->toArray());

        return view('pages.users.profile', compact('user'));
    }

    public function store(StoreAdminUserRequest $request)
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
            $user->status = $request->status;
            $user->save();

            $role = Role::findByName($request->role);
            $user->assignRole($role);

            if (in_array($user->role, ['advisor', 'agency'])) {
                $agency = new Agency();
                $agency->uuid = Str::uuid();
                $agency->user_id = $user->id;
                $agency->name = 'Default Agency';
                $agency->size = '10';
                $agency->about = '';
                $agency->save();
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
        if (! auth()->user()->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = $request->input('user_id');
        $custom_wp_hasher = new PasswordHash(8, true);

        $hashed = $custom_wp_hasher->HashPassword( trim($request->input('password')) );
        dump($hashed);
        User::find($userId)->update([
            'password' => $hashed,
        ]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function get_username_from_email($email)
    {
        $username = Str::before($email, '@');
        $username = Str::slug($username);

        return $username;
    }

    public function impersonate(User $user)
    {
        $token = $user->createToken('impersonation_token')->plainTextToken;
        $url = sprintf('Location: %s?token=%s', env('FRONTEND_IMPERSONATE_URL'), $token);
        header($url);
        exit();
    }
}
