<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate(config('ad-agency-creatives.request.pagination_limit'));

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
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if (! $user) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        $user_updated = $user->update($request->all());
        if ($user_updated) {
            return ApiResponse::success($user, 200);
        }
    }

    public function destroy($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $user->delete();

            return ApiResponse::success($user, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function get_username_from_email($email)
    {
        $username = Str::before($email, '@');
        $username = Str::slug($username);

        return $username;
    }
}
