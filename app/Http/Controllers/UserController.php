<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get available roles based on current user's role.
     * Users can only create/edit users with lower privilege.
     */
    private function getAvailableRoles()
    {
        $currentUser = auth()->user();
        
        // Roles with lower or equal privilege (higher or equal level number)
        $currentLevel = $currentUser->getRoleLevel();
        
        return \App\Models\Role::all()->filter(function ($role) use ($currentLevel) {
            $roleLevel = match($role->name) {
                'superuser' => 1,
                'admin' => 2,
                'manajer' => 3,
                'staff' => 4,
                'kasir' => 5,
                default => 6, // Custom roles are level 6
            };
            return $roleLevel >= $currentLevel;
        });
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->getAvailableRoles();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('users.create', compact('roles', 'cabang'));
    }

    public function store(Request $request)
    {
        $availableRoleIds = $this->getAvailableRoles()->pluck('id')->toArray();
        
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id|in:' . implode(',', $availableRoleIds),
            'jabatan' => 'nullable|string',
            'id_cabang' => 'nullable|exists:cabang,id',
        ]);

        $role = \App\Models\Role::find($request->role_id);

        User::create([
            'nama_user' => $request->nama_user,
            'password_hash' => Hash::make($request->password),
            'role_id' => $role->id,
            'role' => $role->name, // Keep legacy string for compatibility
            'jabatan' => $request->jabatan ?? '',
            'id_cabang' => $request->id_cabang,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Check if current user can edit this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }
        
        $roles = $this->getAvailableRoles();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        
        // Include current user's role in options if not already there
        $userRole = null;
        if ($user->role_id) {
            $userRole = $user->role_relation;
        } elseif ($user->role) {
            $userRole = \App\Models\Role::where('name', $user->role)->first();
        }

        if ($userRole && !$roles->contains('id', $userRole->id)) {
            $roles->push($userRole);
        }
        
        return view('users.edit', compact('user', 'roles', 'cabang'));
    }

    public function update(Request $request, User $user)
    {
        // Check if current user can edit this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }
        
        $availableRoles = $this->getAvailableRoles();
        $availableRoleIds = $availableRoles->pluck('id')->toArray();
        
        // Include current role as valid option
        if ($user->role_id && !in_array($user->role_id, $availableRoleIds)) {
            $availableRoleIds[] = $user->role_id;
        }
        
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id|in:' . implode(',', $availableRoleIds),
            'jabatan' => 'nullable|string',
            'id_cabang' => 'nullable|exists:cabang,id',
        ]);

        $role = \App\Models\Role::find($request->role_id);

        $data = [
            'nama_user' => $request->nama_user,
            'role_id' => $role->id,
            'role' => $role->name, // Keep legacy string for compatibility
            'jabatan' => $request->jabatan ?? '',
            'id_cabang' => $request->id_cabang,
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cannot delete own account
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        
        // Check if current user can edit (and thus delete) this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus user ini.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user ke password default.
     */
    public function resetPassword(User $user)
    {
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk reset password user ini.');
        }

        $defaultPassword = 'password';
        $user->update([
            'password_hash' => Hash::make($defaultPassword),
        ]);

        return back()->with('success', "Password {$user->nama_user} berhasil direset ke '{$defaultPassword}'. Minta user segera mengganti password.");
    }
}

