{{-- resources/views/dashboard/personel.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Personel')
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

<div class="min-h-screen bg-white py-2">
    <div class="max-w-7xl mx-auto px-4">

        <!-- TABEL RIWAYAT -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-2">

            <!-- HEADER TABEL + TOMBOL -->
            <div class="p-8 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-black">
                    Riwayat Pengajuan
                </h2>

                <a href="{{ route('personel.pengajuan.create') }}" 
                   class="inline-flex items-center gap-2 bg-yellow-900
                   hover:bg-yellow-950 text-white px-5 py-2 rounded-md shadow text-l">
                    <i class="fas fa-plus-circle"></i> Ajukan Cuti / Izin Baru
                </a>
            </div>

            @if($riwayat->count() > 0)
            <div class="p-8 overflow-x-auto">
                <table id="riwayatTable" class="min-w-full leading-normal">
                    <thead class="bg-gray-100 ">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-black">No</th>
                            <th class="px-10 text-left text-xs font-bold text-black">Jenis</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-black">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-black">Status</th>
                            <th class="px-10 py-4 text-left text-xs font-bold text-black">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 ">
                        @foreach($riwayat as $i => $r)
                        <tr class="hover:bg-indigo-50 transition">

                            <td class="px-6 py-4 text-sm font-medium text-center"></td>

                            <td class="px-5">
                                <span class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap 
                                    {{ $r->jenis == 'cuti' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ strtoupper($r->nama_jenis) }}
                                </span>
                            </td>



                            <td class="px-6 py-4 text-sm whitespace-nowrap text-center">
                                {{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d M Y') }}
                                @if($r->tanggal_selesai)
                                    → {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d M Y') }}
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $status = strtolower($r->status);
                                $bg = match($status) {
                                    'tervalidasi', 'disetujui' => 'bg-emerald-100 text-emerald-800',
                                    'proses'      => 'bg-amber-100 text-amber-800',
                                    'ditolak'     => 'bg-red-100 text-red-800',
                                    default       => 'bg-orange-100 text-orange-800'
                                };
                            @endphp
                                <span class="px-4 py-2 rounded-full text-xs font-bold {{ $bg }}">
                                    {{ ucwords($status) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    <button 
                                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs px-6 py-2 rounded"
                                        onclick="openDetailModal(JSON.parse(atob(this.dataset.detail)))"
                                        data-detail="{{ base64_encode(json_encode($r)) }}">
                                        Details
                                    </button>
                                    
                                    @if($r->status == 'Tidak Valid' || $r->status == 'Proses')
                                        <button onclick="openEditModal({{ $r->id }}, JSON.parse(atob('{{ base64_encode(json_encode($r)) }}')))"
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs px-6 py-2 rounded">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        @if($r->status == 'Tidak Valid')
                                            <button onclick="kirimUlang({{ $r->id }}, '{{ $r->jenis }}')" 
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded">
                                                Kirim Ulang
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @else
            <div class="text-center py-20">
                <i class="fas fa-inbox text-9xl text-gray-300 mb-6"></i>
                <p class="text-3xl font-bold text-gray-600">Belum Ada Pengajuan</p>
            </div>
            @endif
        </div>
    </div>

        {{-- MODAL EDIT --}}
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50 p-4" 
            onclick="if(event.target === this) closeEditModal()">
            <div class="bg-white rounded-3xl shadow-3xl max-w-5xl w-full max-h-screen overflow-y-auto">
                <div class="bg-white p-6 text-black rounded-t-3xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-3xl font-bold">Edit Pengajuan</h3>
                        <button onclick="closeEditModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-times text-3xl"></i>
                        </button>
                    </div>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" class="p-8 bg-gray-50">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="pengajuan_id" id="editId">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-black font-bold mb-2">Jenis Pengajuan</label>
                            <input type="text" id="editJenisDisplay" class="w-full px-3 py-2 border rounded-lg bg-gray-100" disabled>
                        </div>
                        <div id="keperluanWrapper">
                                <label class="block text-black font-bold mb-2">Keperluan</label>
                                <textarea name="keterangan" id="editKeterangan" rows="3" 
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500"></textarea>
                                <small class="text-gray-500">Wajib diisi untuk izin, tidak diperlukan untuk cuti</small>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-black font-bold mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="editTglMulai" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-black font-bold mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="editTglSelesai" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-black font-bold mb-2">Pergi Dari</label>
                            <input type="text" name="pergi_dari" id="editPergiDari" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-black font-bold mb-2">Tujuan</label>
                            <input type="text" name="tujuan" id="editTujuan" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-black font-bold mb-2">Transportasi</label>
                            <input type="text" name="transportasi" id="editTransportasi" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-black font-bold mb-2">Pengikut</label>
                            <input type="text" name="pengikut" id="editPengikut" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-black font-bold mb-2">Catatan Tambahan</label>
                        <textarea name="catatan" id="editCatatan" rows="1" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-black font-bold mb-2">Bukti Pendukung</label>
                        <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border rounded-lg">
                        <small class="text-gray-500">File lama tetap digunakan jika tidak upload baru.</small>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-lg">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-yellow-900 hover:bg-yellow-950 text-white font-bold rounded-lg shadow-lg">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
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
                            <h4 class="font-bold text-black mb-3 text-base">Bukti Pendukung</h4>
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
                            <h4 class="font-bold text-black mb-3 text-base">Keperluan</h4>
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4 text-gray-700">
                                <p id="modalKeperluan" class="italic leading-relaxed min-h-[40px] text-sm">
                                    Tidak ada keterangan
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 text-left sm:text-right w-full sm:w-auto">
                        Diajukan pada: <span id="modalCreatedAt" class="font-bold text-yellow-900">-</span>
                    </div>

                    <!-- Footer: Tombol Aksi di KIRI, Close di KANAN -->
                    <div class="pt-6 border-t flex flex-col sm:flex-row justify-between items-center gap-4">
                        <!-- Kiri: aksi (Download Surat dll) -->
                        <div id="aksiFooterContainer" class="flex gap-3"></div>

                        <!-- Kanan: tombol Tutup -->
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
        
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-detail');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                const data = JSON.parse(atob(btn.dataset.detail));
                openDetailModal(data);
            }
        });

        function openEditModal(id, data) {
            // Isi form seperti biasa
            document.getElementById('editId').value = id;
            document.getElementById('editJenisDisplay').value = data.nama_jenis || data.jenis || '';

            document.getElementById('editKeterangan').value   = data.keterangan   || '';
            document.getElementById('editTglMulai').value    = data.tanggal_mulai || '';
            document.getElementById('editTglSelesai').value  = data.tanggal_selesai || '';
            document.getElementById('editPergiDari').value   = data.pergi_dari   || '';
            document.getElementById('editTujuan').value      = data.tujuan       || '';
            document.getElementById('editTransportasi').value = data.transportasi || '';
            document.getElementById('editPengikut').value    = data.pengikut     || '';
            document.getElementById('editCatatan').value     = data.catatan      || '';

            // Set action form
            document.getElementById('editForm').action = `/dashboard/personel/pengajuan/${id}`;

            // === LOGIKA HIDE KOLOM KEPERLUAN JIKA CUTI ===
            const jenis = (data.jenis || '').toLowerCase().trim();
            const keperluanWrapper = document.getElementById('keperluanWrapper');
            const keteranganField  = document.getElementById('editKeterangan');

            if (jenis === 'cuti') {
                keperluanWrapper.style.display = 'none';   // sembunyikan seluruh kolom
                keteranganField.removeAttribute('required'); // hapus required (jika ada)
                keteranganField.value = '';                 // kosongkan (opsional)
            } else {
                keperluanWrapper.style.display = 'block';  // atau 'grid' kalau pakai grid
                keteranganField.setAttribute('required', 'required');
            }

            // Tampilkan modal
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('editForm').reset();
        }

        // Submit form dengan fetch (biar tidak reload halaman)
        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    alert('Pengajuan berhasil diperbarui!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan'));
        });

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
        }
        function closeBuktiModal() {
            document.getElementById('buktiModal').classList.add('hidden');
            document.getElementById('buktiFrame').src = '';
            document.getElementById('buktiImage').src = '';
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function openDetailModal(data) {
            // Isi semua field (sama seperti sebelumnya)
            document.getElementById('modalNama').textContent = data.nama_personel || '-';
            document.getElementById('modalNrp').textContent = data.nrp || '-';
            document.getElementById('modalPangkat').textContent = data.pangkat || '-';
            document.getElementById('modalGolongan').textContent = data.golongan || '-';
            document.getElementById('modalJenis').textContent = (data.nama_jenis || data.jenis?.toUpperCase()) || '-';
            document.getElementById('modalKeperluan').textContent = data.keterangan || 'Tidak ada keterangan';
            document.getElementById('modalTglMulai').textContent = formatDate(data.tanggal_mulai);
            document.getElementById('modalTglSelesai').textContent = data.tanggal_selesai ? formatDate(data.tanggal_selesai) : '-';

            const lama = data.tanggal_selesai 
                ? Math.ceil((new Date(data.tanggal_selesai) - new Date(data.tanggal_mulai)) / 86400000) + 1 
                : 1;
            document.getElementById('modalLama').textContent = lama + ' Hari';

            document.getElementById('modalPergiDari').textContent = data.pergi_dari || '-';
            document.getElementById('modalTujuan').textContent = data.tujuan || '-';
            document.getElementById('modalTransportasi').textContent = data.transportasi || '-';
            document.getElementById('modalPengikut').textContent = data.pengikut || '-';
            document.getElementById('modalCreatedAt').textContent = new Date(data.created_at).toLocaleString('id-ID');

            // Status badge
            const badge = document.getElementById('modalStatusBadge');
            const statusText = data.status === 'Proses' ? 'Menunggu' : data.status.toUpperCase();
            badge.textContent = statusText;
            badge.className = 'inline-block px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider border';
            if (['Disetujui', 'Tervalidasi'].includes(data.status)) {
                badge.classList.add('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
            } else if (data.status === 'Ditolak') {
                badge.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
            } else if (data.status === 'Tidak Valid') {
                badge.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
            } else {
                badge.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-300');
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

            // FOOTER: TOMBOL DOWNLOAD SURAT (PAKAI FETCH AGAR TIDAK DOWNLOAD HTML)
            // === TOMBOL DOWNLOAD SURAT – VERSI YANG BENAR-BENAR JALAN ===
            const container = document.getElementById('aksiFooterContainer');
            container.innerHTML = '';

            if (data.status === 'Disetujui') {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'inline-flex items-center gap-3 bg-yellow-900 hover:bg-yellow-950 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition text-lg';
                btn.innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Surat
                `;

                btn.onclick = async function () {
                    btn.disabled = true;
                    btn.innerHTML = '⏳ Memproses...';
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        const response = await fetch(`/dashboard/personel/surat/download/${data.id}`, {
                            method: 'GET',
                            credentials: 'same-origin', // INI PENTING
                            headers: {
                                'Accept': 'application/pdf',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            alert('Error: ' + errorText);
                            return;
                        }
                        
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Surat_${data.jenis}_${data.nrp}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                    } catch (err) {
                        alert('Error: ' + err.message);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Download Surat Resmi';
                    }
                };

                container.appendChild(btn);

            } else if (data.status === 'Proses' || data.status === 'Tidak Valid') {
                container.innerHTML = '<span class="text-amber-600 font-bold text-lg"></span>';
            } else if (data.status === 'Ditolak') {
                container.innerHTML = '<span class="text-red-600 font-bold text-lg"></span>';
            }

            // Buka modal
            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function kirimUlang(id, tipe) {
            if(!confirm('Kirim ulang pengajuan ini?')) return;
            fetch("{{ route('personel.pengajuan.kirim-ulang') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({id, tipe})
            })
            .then(r => r.json())
            .then(res => { if(res.success) { alert('Berhasil dikirim ulang!'); location.reload(); } });
        }

        $(document).ready(function () {
            $('#riwayatTable').DataTable({
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
    </div>
</div>

{{-- AKHIR DARI @section('content') – HANYA SATU KALI! --}}
@endsection