<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);
        $roles = ['admin', 'manager', 'reception', 'housekeeping', 'finance', 'inventory', 'maintenance'];

        return view('admin.roles', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate(['role' => 'required|in:admin,manager,reception,housekeeping,finance,inventory,maintenance']);

        if ($user->is(auth()->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own administrator access.']);
        }

        $user->update(['role' => $validated['role'], 'usertype' => $validated['role'] === 'admin' ? '1' : '0']);

        return back()->with('message', 'User role updated successfully.');
    }
}