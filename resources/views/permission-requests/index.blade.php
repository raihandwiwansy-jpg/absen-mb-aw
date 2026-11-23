@extends('layouts.app')

@section('title', 'Permohonan Izin & Sakit')
@section('breadcrumb', 'Pengajuan Dispensasi Kehadiran')

@section('content')
<div class="space-y-6" x-data="{
    rows: {
        @foreach($requests as $req)
        {{ $req->id }}: {
            status: '{{ $req->status }}',
            status_label: '{{ $req->status_label }}',
            isProcessing: false,
        },
        @endforeach
    },

    quickApprove(id, url) {
        const row = this.rows[id];
        if (!row || row.isProcessing) return;
        row.isProcessing = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            row.isProcessing = false;
            if (data.success) {
                row.status = 'approved';
                row.status_label = 'Disetujui';
                window.playAudioFeedback('success');
                window.showToast(data.message, 'success');
            } else {
                window.showToast(data.message || 'Gagal menyetujui', 'error');
            }
        })
        .catch(() => {
            row.isProcessing = false;
            window.showToast('Gagal menghubungi server.', 'error');
        });
    },

    quickReject(id, url) {
        if (!confirm('Apakah Anda yakin ingin menolak permohonan ini?')) return;
        const row = this.rows[id];
        if (!row || row.isProcessing) return;
        row.isProcessing = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            row.isProcessing = false;
            if (data.success) {
                row.status = 'rejected';
                row.status_label = 'Ditolak';
                window.playAudioFeedback('error');
                window.showToast(data.message, 'warning');
            } else {
                window.showToast(data.message || 'Gagal menolak', 'error');
            }
        })
        .catch(() => {
            row.isProcessing = false;
            window.showToast('Gagal menghubungi server.', 'error');
        });
    }
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#101D3B]">Permohonan Izin & Sakit</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar pengajuan dispensasi ketidakhadiran latihan anggota Symphony</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('permission-requests.create') }}" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Izin / Sakit
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('permission-requests.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                <option value="">Semua Status Approval</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <select name="type" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                <option value="">Semua Tipe</option>
                <option value="permission" {{ request('type') === 'permission' ? 'selected' : '' }}>Izin</option>
                <option value="sick" {{ request('type') === 'sick' ? 'selected' : '' }}>Sakit</option>
            </select>

            @if(request()->hasAny(['status', 'type']))
            <a href="{{ route('permission-requests.index') }}" class="text-xs text-gray-500 hover:text-red-600 underline">Reset Filter</a>
            @endif
        </form>

        <span class="text-xs font-bold text-gray-500">
            Total {{ $requests->total() }} Pengajuan
        </span>
    </div>

    {{-- Requests Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[720px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tanggal Pengajuan</th>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Kegiatan Latihan</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Alasan</th>
                        <th class="p-4">Lampiran</th>
                        <th class="p-4">Status Approval</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($requests as $req)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <td class="p-4 whitespace-nowrap font-mono text-gray-500">
                            {{ $req->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-[#101D3B]">{{ $req->member->full_name }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $req->member->member_code }} • {{ $req->member->division->name ?? '' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-900">{{ $req->practiceSchedule->title ?? '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $req->practiceSchedule->date->format('d/m/Y') ?? '' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $req->type === 'sick' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $req->type_label }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-600 max-w-xs truncate" title="{{ $req->reason }}">
                            {{ $req->reason }}
                        </td>
                        <td class="p-4">
                            @if($req->attachment)
                                <a href="{{ asset('storage/' . $req->attachment) }}" target="_blank" class="text-xs font-bold text-[#C6A044] hover:underline flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Lihat Berkas
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-4">
                            {{-- Dynamic Instant Status Badge --}}
                            <span :class="{
                                'bg-emerald-100 text-emerald-800': rows[{{ $req->id }}].status === 'approved',
                                'bg-red-100 text-red-800': rows[{{ $req->id }}].status === 'rejected',
                                'bg-amber-100 text-amber-800': rows[{{ $req->id }}].status === 'pending'
                            }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase transition-all duration-300">
                                <span x-text="rows[{{ $req->id }}].status_label"></span>
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                @can('verify-permission')
                                    <template x-if="rows[{{ $req->id }}].status === 'pending'">
                                        <div class="flex items-center gap-1">
                                            {{-- Instant Approve --}}
                                            <button type="button"
                                                    @click="quickApprove({{ $req->id }}, '{{ route('permission-requests.approve', $req) }}')"
                                                    :disabled="rows[{{ $req->id }}].isProcessing"
                                                    class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-xl transition"
                                                    title="Setujui Izin Secara Instan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>

                                            {{-- Instant Reject --}}
                                            <button type="button"
                                                    @click="quickReject({{ $req->id }}, '{{ route('permission-requests.reject', $req) }}')"
                                                    :disabled="rows[{{ $req->id }}].isProcessing"
                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-xl transition"
                                                    title="Tolak Izin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                @endcan

                                <a href="{{ route('permission-requests.show', $req) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-navy bg-gray-100 hover:bg-gray-200 transition">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">Belum ada pengajuan izin atau sakit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
