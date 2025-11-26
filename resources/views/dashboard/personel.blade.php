{{-- resources/views/dashboard/personel.blade.php --}}
@extends('layouts.app')

{{-- HARUS ADA INI – JANGAN LUPA! --}}
@section('title', 'Dashboard Personel')
@section('content')
{{-- SEMUA KONTEN DASHBOARD DI SINI --}}

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-10">
    <div class="max-w-7xl mx-auto px-4">

        <!-- HEADER -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 mb-10 text-center border-4 border-indigo-300">
            <h1 class="text-5xl font-bold text-indigo-800 mb-3">
                DASHBOARD PERSONEL
            </h1>
            <p class="text-2xl text-gray-700">
                Selamat Datang, <strong>{{ Auth::guard('personel')->user()->name }}</strong> 
                ({{ Auth::guard('personel')->user()->nrp }})
            </p>
        </div>

        <!-- TOMBOL PENGAJUAN BARU -->
        <div class="text-right mb-8">
            <a href="{{ route('personel.pengajuan.create') }}" 
            class="inline-flex items-center gap-3 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white font-bold text-xl px-8 py-4 rounded-xl shadow-2xl transition transform hover:scale-105">
                <i class="fas fa-plus-circle"></i> AJUKAN CUTI / IZIN BARU
            </a>
        </div>

        <!-- TABEL RIWAYAT -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-4 border-indigo-300">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-indigo-800 mb-6">
                    RIWAYAT PENGAJUAN
                </h2>

                @if($riwayat->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-indigo-200">
                        <thead class="bg-gradient-to-r from-indigo-100 to-purple-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">NO</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">JENIS</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">KEPERLUAN</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">PERIODE</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">STATUS</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($riwayat as $i => $r)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-4 text-sm font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-4 py-2 rounded-full text-xs font-bold {{ $r->jenis == 'cuti' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
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
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4 text-center space-y-2">
                                    @if($r->status == 'Tidak Valid')
                                        <a href="{{ route('personel.pengajuan.edit', [$r->id, $r->jenis]) }}"
                                           class="block bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs px-4 py-2 rounded">EDIT</a>
                                        <button onclick="kirimUlang({{ $r->id }}, '{{ $r->jenis }}')"
                                                class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded w-full">KIRIM ULANG</button>
                                    @else
                                        <button onclick="openDetailModal({!! json_encode($r) !!})"
                                                class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs px-6 py-2 rounded">DETAILS</button>
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

        <!-- MODAL BUKTI & DETAIL (TEMPAL LANGSUNG – TANPA PARTIALS) -->
        <div id="buktiModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeBuktiModal()">
            <div class="bg-white rounded-2xl shadow-3xl w-full max-w-3xl max-h-95vh overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2"><i class="fas fa-file-alt"></i> <span id="modalTitle">Bukti</span></h3>
                    <button onclick="closeBuktiModal()" class="hover:bg-white hover:bg-opacity-20 rounded-full p-2"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="bg-gray-50 p-4">
                    <div class="relative w-full h-96 bg-white rounded-xl overflow-hidden border-4 border-indigo-100">
                        <iframe id="buktiFrame" class="absolute inset-0 w-full h-full" frameborder="0"></iframe>
                        <img id="buktiImage" src="" class="hidden absolute inset-0 w-full h-full object-contain" alt="Bukti">
                    </div>
                    <div class="mt-4 text-center">
                        <a id="downloadLink" href="#" download class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg">
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeDetailModal()">
            <div class="bg-white rounded-3xl shadow-3xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="bg-gradient-to-r from-indigo-800 to-purple-900 p-8 text-white rounded-t-3xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-4xl font-bold" id="modalJenisTitle">DETAIL PENGAJUAN</h3>
                        <button onclick="closeDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-4">
                            <i class="fas fa-times text-3xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-10 bg-gray-50">
                    <div class="grid grid-cols-2 gap-8 text-lg">
                        <div><strong>Nama:</strong> <span id="modalNama">-</span></div>
                        <div><strong>NRP:</strong> <span id="modalNrp">-</span></div>
                        <div><strong>Jenis:</strong> <span id="modalJenis">-</span></div>
                        <div><strong>Keperluan:</strong> <span id="modalKeperluan">-</span></div>
                        <div><strong>Tanggal Mulai:</strong> <span id="modalTglMulai">-</span></div>
                        <div><strong>Tanggal Selesai:</strong> <span id="modalTglSelesai">-</span></div>
                        <div><strong>Tujuan:</strong> <span id="modalTujuan">-</span></div>
                        <div><strong>Status:</strong> <span id="modalStatus" class="font-bold"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <script>
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

        function openDetailModal(data) {
            document.getElementById('modalNama').textContent = data.nama_personel || '-';
            document.getElementById('modalNrp').textContent = data.nrp || '-';
            document.getElementById('modalJenis').textContent = data.nama_jenis || '-';
            document.getElementById('modalKeperluan').textContent = data.keterangan || '-';
            document.getElementById('modalTglMulai').textContent = new Date(data.tanggal_mulai).toLocaleDateString('id-ID');
            document.getElementById('modalTglSelesai').textContent = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID') : '-';
            document.getElementById('modalTujuan').textContent = data.tujuan || '-';
            document.getElementById('modalStatus').textContent = data.status;
            document.getElementById('modalJenisTitle').textContent = data.jenis === 'cuti' ? 'DETAIL CUTI' : 'DETAIL IZIN';
            document.getElementById('detailModal').classList.remove('hidden');
        }
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
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