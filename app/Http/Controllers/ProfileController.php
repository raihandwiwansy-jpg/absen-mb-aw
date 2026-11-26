<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show user profile edit form.
     */
    public function edit(): View
    {
        $user = auth()->user()->load('member');
        $member = $user->member;

        return view('profile.edit', compact('user', 'member'));
    }

    /**
     * Update user profile information, photo, phone, email, and password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user()->load('member');
        $member = $user->member;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'file', 'mimetypes:image/*', 'max:51200'],
            'remove_photo' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan akun lain.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'photo.file' => 'Foto profil gagal diunggah, silakan pilih file gambar lagi.',
            'photo.mimetypes' => 'File harus berupa foto atau gambar (JPG, PNG, WEBP, HEIC, GIF, dll).',
            'photo.max' => 'Ukuran file foto terlalu besar (maksimal 50MB).',
            'current_password.required_with' => 'Masukkan kata sandi saat ini untuk mengganti kata sandi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
            $userData['plain_password'] = $validated['password'];
        }

        $memberData = [];
        if ($member) {
            $memberData['phone'] = $validated['phone'] ?? null;
        }

        $oldPaths = array_unique(array_filter([
            $user->photo,
            $member?->photo,
        ]));

        if ($request->hasFile('photo')) {
            $folder = $member ? 'members/photos' : 'users/photos';
            $newPhotoPath = $request->file('photo')->store($folder, 'public');

            foreach ($oldPaths as $oldPath) {
                if ($oldPath !== $newPhotoPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $userData['photo'] = $newPhotoPath;
            if ($member) {
                $memberData['photo'] = $newPhotoPath;
            }
        } elseif ($request->boolean('remove_photo')) {
            foreach ($oldPaths as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $userData['photo'] = null;
            if ($member) {
                $memberData['photo'] = null;
            }
        }

        $user->update($userData);

        if ($member && ! empty($memberData)) {
            $member->update($memberData);
        }

        ActivityLog::log([
            'action' => 'update_profile',
            'description' => "Pengguna {$user->name} memperbarui profil akun",
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
