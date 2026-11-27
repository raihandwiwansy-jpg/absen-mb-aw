<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of system users.
     */
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'member'])->orderBy('name');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->role) {
            $query->role($request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();
        $unlinkedMembers = Member::whereNull('user_id')->get();

        return view('users.create', compact('roles', 'unlinkedMembers'));
    }

    /**
     * Store new user and assign role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'member_id' => ['nullable', 'exists:members,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        if (! empty($validated['member_id'])) {
            $member = Member::find($validated['member_id']);
            $member->update(['user_id' => $user->id]);
        }

        ActivityLog::log([
            'action' => 'create_user',
            'description' => "Membuat user baru: {$user->name} ({$user->email}) dengan role {$validated['role']}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Show form for editing user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $unlinkedMembers = Member::whereNull('user_id')->orWhere('user_id', $user->id)->get();

        return view('users.edit', compact('user', 'roles', 'unlinkedMembers'));
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'member_id' => ['nullable', 'exists:members,id'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
            $userData['plain_password'] = $validated['password'];
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        // Unlink old member if changed
        if ($user->member && $user->member->id != $validated['member_id']) {
            $user->member->update(['user_id' => null]);
        }

        if (! empty($validated['member_id'])) {
            Member::where('id', $validated['member_id'])->update(['user_id' => $user->id]);
        }

        ActivityLog::log([
            'action' => 'update_user',
            'description' => "Memperbarui data pengguna: {$user->name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;

        // Unlink member if any
        if ($user->member) {
            $user->member->update(['user_id' => null]);
        }

        $user->delete();

        ActivityLog::log([
            'action' => 'delete_user',
            'description' => "Menghapus akun pengguna: {$name}",
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}
