<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions with instruments and member counts.
     */
    public function index(): View
    {
        $divisions = Division::withCount(['instruments', 'members'])->get();

        return view('divisions.index', compact('divisions'));
    }

    /**
     * Show form for creating a new division.
     */
    public function create(): View
    {
        return view('divisions.create');
    }

    /**
     * Store a newly created division in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:divisions,name'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $division = Division::create($validated);

        ActivityLog::log([
            'action' => 'create_division',
            'description' => "Menambahkan divisi baru: {$division->name}",
            'subject_type' => Division::class,
            'subject_id' => $division->id,
        ]);

        return redirect()->route('divisions.index')->with('success', "Divisi {$division->name} berhasil ditambahkan.");
    }

    /**
     * Display the specified division.
     */
    public function show(Division $division): View
    {
        $division->load(['instruments.members', 'members.instrument', 'members.position']);

        return view('divisions.show', compact('division'));
    }

    /**
     * Show form for editing division.
     */
    public function edit(Division $division): View
    {
        return view('divisions.edit', compact('division'));
    }

    /**
     * Update the division.
     */
    public function update(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:divisions,name,'.$division->id],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $division->update($validated);

        ActivityLog::log([
            'action' => 'update_division',
            'description' => "Memperbarui divisi: {$division->name}",
            'subject_type' => Division::class,
            'subject_id' => $division->id,
        ]);

        return redirect()->route('divisions.index')->with('success', "Divisi {$division->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified division.
     */
    public function destroy(Division $division): RedirectResponse
    {
        if ($division->members()->where('status', 'active')->count() > 0) {
            return back()->with('error', "Divisi {$division->name} tidak dapat dihapus karena masih memiliki anggota aktif.");
        }

        $name = $division->name;
        $division->delete();

        ActivityLog::log([
            'action' => 'delete_division',
            'description' => "Menghapus divisi: {$name}",
        ]);

        return redirect()->route('divisions.index')->with('success', "Divisi {$name} berhasil dihapus.");
    }
}
