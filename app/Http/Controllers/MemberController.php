<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Exports\MembersTemplateExport;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Imports\MembersImport;
use App\Models\ActivityLog;
use App\Models\Division;
use App\Models\Instrument;
use App\Models\Member;
use App\Models\OrganizationalPeriod;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberController extends Controller
{
    /**
     * Display a listing of members with search and filter.
     */
    public function index(Request $request): View
    {
        $query = Member::with(['division', 'instrument', 'position', 'user'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('full_name', 'like', '%'.$request->search.'%')
                        ->orWhere('member_code', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->division_id, function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            })
            ->when($request->instrument_id, function ($q) use ($request) {
                $q->where('instrument_id', $request->instrument_id);
            })
            ->when($request->class, function ($q) use ($request) {
                $q->where('class', 'like', '%'.$request->class.'%');
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('full_name');

        $members = $query->paginate(15)->withQueryString();

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $inactiveMembers = Member::where('status', 'inactive')->count();
        $alumniMembers = Member::where('status', 'alumni')->count();

        $divisions = Division::orderBy('name')->get();
        $instruments = Instrument::orderBy('name')->get();

        return view('members.index', compact(
            'members',
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'alumniMembers',
            'divisions',
            'instruments'
        ));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create(): View
    {
        $divisions = Division::orderBy('name')->get();
        $instruments = Instrument::with('division')->orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $periods = OrganizationalPeriod::orderBy('start_date', 'desc')->get();

        return view('members.create', compact('divisions', 'instruments', 'positions', 'periods'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $memberCode = $this->generateMemberCode();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('members/photos', 'public');
        }

        $member = Member::create([
            'member_code' => $memberCode,
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'class' => $request->class,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'enrollment_date' => $request->enrollment_date,
            'division_id' => $request->division_id,
            'instrument_id' => $request->instrument_id,
            'position_id' => $request->position_id,
            'organizational_period_id' => $request->organizational_period_id,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'description' => "Menambahkan anggota baru: {$member->full_name} ({$member->member_code})",
            'subject_type' => Member::class,
            'subject_id' => $member->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('members.index')
            ->with('success', "Anggota {$member->full_name} berhasil ditambahkan dengan kode {$member->member_code}.");
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member): View
    {
        $member->load([
            'division',
            'instrument',
            'position',
            'organizationalPeriod',
            'user',
            'attendances.attendanceSession.practiceSchedule',
            'permissionRequests.practiceSchedule',
        ]);

        $attendances = $member->attendances()
            ->with('attendanceSession.practiceSchedule')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'attendance_page');

        $permissionRequests = $member->permissionRequests()
            ->with('practiceSchedule')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'permission_page');

        $attendanceStats = [
            'present' => $member->attendances()->where('status', 'present')->count(),
            'late' => $member->attendances()->where('status', 'late')->count(),
            'permission' => $member->attendances()->where('status', 'permission')->count(),
            'sick' => $member->attendances()->where('status', 'sick')->count(),
            'absent' => $member->attendances()->where('status', 'absent')->count(),
            'total' => $member->attendances()->count(),
        ];

        return view('members.show', compact('member', 'attendances', 'permissionRequests', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member): View
    {
        $member->load(['division', 'instrument', 'position', 'organizationalPeriod']);

        $divisions = Division::orderBy('name')->get();
        $instruments = Instrument::with('division')->orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $periods = OrganizationalPeriod::orderBy('start_date', 'desc')->get();

        return view('members.edit', compact('member', 'divisions', 'instruments', 'positions', 'periods'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $photoPath = $member->photo;

        if ($request->hasFile('photo')) {
            $oldPaths = array_unique(array_filter([
                $member->photo,
                $member->user?->photo,
            ]));

            $photoPath = $request->file('photo')->store('members/photos', 'public');

            foreach ($oldPaths as $oldPath) {
                if ($oldPath !== $photoPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $member->update([
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'class' => $request->class,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'enrollment_date' => $request->enrollment_date,
            'division_id' => $request->division_id,
            'instrument_id' => $request->instrument_id,
            'position_id' => $request->position_id,
            'organizational_period_id' => $request->organizational_period_id,
            'status' => $request->status,
            'user_id' => $request->has('user_id') ? $request->user_id : $member->user_id,
        ]);

        if ($member->user) {
            $userSync = ['phone' => $request->phone];
            if ($request->hasFile('photo')) {
                $userSync['photo'] = $photoPath;
            }
            $member->user->update($userSync);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'description' => "Memperbarui data anggota: {$member->full_name} ({$member->member_code})",
            'subject_type' => Member::class,
            'subject_id' => $member->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', "Data anggota {$member->full_name} berhasil diperbarui.");
    }

    /**
     * Remove the specified member from storage (soft delete).
     */
    public function destroy(Request $request, Member $member): RedirectResponse
    {
        $memberName = $member->full_name;
        $memberCode = $member->member_code;

        $member->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'description' => "Menghapus anggota: {$memberName} ({$memberCode})",
            'subject_type' => Member::class,
            'subject_id' => $member->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('members.index')
            ->with('success', "Anggota {$memberName} berhasil dihapus.");
    }

    /**
     * Export members to Excel.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'division_id', 'status', 'class']);
        $filename = 'data-anggota-'.now()->format('Y-m-d').'.xlsx';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export',
            'description' => 'Export data anggota ke Excel',
            'ip_address' => $request->ip(),
        ]);

        return Excel::download(new MembersExport($filters), $filename);
    }

    /**
     * Export members to PDF.
     */
    public function exportPdf(Request $request): Response
    {
        $query = Member::with(['division', 'instrument', 'position', 'user'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('full_name', 'like', '%'.$request->search.'%')
                        ->orWhere('member_code', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->division_id, function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->class, function ($q) use ($request) {
                $q->where('class', 'like', '%'.$request->class.'%');
            })
            ->orderBy('full_name');

        $members = $query->get();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export',
            'description' => 'Export data anggota ke PDF',
            'ip_address' => $request->ip(),
        ]);

        $pdf = Pdf::loadView('members.export-pdf', compact('members'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('data-anggota-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Download the Excel/CSV template for importing members.
     */
    public function downloadTemplate(Request $request): BinaryFileResponse
    {
        $format = $request->query('format', 'xlsx');
        $fileName = 'template-import-anggota-symphony-'.now()->format('Y-m-d');

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'download_template',
            'description' => 'Download template import data anggota',
            'ip_address' => $request->ip(),
        ]);

        if ($format === 'csv') {
            return Excel::download(
                new MembersTemplateExport,
                $fileName.'.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        return Excel::download(
            new MembersTemplateExport,
            $fileName.'.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Import members from Excel.
     */
    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ], [
            'file.required' => 'Silakan pilih file spreadsheet (Excel / CSV) terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file tidak boleh melebihi 5 MB.',
        ]);

        try {
            $import = new MembersImport;
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $errors = $import->getErrors();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'import',
                'description' => "Import {$imported} data anggota dari file spreadsheet",
                'ip_address' => $request->ip(),
            ]);

            if ($imported === 0 && ! empty($errors)) {
                return redirect()->route('members.index')
                    ->with('error', 'Gagal mengimpor anggota: '.implode('; ', array_slice($errors, 0, 3)));
            }

            $message = "Berhasil mengimpor {$imported} data anggota ke sistem Symphony.";
            if (! empty($errors)) {
                $message .= ' Catatan ('.count($errors).' baris dilewati): '.implode('; ', array_slice($errors, 0, 2));
            }

            return redirect()->route('members.index')->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->route('members.index')
                ->with('error', 'Terjadi kesalahan saat membaca file spreadsheet: '.$e->getMessage());
        }
    }

    /**
     * Generate a unique member code in SMB-YEAR-NNN format.
     */
    private function generateMemberCode(): string
    {
        $year = now()->year;
        $count = Member::withTrashed()->whereYear('created_at', $year)->count() + 1;

        do {
            $code = sprintf('SMB-%d-%03d', $year, $count);
            $exists = Member::withTrashed()->where('member_code', $code)->exists();
            if ($exists) {
                $count++;
            }
        } while ($exists);

        return $code;
    }
}
