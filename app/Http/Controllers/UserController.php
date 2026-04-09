<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function updateOrCreate(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        User::updateOrCreate(
            ['email' => $request->email], // 条件: email で検索
            [
                'name' => $request->name,
                'password' => $request->password ? bcrypt($request->password) : null,
            ]
        );

        return redirect()->route('users.index')->with('success', 'User updated or created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully. User can be restored within 30 days.');
    }

    public function showUpdateOrCreateForm()
    {
        return view('users.update-or-create');
    }

    public function trashed()
    {
        $trashedUsers = User::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(30))
            ->get();
        return view('users.trashed', compact('trashedUsers'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // ตรวจสอบว่าผู้ใช้ถูกลบมาแล้วไม่เกิน 30 วัน
        if ($user->deleted_at >= now()->subDays(30)) {
            $user->restore();
            return redirect()->route('users.trashed')->with('success', 'User restored successfully.');
        }

        return redirect()->route('users.trashed')->with('error', 'User cannot be restored. Restoration period has expired.');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('users.trashed')->with('success', 'User permanently deleted.');
    }

    public function getUsersData()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }
}