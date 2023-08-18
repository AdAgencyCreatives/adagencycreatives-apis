<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        return view('pages.users.creative.detail', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        if (!auth()->user()->role == 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = $request->input('user_id');

        User::find($userId)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
}
