<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Show Add User form (Admin only)
    public function create()
    {
        return view('admin.create-user');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'rfid_uid' => 'nullable|unique:users,rfid_uid',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'active' => 1,
            'is_admin' => $request->has('is_admin'),
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->back()->with('success', 'User created successfully!');
    }
}
