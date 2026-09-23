<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response(['data' => User::with('role')
            ->select(['id', 'name', 'email', 'role_id'])
            ->orderBy('name')
            ->get()]);
    }

    public function show(User $user)
    {
        return response(['data' => $user->load('role')]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|max:255',
            'role_id' => 'required|'.Rule::in(['1', '2', '3', '4']),
        ]);

        $request['password'] = Hash::make($request->password);
        $user = User::create($request->all());

        return response(['data' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response(['data' => $user->load('role')]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response(['message' => 'User deleted']);
    }
}
