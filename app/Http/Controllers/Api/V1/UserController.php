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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public $cache_expiration_time = 60;

    public function index(Request $request)
    {
        $users = Cache::remember('users', $this->cache_expiration_time, function () {
            return User::paginate(config('global.request.pagination_limit'));
        });

        return new UserCollection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $email = fake()->unique()->safeEmail();

        $user = new User();
        $user->uuid = Str::uuid();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $this->get_username_from_email($email);
        $user->email = $email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user_created = $user->save();

        if ($user_created) {
            return response()->json([
                'message' => 'User created successfully.',
                'data' => new UserResource($user),
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
}
