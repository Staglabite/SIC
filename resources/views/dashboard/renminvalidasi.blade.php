{{-- resources/views/dashboard/renminvalidasi.blade.php --}}
@extends('layouts.app')
@section('title', 'Renmin - Validasi Pengajuan')

@section('content')
<div class="min-h-screen bg-white py-2">
    <div class="max-w-7xl mx-auto px-4">

    

        <!-- TABEL -->
        <div class="bg-white rounded-xl shadow-2xl border-2 overflow-hidden">
                <!-- HEADER TABEL -->
                <div class="p-8">
                    <h2 class="text-3xl font-bold text-black mb-4">
                        Tabel Pengajuan
                    </h2>
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y" id="tabelValidasi">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">No</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Nama</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">NRP</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Jenis</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Keperluan</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Tanggal</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Status</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Bukti</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-black">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pengajuan as $i => $p)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-5 text-sm font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-5 text-sm font-medium text-black whitespace-nowrap">{{ $p->nama_personel ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-black">{{ $p->nrp ?? '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="whitespace-nowrap inline-block px-5 py-2.5 rounded-full text-xs font-bold {{ $p->jenis == 'cuti' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper($p->nama_jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm">{{ $p->keterangan ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                                    @if($p->tanggal_selesai) → {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }} @endif
                                </td>
                                <td class="px-6 py-5">
                                    @php $s = $p->status; @endphp
                                    <span class="px-5 py-2.5 rounded-full text-xs font-bold whitespace-nowrap
                                        {{ $s == 'Tervalidasi' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($s == 'Ditolak' ? 'bg-red-100 text-red-800' : 
                                           ($s == 'Tidak Valid' ? 'bg-orange-100 text-orange-800' : 'bg-amber-100 text-amber-800')) }}">
                                        {{ $s == 'Proses' ? 'Menunggu' : $s }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($p->pathFile_bukti)
                                        <button onclick="openBuktiModal('{{ asset('storage/' . $p->pathFile_bukti) }}', '{{ $p->namaFile_bukti ?? 'Bukti' }}')" 
                                                class="text-black font-bold text-xs">
                                            <i class="fas fa-paperclip"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Tanpa bukti</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center space-y-2 whitespace-nowrap">
                                    <button onclick='openDetailModal({!! json_encode($p) !!})' 
                                            class="bg-white text-black font-bold px-5 py-2 rounded-lg shadow text-xs border-2">
                                        DETAILS
                                    </button>

                                    @if($p->status == 'Proses')
                                        <button onclick="updateStatus({{ $p->id }}, 'Tervalidasi', '{{ $p->jenis }}')" 
                                                class="bg-gradient-to-r from-emerald-600 to-green-700 text-white font-bold px-5 py-2 rounded-lg shadow text-xs block w-full">
                                            VALID
                                        </button>

                                        <div class="relative inline-block w-full">
                                            <button onclick="toggleDropdown({{ $p->id }})" 
                                                    class="bg-gradient-to-r from-red-600 to-rose-700 text-white font-bold px-5 py-2 rounded-lg shadow text-xs w-full">
                                                TOLAK
                                            </button>
                                            <div id="dropdown-{{ $p->id }}" class="hidden absolute z-10 mt-2 w-full bg-white rounded-lg shadow-2xl border">
                                                <button onclick="updateStatus({{ $p->id }}, 'Ditolak', '{{ $p->jenis }}')" 
                                                        class="block w-full text-left px-4 py-3 text-red-700 hover:bg-red-50 font-medium">
                                                    Ditolak
                                                </button>
                                                <button onclick="updateStatus({{ $p->id }}, 'Tidak Valid', '{{ $p->jenis }}')" 
                                                        class="block w-full text-left px-4 py-3 text-orange-700 hover:bg-orange-50 font-medium border-t">
                                                    Tidak Valid
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-green-500 ml-5">Sudah Diproses</span>
                                    @endif
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
// BUKTI MODAL
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
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

// UPDATE STATUS
function updateStatus(id, status, tipe) {
    if (!confirm(`Yakin mengubah status menjadi "${status}"?`)) return;
    fetch("{{ route('renmin.validasi.update') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ id, status, tipe })
    })
    .then(r => r.json())
    .then(data => { if (data.success) { alert('Berhasil!'); location.reload(); } });
}

function toggleDropdown(id) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
        if (d.id !== 'dropdown-' + id) d.classList.add('a hidden');
    });
    document.getElementById('dropdown-' + id).classList.toggle('hidden');
}
document.addEventListener('click', e => {
    if (!e.target.closest('button[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>

@push('styles')
<style>
    .max-h-95vh { max-height: 95vh; }
    #buktiModal.hidden, #detailModal.hidden { display: none; }
</style>
@endpush
@endsection