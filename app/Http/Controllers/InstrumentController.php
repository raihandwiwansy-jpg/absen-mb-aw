<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Division;
use App\Models\Instrument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstrumentController extends Controller
{
    /**
     * Display a listing of instruments.
     */
    public function index(Request $request): View
    {
        $query = Instrument::with('division')->withCount('members');

        if ($request->division_id) {
            $query->where('division_id', $request->division_id);
        }

        $instruments = $query->orderBy('name')->paginate(15)->withQueryString();
        $divisions = Division::all();

        return view('instruments.index', compact('instruments', 'divisions'));
    }

    /**
     * Show form for creating a new instrument.
     */
    public function create(Request $request): View
    {
        $divisions = Division::all();
        $selectedDivisionId = $request->division_id;

        return view('instruments.create', compact('divisions', 'selectedDivisionId'));
    }

    /**
     * Store a newly created instrument in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $instrument = Instrument::create($validated);

        ActivityLog::log([
            'action' => 'create_instrument',
            'description' => "Menambahkan instrumen: {$instrument->name}",
            'subject_type' => Instrument::class,
            'subject_id' => $instrument->id,
        ]);

        return redirect()->route('instruments.index')->with('success', "Instrumen {$instrument->name} berhasil ditambahkan.");
    }

    /**
     * Show form for editing instrument.
     */
    public function edit(Instrument $instrument): View
    {
        $divisions = Division::all();

        return view('instruments.edit', compact('instrument', 'divisions'));
    }

    /**
     * Update instrument.
     */
    public function update(Request $request, Instrument $instrument): RedirectResponse
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $instrument->update($validated);

        ActivityLog::log([
            'action' => 'update_instrument',
            'description' => "Memperbarui instrumen: {$instrument->name}",
            'subject_type' => Instrument::class,
            'subject_id' => $instrument->id,
        ]);

        return redirect()->route('instruments.index')->with('success', "Instrumen {$instrument->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified instrument.
     */
    public function destroy(Instrument $instrument): RedirectResponse
    {
        if ($instrument->members()->where('status', 'active')->count() > 0) {
            return back()->with('error', "Instrumen {$instrument->name} masih digunakan oleh anggota aktif.");
        }

        $name = $instrument->name;
        $instrument->delete();

        ActivityLog::log([
            'action' => 'delete_instrument',
            'description' => "Menghapus instrumen: {$name}",
        ]);

        return redirect()->route('instruments.index')->with('success', "Instrumen {$name} berhasil dihapus.");
    }
}
