<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show user profile edit form.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $member = $user->member;

        return view('profile.edit', compact('user', 'member'));
    }

    /**
     * Update user profile information and password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        ActivityLog::log([
            'action' => 'update_profile',
            'description' => "Pengguna {$user->name} memperbarui profil akun",
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
