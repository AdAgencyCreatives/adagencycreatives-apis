<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAdminUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Agency;
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
        if (in_array($user->role, ['agency', 'advisor'])) {
             $user->load(['agency', 'links', 'attachments' => function ($query) use ($user) {
            $query->where('resource_id', $user->agency->id)
            ->latest()->take(1); 
        }]);
        } elseif ($user->role == 'creative') {
            $user->load( ['creative', 'phones', 'links']);
        }


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

            if($user->role == 'advisor') {
                $agency = new Agency();
                $agency->uuid = Str::uuid();
                $agency->user_id = $user->id;
                $agency->name = 'Default Agency';
                $agency->about = '';
                $agency->size = '10';
                $agency->type_of_work = '';
                $agency->save();
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

        User::find($userId)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function get_username_from_email($email)
    {
        $username = Str::before($email, '@');
        $username = Str::slug($username);

        return $username;
    }
}