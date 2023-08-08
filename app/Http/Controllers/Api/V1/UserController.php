<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate(10);

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
        $user->user_role = $request->role;
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
        $user = User::where('uuid', $uuid)->first();
        if (! $user) {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if (! $user) {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        foreach ($data as $key => $value) {
            // if($key == 'role') $key = 'user_role';
            // if($key == 'status') $key = 'user_status';
            $user->$key = $value;
        }
        $user_updated = $user->save();

        if ($user_updated) {
            $user->fresh();

            return response()->json([
                'message' => 'User updated successfully.',
                'data' => new UserResource($user),
            ], Response::HTTP_OK);
        }
    }

    public function destroy($uuid)
    {
        $deleted = User::where('uuid', $uuid)->delete();
        if ($deleted) {
            return response()->json([
                'message' => 'User deleted successfully.',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'No record found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function get_username_from_email($email)
    {
        $username = explode('@', $email)[0];
        $username = str_replace(['.', '-', '+'], '_', $username);
        $username = trim($username, '_');

        return $username;
    }
}
