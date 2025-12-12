{{-- resources/views/dashboard/pimpinanapproval.blade.php --}}
@extends('layouts.app')
@section('title', 'Pimpinan - Approval Pengajuan')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<style>
    .dataTables_filter, 
    .dataTables_length {
        margin: 0 0 20px 0; /* bawah 20px */
    }

    .dataTables_info, 
    .dataTables_paginate {
        margin: 20px 0 0 0; /* atas 20px */
    }

    .dataTables_wrapper .text-center {
        text-align: center !important;
    }

</style>

<div class="min-h-screen bg-gray-50 py-2">
    <div class="max-w-7xl mx-auto px-4">

        <!-- TABEL APPROVAL -->
        <div class="bg-white rounded-xl shadow-2xl border-2 overflow-hidden">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Daftar Pengajuan</h2>

                <div class="rounded-xl overflow-x-auto">
                    <table class="border-2 min-w-full divide-y divide-gray-200" id="tabelValidasi">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-5 text-center text-xs font-bold text-black">No</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Nama</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-black">NRP</th>
                                <th class="px-10 py-5 text-left text-xs font-bold text-black">Jenis</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-black">Tanggal</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-black">Status</th>
                                <th class="px-10 py-5 text-left text-xs font-bold text-black">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pengajuan as $i => $p)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-5 text-sm ">{{ $i + 1 }}</td>
                                <td class="px-6 py-5 text-sm text-black">{{ $p->nama_personel ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm text-black">{{ $p->nrp ?? '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="whitespace-nowrap inline-block px-5 py-2.5 rounded-full text-xs font-bold {{ $p->jenis == 'cuti' ? 'bg-purple-100 text-purple-800' : 'bg-cyan-100 text-cyan-800' }}">
                                        {{ strtoupper($p->nama_jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm whitespace-nowrap text-center">
                                    {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                                    @if($p->tanggal_selesai) → {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }} @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @php $s = $p->status; @endphp
                                    <span class="px-5 py-2.5 rounded-full text-xs font-bold whitespace-nowrap
                                        {{ $s == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($s == 'Ditolak' ? 'bg-red-100 text-red-800' : 
                                           ($s == 'Tervalidasi' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $s == 'Tervalidasi' ? 'Menunggu Persetujuan' : $s }}
                                    </span>
                                </td>

                                <!-- KOLOM AKSI -->
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-2 whitespace-nowrap">
                                        <!-- DETAILS -->
                                        <button onclick='openDetailModal({!! json_encode($p) !!})' 
                                                class="bg-white text-black font-semibold px-5 py-2 rounded-lg shadow text-xs border-2 hover:bg-gray-200">
                                            Details
                                        </button>

                                        @if($p->status == 'Proses')
                                            <!-- VALID
                                            <button onclick="updateStatus({{ $p->id }}, 'Tervalidasi', '{{ $p->jenis }}')" 
                                                    class="bg-gradient-to-r from-emerald-600 to-green-700 text-white font-bold px-5 py-2 rounded-lg shadow text-xs">
                                                Valid
                                            </button> -->

                                            <!-- TOLAK + DROPDOWN - STRUKTUR YANG DIPERBAIKI
                                            <div class="relative inline-block text-left" id="dropdown-container-{{ $p->id }}">
                                                <- Tombol TOLAK utama
                                                <button type="button"
                                                        onclick="toggleDropdown({{ $p->id }}, event)"
                                                        class="w-full bg-gradient-to-r from-red-600 to-rose-700 text-white font-bold px-5 py-2 rounded-lg shadow text-xs flex justify-center items-center gap-1 dropdown-trigger">
                                                    Tolak
                                                    <svg class="w-4 h-4 transition-transform duration-200" 
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                Dropdown Menu
                                                <div id="dropdown-{{ $p->id }}"
                                                    class="hidden absolute left-0 right-0 mt-2 bg-white rounded-lg shadow-2xl border-2 border-gray-300 z-[9999] overflow-hidden dropdown-menu">
                                                    <button type="button" 
                                                            onclick="handleStatusUpdate({{ $p->id }}, 'Ditolak', '{{ $p->jenis }}')"
                                                            class="block w-full text-left px-4 py-3 text-red-700 hover:bg-red-50 font-medium text-sm transition-colors duration-150">
                                                        <i class="fas fa-times-circle mr-1"></i>Ditolak
                                                    </button>
                                                    <button type="button" 
                                                            onclick="handleStatusUpdate({{ $p->id }}, 'Tidak Valid', '{{ $p->jenis }}')"
                                                            class="block w-full text-left px-4 py-3 text-orange-700 hover:bg-orange-50 font-medium text-sm border-t border-gray-200 transition-colors duration-150">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Tidak Valid
                                                    </button>
                                                </div>
                                            </div> -->
                                        @else
                                            <span class="text-xs text-center font-bold text-green-600">Sudah Diproses</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL – VERSI FINAL TERBARU --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-70 hidden flex items-center justify-center z-50 p-4"
     onclick="if(event.target === this) closeDetailModal()">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto">

        <!-- Header + Status Badge di Kanan Atas -->
        <div class="bg-gray-100 px-8 py-5 flex justify-between items-center border-b">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Detail Pengajuan</h3>
                <p class="text-sm text-gray-600">Informasi lengkap izin/cuti personel</p>
            </div>
            <div class="flex items-center gap-6">
                <!-- STATUS BADGE DI HEADER -->
                <div class="text-right">
                    <span id="modalStatusBadge" 
                          class="inline-block px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider border mt-1">
                        Menunggu
                    </span>
                </div>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8 bg-gray-50">

            <!-- 3 Kolom Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm mb-10">
                <!-- Kolom 1 -->
                <div class="space-y-5">
                        <div><span class="font-bold text-black">Nama Lengkap</span></div>
                        <div id="modalNama" class="text-black">-</div>
                        <div><span class="font-bold text-black">NRP</span></div>
                        <div id="modalNrp" class=" text-black">-</div>
                        <div><span class="font-bold text-black">Pangkat</span></div>
                        <div id="modalPangkat" class="text-black">-</div>
                        <div><span class="font-bold text-black">Golongan</span></div>
                        <div id="modalGolongan" class="text-black">-</div>
                </div>

                <!-- Kolom 2 -->
                <div class="space-y-5">
                        <div><span class="font-bold text-black">Jenis</span></div>
                        <div id="modalJenis" class="text-black">-</div>
                        <div><span class="font-bold text-black">Mulai</span></div>
                        <div id="modalTglMulai" class=" text-black">-</div>
                        <div><span class="font-bold text-black">Dari</span></div>
                        <div id="modalPergiDari" class="text-black">-</div>
                        <div><span class="font-bold text-black">Transportasi</span></div>
                        <div id="modalTransportasi" class="text-black">-</div>
                </div>

                <!-- Kolom 3 -->
                <div class="space-y-5">
                        <div><span class="font-bold text-black">Lama</span></div>
                        <div id="modalLama" class="text-gray-700">-</div>
                        <div><span class="font-bold text-black">Selesai</span></div>
                        <div id="modalTglSelesai" class="text-black">-</div>
                        <div><span class="font-bold text-black">Tujuan</span></div>
                        <div id="modalTujuan" class="text-black">-</div>
                        <div><span class="font-bold text-black">Pengikut</span></div>
                        <div id="modalPengikut" class="text-black">-</div>
                </div>
            </div>

            <!-- BUKTI PENDUKUNG + KEPERLUAN (BERDAMPINGAN) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

                <!-- Bukti Pendukung (kiri) -->
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 text-base">Bukti Pendukung</h4>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 hover:shadow-sm transition-shadow">
                        <div class="text-gray-400 flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div id="buktiFileName" class="font-medium text-gray-800 text-sm truncate">
                                Tidak ada bukti
                            </div>
                            <div id="buktiLinkContainer" class="text-xs text-yellow-900 font-medium cursor-pointer hover:underline mt-0.5">
                                Lihat File
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keperluan (kanan) -->
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 text-base">Keperluan</h4>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4 text-gray-800">
                        <p id="modalKeperluan" class="italic leading-relaxed min-h-[40px] text-sm">
                            Tidak ada keterangan
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-sm text-gray-500 text-left sm:text-right w-full sm:w-auto">
                Diajukan pada: <span id="modalCreatedAt" class="font-bold text-yellow-900">-</span>
            </div>

            <!-- Footer: Tombol Aksi di KIRI, Tanggal di KANAN -->
            <div class="mt-12 pt-6 border-t flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div id="aksiFooterContainer" class="flex gap-3">
                    <!-- Diisi oleh JavaScript -->
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDetailModal()"
                            class="px-8 py-3 bg-white text-black font-bold border-1 hover:bg-gray-200 rounded-lg shadow transition">
                        <i class="fas fa-times mr-2"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update route ke route pimpinan
function updateStatus(id, status, tipe) {
    if (!confirm(`Apakah Anda yakin ingin ${status == 'Disetujui' ? 'menyetujui' : 'menolak'} pengajuan ini?`)) return;

    fetch("{{ route('pimpinan.validasi.update') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ id, status, tipe })
    })
    .then(response => response.ok ? response.json() : Promise.reject())
    .then(data => { 
        if (data.success) { 
            alert('Status berhasil diubah menjadi "' + status + '"'); 
            location.reload(); 
        } else {
            alert('Gagal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Terjadi kesalahan jaringan'));
}

function kirimStatus(id, statusBaru, tipe) {
    fetch("{{ route('pimpinan.validasi.update') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ 
            id: id, 
            status: statusBaru,     // ← pastikan 'Disetujui' atau 'Ditolak'
            tipe: tipe 
        })
    })
    .then(r => r.json().then(data => ({ok: r.ok, data})))
    .then(res => {
        if (res.ok && res.data.success) {
            alert('Pengajuan berhasil ' + (statusBaru === 'Disetujui' ? 'disetujui' : 'ditolak'));
            location.reload();
        } else {
            alert('Gagal: ' + (res.data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(() => alert('Terjadi kesalahan jaringan atau server'));
}

// Fungsi lainnya tetap sama
function openBuktiModal(url, filename) {
    document.getElementById('modalTitle').textContent = filename;
    document.getElementById('downloadLink').href = url;
    const frame = document.getElementById('buktiFrame');
    const img = document.getElementById('buktiImage');
    if (url.match(/\.(jpeg|jpg|png|gif|webp)$/i)) {
        img.src = url; img.classList.remove('hidden'); frame.classList.add('hidden');
    } else {
        frame.src = url; frame.classList.remove('hidden'); img.classList.add('hidden');
    }
    document.getElementById('buktiModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBuktiModal() {
    document.getElementById('buktiModal').classList.add('hidden');
    document.getElementById('buktiFrame').src = '';
    document.getElementById('buktiImage').src = '';
    document.body.style.overflow = 'auto';
}

function openDetailModal(data) {
    console.log('Detail modal dibuka:', data.id);

    // Isi data utama
    document.getElementById('modalNama').textContent       = data.nama_personel || '-';
    document.getElementById('modalNrp').textContent        = data.nrp || '-';
    document.getElementById('modalPangkat').textContent    = data.pangkat || '-';
    document.getElementById('modalGolongan').textContent   = data.golongan || '-';
    document.getElementById('modalJenis').textContent       = (data.nama_jenis || data.jenis?.toUpperCase()) || '-';
    document.getElementById('modalKeperluan').textContent   = data.keterangan || 'Tidak ada keterangan';
    document.getElementById('modalTglMulai').textContent    = formatDate(data.tanggal_mulai);
    document.getElementById('modalTglSelesai').textContent  = data.tanggal_selesai ? formatDate(data.tanggal_selesai) : '-';

    const lama = data.tanggal_selesai 
        ? Math.ceil((new Date(data.tanggal_selesai) - new Date(data.tanggal_mulai)) / 86400000) + 1 
        : 1;
    document.getElementById('modalLama').textContent = lama + ' Hari';

    document.getElementById('modalPergiDari').textContent    = data.pergi_dari || '-';
    document.getElementById('modalTujuan').textContent       = data.tujuan || '-';
    document.getElementById('modalTransportasi').textContent = data.transportasi || '-';
    document.getElementById('modalPengikut').textContent     = data.pengikut || '-';
    document.getElementById('modalCreatedAt').textContent    = new Date(data.created_at).toLocaleString('id-ID');

    // === STATUS BADGE DI HEADER (SUDAH DIPERBAIKI) ===
    const statusText = data.status === 'Proses' ? 'Menunggu' : data.status.toUpperCase();
    const badge = document.getElementById('modalStatusBadge');
    badge.textContent = statusText;

    // PERBAIKAN: Hapus baris error "07"!
    badge.className = 'inline-block px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider border';

    if (['Disetujui', 'Tervalidasi'].includes(data.status)) {
        badge.classList.add('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
    } else if (data.status === 'Ditolak') {
        badge.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
    } else if (data.status === 'Tidak Valid') {
        badge.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
    } else if (data.status === 'Proses') {
        badge.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-300');
    } else {
        badge.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
    }

    // Bukti
    const buktiFileName = document.getElementById('buktiFileName');
    const buktiLink = document.getElementById('buktiLinkContainer');
    if (data.pathFile_bukti) {
        const url = `/storage/${data.pathFile_bukti}`;
        buktiFileName.textContent = data.namaFile_bukti || 'File Bukti';
        buktiLink.innerHTML = 'Lihat File';
        buktiLink.onclick = () => window.open(url, '_blank');
    } else {
        buktiFileName.textContent = 'Tidak ada bukti';
        buktiLink.innerHTML = '';
    }

    // === TOMBOL AKSI DI FOOTER (KODE BARU - GANTI YANG LAMA) ===
    const footerAksi = document.getElementById('aksiFooterContainer');
    footerAksi.innerHTML = ''; // kosongkan dulu

    if (data.status === 'Tervalidasi') {
        // Tombol SETUJUI (khusus modal)
        const btnSetujui = document.createElement('button');
        btnSetujui.textContent = 'Setujui';
        btnSetujui.className = 'bg-gradient-to-r from-emerald-600 to-green-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg hover:shadow-xl transition text-lg';
        btnSetujui.onclick = function() {
            if (confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')) {
                kirimStatus(data.id, 'Disetujui', data.jenis);  // <-- status yang benar
            }
        };

        // Tombol TOLAK (khusus modal)
        const btnTolak = document.createElement('button');
        btnTolak.innerHTML = 'Tidak Setuju';
        btnTolak.className = 'bg-gradient-to-r from-red-600 to-rose-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg hover:shadow-xl transition text-lg';
        btnTolak.onclick = function() {
            if (confirm('Apakah Anda yakin ingin menolak pengajuan ini?')) {
                kirimStatus(data.id, 'Ditolak', data.jenis);
            }
        };

        footerAksi.appendChild(btnSetujui);
        footerAksi.appendChild(btnTolak);
    } else {
        footerAksi.innerHTML = '<span class="text-green-500 font-bold text-lg">Sudah Diproses</span>';
    }

    // Buka modal
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}
// Sisanya sama persis seperti sebelumnya (modal bukti & detail)

        $(document).ready(function () {
            $('#tabelValidasi').DataTable({
                pageLength: 25,
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                language: {
                    "sEmptyTable":     "Tidak ada data yang tersedia pada tabel",
                    "sProcessing":     "Sedang memproses...",
                    "sLengthMenu":     "Tampilkan _MENU_ data",
                    "sZeroRecords":    "Tidak ditemukan data yang sesuai",
                    "sInfo":           "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sInfoEmpty":      "Menampilkan 0 sampai 0 dari 0 data",
                    "sInfoFiltered":   "(disaring dari _MAX_ data keseluruhan)",
                    "sSearch":         "",
                    "sSearchPlaceholder": "Ketik untuk mencari...",
                    "oPaginate": {
                        "sFirst":    "<<",
                        "sPrevious": "<",
                        "sNext":     ">",
                        "sLast":     ">>"
                    }
                },

                // 🟨 Center khusus kolom No & Tanggal 🟨
                columnDefs: [
                    { className: "text-center", targets: [0, 2] }
                ]
            });
        });
</script>

@push('scripts')
@endpush
@endsection