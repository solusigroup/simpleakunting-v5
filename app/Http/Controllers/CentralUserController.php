<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CentralUserController extends Controller
{
    /**
     * List all central users.
     */
    public function index()
    {
        $users = CentralUser::orderBy('created_at', 'desc')->get();
        return view('central.admin.users', compact('users'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        return view('central.admin.users-create');
    }

    /**
     * Store a new central user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => ['required', 'string', 'max:255', 'unique:central_users,nama_user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'jabatan' => ['nullable', 'string', 'max:255'],
        ]);

        CentralUser::create([
            'nama_user' => $validated['nama_user'],
            'password_hash' => Hash::make($validated['password']),
            'role' => 'superuser',
            'jabatan' => $validated['jabatan'] ?? 'Central Administrator',
        ]);

        return redirect()->route('central.users.index')
            ->with('success', "User '{$validated['nama_user']}' berhasil dibuat.");
    }

    /**
     * Show change password form.
     */
    public function editPassword()
    {
        return view('central.admin.password');
    }

    /**
     * Update own password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::guard('central')->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->password_hash = Hash::make($request->password);
        $user->save();

        return redirect()->route('central.users.index')
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * Delete a central user.
     */
    public function destroy(Request $request, $id)
    {
        $user = CentralUser::findOrFail($id);
        $currentUser = Auth::guard('central')->user();

        // Cannot delete yourself
        if ($user->id_user === $currentUser->id_user) {
            return redirect()->route('central.users.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('central.users.index')
            ->with('success', "User '{$user->nama_user}' berhasil dihapus.");
    }
}
