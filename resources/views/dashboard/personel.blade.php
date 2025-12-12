{{-- resources/views/dashboard/personel.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Personel')
@section('content')

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
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-700 
                   hover:from-green-700 hover:to-emerald-800 text-white px-5 py-2 rounded-md shadow text-l">
                    <i class="fas fa-plus-circle"></i> Ajukan Cuti / Izin Baru
                </a>
            </div>

            @if($riwayat->count() > 0)
            <div class="p-8 overflow-x-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-200 ">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Keperluan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Periode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Bukti</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach($riwayat as $i => $r)
                        <tr class="hover:bg-indigo-50 transition">

                            <td class="px-6 py-4 text-sm font-medium">{{ $i + 1 }}</td>

                            <td class="px-6 py-4">
                                <span class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap 
                                    {{ $r->jenis == 'cuti' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ strtoupper($r->nama_jenis) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm">{{ $r->keterangan ?? '-' }}</td>

                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d M Y') }}
                                @if($r->tanggal_selesai)
                                    → {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d M Y') }}
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = strtolower($r->status);
                                    $bg = match($status) {
                                        'tervalidasi' => 'bg-emerald-100 text-emerald-800',
                                        'proses'      => 'bg-amber-100 text-amber-800',
                                        'ditolak'     => 'bg-red-100 text-red-800',
                                        default       => 'bg-orange-100 text-orange-800'
                                    };
                                @endphp
                                <span class="px-4 py-2 rounded-full text-xs font-bold {{ $bg }}">
                                    {{ ucwords($status) }}
                                </span>
                            </td>

                            <!-- BUKTI -->
                            <td class="px-6 py-4 text-center">
                                @if($r->pathFile_bukti)
                                    <button 
                                        onclick="openBuktiModal('{{ asset('storage/' . $r->pathFile_bukti) }}', '{{ $r->namaFile_bukti ?? 'Bukti' }}')" 
                                        class="text-indigo-700 hover:text-indigo-900 font-bold text-xs">
                                        <i class="fas fa-paperclip"></i> Lihat
                                    </button>
                                @else
                                    <span class="text-gray-400 italic text-xs">- tidak ada -</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center space-y-2 whitespace-nowrap">
                                @if($r->status == 'Tidak Valid' || $r->status == 'Proses')   {{-- tambah Proses kalau mau bisa edit saat masih diproses --}}
                                    <button onclick="openEditModal({{ $r->id }}, JSON.parse(atob('{{ base64_encode(json_encode($r)) }}')))"
                                            class="block bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs px-4 py-2 rounded w-full">
                                        <i class="fas fa-edit"></i> EDIT
                                    </button>

                                    @if($r->status == 'Tidak Valid')
                                        <button onclick="kirimUlang({{ $r->id }}, '{{ $r->jenis }}')" 
                                                class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded w-full">
                                            KIRIM ULANG
                                        </button>
                                    @endif

                                @else
                                    <button 
                                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs px-6 py-2 rounded"
                                        onclick="openDetailModal(JSON.parse(atob(this.dataset.detail)))"
                                        data-detail="{{ base64_encode(json_encode($r)) }}">
                                        Details
                                    </button>
                                @endif
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
                <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-6 text-white rounded-t-3xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-3xl font-bold">EDIT PENGAJUAN</h3>
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
                            <label class="block text-gray-700 font-bold mb-2">Jenis Pengajuan</label>
                            <input type="text" id="editJenisDisplay" class="w-full px-4 py-3 border rounded-lg bg-gray-100" disabled>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Keperluan</label>
                            <textarea name="keterangan" id="editKeterangan" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-500" required></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="editTglMulai" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="editTglSelesai" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Pergi Dari</label>
                            <input type="text" name="pergi_dari" id="editPergiDari" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Tujuan</label>
                            <input type="text" name="tujuan" id="editTujuan" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Transportasi</label>
                            <input type="text" name="transportasi" id="editTransportasi" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Pengikut</label>
                            <input type="text" name="pengikut" id="editPengikut" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Catatan Tambahan</label>
                        <textarea name="catatan" id="editCatatan" rows="3" class="w-full px-4 py-3 border rounded-lg"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Bukti Pendukung (biarkan kosong jika tidak ingin ganti)</label>
                        <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 border rounded-lg">
                        <small class="text-gray-500">File lama tetap digunakan jika tidak upload baru.</small>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-lg">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-bold rounded-lg shadow-lg">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL BUKTI – LANGSUNG DI SINI (TIDAK PAKAI @include) --}}
        <div id="buktiModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeBuktiModal()">
            <div class="bg-white rounded-2xl shadow-3xl w-full max-w-3xl max-h-95vh overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2"><i class="fas fa-file-alt"></i> <span id="modalTitle">Bukti</span></h3>
                    <button onclick="closeBuktiModal()" class="hover:bg-white hover:bg-opacity-20 rounded-full p-2"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="bg-gray-50 p-4">
                    <div class="relative w-full h-96 bg-white rounded-xl overflow-hidden border-4 border-indigo-100">
                        <iframe id="buktiFrame" class="absolute inset-0 w-full h-full" src="" frameborder="0"></iframe>
                        <img id="buktiImage" src="" class="hidden absolute inset-0 w-full h-full object-contain" alt="Bukti">
                    </div>
                    <div class="mt-4 text-center">
                        <a id="downloadLink" href="#" download class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DETAILS – LANGSUNG DI SINI --}}
        <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeDetailModal()">
            <div class="bg-white rounded-3xl shadow-3xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="bg-white p-8 text-black rounded-t-3xl">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-6">
                            <div class="bg-gray-200 rounded-full p-5 shadow-2xl">
                                <i class="fas fa-paper-plane text-blue-600 text-5xl"></i>
                            </div>
                            <div>
                                <h3 class="text-4xl font-bold" id="modalJenisTitle">DETAIL PENGAJUAN</h3
                            </div>
                        </div>
                        <button onclick="closeDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-4 transition">
                            <i class="fas fa-times text-3xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-10 bg-gray-50">
                    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-l-8 border-gray-200">
                        <h4 class="text-2xl font-bold text-black mb-4 flex items-center gap-3">
                            <i class="fas fa-user-tie text-blue-600"></i> Identitas Personel
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                            <div><p class="text-gray-600">Nama</p><p id="modalNama" class="font-semibold text-xl text-black">-</p></div>
                            <div><p class="text-gray-600">NRP</p><p id="modalNrp" class="font-semibold text-xl text-black">-</p></div>
                            <div><p class="text-gray-600">Pangkat</p><p id="modalPangkat" class="font-semibold">-</p></div>
                            <div><p class="text-gray-600">Jabatan</p><p id="modalJabatan" class="font-semibold text-black">-</p></div>
                            <div><p class="text-gray-600">Golongan</p><p id="modalGolongan" class="font-semibold text-black">-</p></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-8 border-l-8 border-gray-200">
                        <h4 class="text-2xl font-bold text-black mb-6 flex items-center gap-3">
                            <i class="fas fa-file-alt text-blue-600"></i> DETAIL PENGAJUAN
                        </h4>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><p class="text-gray-600 font-medium">Jenis</p><p id="modalJenis" class="text-xl font-semibold text-black">-</p></div>
                                <div><p class="text-gray-600 font-medium">Keperluan</p><p id="modalKeperluan" class="text-xl font-semibold">-</p></div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-5 rounded-xl border-l-4 border-green-600">
                                    <p class="text-sm text-gray-600">Mulai</p><p id="modalTglMulai" class="text-xl font-bold text-black">-</p>
                                </div>
                                <div class="bg-gradient-to-r from-red-50 to-rose-50 p-5 rounded-xl border-l-4 border-red-600">
                                    <p class="text-sm text-gray-600">Selesai</p><p id="modalTglSelesai" class="text-xl font-bold text-black">-</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div><p class="text-gray-600 font-medium">Dari</p><p id="modalPergiDari" class="font-semibold">-</p></div>
                                <div><p class="text-gray-600 font-medium">Tujuan</p><p id="modalTujuan" class="font-semibold text-black">-</p></div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div><p class="text-gray-600 font-medium">Transportasi</p><p id="modalTransportasi" class="font-semibold">-</p></div>
                                <div><p class="text-gray-600 font-medium">Pengikut</p><p id="modalPengikut" class="font-semibold text-black">-</p></div>
                            </div>
                            <div class="bg-yellow-50 p-6 rounded-xl border-2 border-yellow-400">
                                <p class="text-gray-700 font-medium mb-2">Catatan</p>
                                <p id="modalCatatan" class="italic text-gray-800">Tidak ada catatan</p>
                            </div>
                            <div class="text-right pt-6 border-t">
                                <p class="text-sm text-gray-500">Dibuat: <span id="modalCreatedAt" class="font-bold text-indigo-700">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function openEditModal(id, data) {
            // Isi form
            document.getElementById('editId').value = id;
            document.getElementById('editJenisDisplay').value = data.nama_jenis || data.jenis;
            document.getElementById('editKeterangan').value = data.keterangan || '';
            document.getElementById('editTglMulai').value = data.tanggal_mulai || '';
            document.getElementById('editTglSelesai').value = data.tanggal_selesai || '';
            document.getElementById('editPergiDari').value = data.pergi_dari || '';
            document.getElementById('editTujuan').value = data.tujuan || '';
            document.getElementById('editTransportasi').value = data.transportasi || '';
            document.getElementById('editPengikut').value = data.pengikut || '';
            document.getElementById('editCatatan').value = data.catatan || '';

            // Set action form
            document.getElementById('editForm').action = `/dashboard/personel/pengajuan/${id}`;

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
            document.getElementById('modalNama').textContent = data.nama_personel || '-';
            document.getElementById('modalNrp').textContent = data.nrp || '-';
            document.getElementById('modalPangkat').textContent = data.pangkat || '-';
            document.getElementById('modalJabatan').textContent = data.jabatan || '-';
            document.getElementById('modalGolongan').textContent = data.golongan || '-'; // ← BARU

            document.getElementById('modalJenisTitle').textContent = data.jenis === 'cuti' ? 'Detail Cuti' : 'Detail Izin';
            document.getElementById('modalJenis').textContent = data.nama_jenis || '-';
            document.getElementById('modalKeperluan').textContent = data.keterangan || '-';
            document.getElementById('modalTglMulai').textContent = formatDate(data.tanggal_mulai);
            document.getElementById('modalTglSelesai').textContent = data.tanggal_selesai ? formatDate(data.tanggal_selesai) : '-';
            
            document.getElementById('modalPergiDari').textContent = data.pergi_dari || '-';
            document.getElementById('modalTujuan').textContent = data.tujuan || '-';
            document.getElementById('modalTransportasi').textContent = data.transportasi || '-';
            document.getElementById('modalPengikut').textContent = data.pengikut || '-';
            document.getElementById('modalCatatan').textContent = data.catatan || 'Tidak ada catatan';
            
            document.getElementById('modalCreatedAt').textContent = new Date(data.created_at).toLocaleString('id-ID');
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
        </script>
    </div>
</div>

{{-- AKHIR DARI @section('content') – HANYA SATU KALI! --}}
@endsection