<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::paginate(10);
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {

        $user = User::create($request->all());

        $username = explode('@', $email)[0];
        $username = str_replace(['.', '-', '+'], '_', $username);
        $username = trim($username, '_');
    }

    public function show($uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resumes  $resumes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resumes $resumes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resumes  $resumes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resumes $resumes)
    {
        //
    }
}
