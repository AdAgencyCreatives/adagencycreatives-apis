<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ModelNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public $cache_expiration_time = 60;

    public function index(Request $request)
    {
        // $users = Cache::remember('users', $this->cache_expiration_time, function () {
        //     return User::paginate(config('global.request.pagination_limit'));
        // });

        $query = QueryBuilder::for(User::class)
                ->allowedFilters([
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'role',
                    'status',
                    'is_visible',
                ]);

        $users = $query->paginate(config('global.request.pagination_limit'));

        return new UserCollection($users);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $email = fake()->unique()->safeEmail();

            $user = new User();
            $user->uuid = Str::uuid();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $this->get_username_from_email($email);
            $user->email = $email;
            $user->password = bcrypt($request->password);
            $user->role = $request->role;
            $user->save();

            $role = Role::findByName($request->role);
            $user->assignRole($role);

            return new UserResource($user);
        } catch (\Exception $e) {
            throw new ApiException($e, 'US-01');
        }
    }

    public function show($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $user_resource = new UserResource($user);

            return 'html';
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

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->email)->first();

        $agency = Role::findByName('agency');
        // $user->assignRole($agency);
        // $user->givePermissionTo('job.create');

        $token = $user->createToken('auth_token')->plainTextToken;
        $role_name = $user->getRoleNames();

        return response()->json([
            'token' => $token,
            'role' => $role_name,
            'permissions' => $user->getAllPermissions(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
