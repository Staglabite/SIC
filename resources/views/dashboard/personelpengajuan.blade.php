{{-- resources/views/dashboard/personelpengajuan.blade.php --}}
{{-- VERSI FULL BENAR – FORM KECIL, AMAN, CANTIK, POLRI READY --}}
@extends('layouts.app')
@section('title', 'Pengajuan Baru - SICIP POLRI')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl border-4 border-indigo-300 overflow-hidden">
            <!-- HEADER POLRI -->
            <div class="bg-gradient-to-r from-indigo-700 via-blue-600 to-purple-700 p-6 text-white text-center">
                <h1 class="text-3xl font-bold flex items-center justify-center gap-3">
                    <i class="fas fa-shield-alt text-yellow-400"></i>
                    PENGAJUAN BARU
                </h1>
                <p class="text-sm mt-2 opacity-90">Sistem Informasi Cuti & Izin Personel POLRI</p>
            </div>

            <div x-data="{ jenis: 'cuti' }" class="p-6">
                <!-- NOTIFIKASI ERROR -->
                @if($errors->any())
                    <div class="bg-red-100 border-2 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 font-bold text-center">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                        <p class="mt-2">Ada kesalahan pengisian:</p>
                        <ul class="mt-3 text-left list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border-2 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 font-bold text-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- PILIH JENIS PENGAJUAN -->
                <div class="flex justify-center gap-8 mb-10">
                    <button @click="jenis = 'cuti'"
                            :class="jenis === 'cuti' ? 'bg-purple-600 text-white ring-4 ring-purple-300 shadow-2xl' : 'bg-gray-100 text-gray-600'"
                            class="w-40 h-40 rounded-2xl font-bold text-xl transition-all hover:scale-110 shadow-xl flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-calendar-alt text-5xl"></i>
                        <span>CUTI</span>
                    </button>
                    <button @click="jenis = 'izin'"
                            :class="jenis === 'izin' ? 'bg-blue-600 text-white ring-4 ring-blue-300 shadow-2xl' : 'bg-gray-100 text-gray-600'"
                            class="w-40 h-40 rounded-2xl font-bold text-xl transition-all hover:scale-110 shadow-xl flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-file-signature text-5xl"></i>
                        <span>IZIN</span>
                    </button>
                </div>

                <!-- FORM CUTI -->
                <div x-show="jenis === 'cuti'" x-transition x-cloak>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border-4 border-purple-400">
                        <h3 class="text-2xl font-bold text-purple-900 text-center mb-8 flex items-center justify-center gap-3">
                            <i class="fas fa-calendar-check"></i> FORM PENGAJUAN CUTI
                        </h3>
                        <form action="{{ route('personel.cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="personel_id" value="{{ Auth::guard('personel')->user()->nrp }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Jenis Cuti</label>
                                    <select name="kode_cuti" required class="w-full px-4 py-3 rounded-lg border-2 border-purple-400 focus:ring-4 focus:ring-purple-300">
                                        <option value="">-- Pilih Jenis Cuti --</option>
                                        @foreach(\App\Models\Cuti::all() as $c)
                                            <option value="{{ $c->kode_cuti }}" {{ old('kode_cuti') == $c->kode_cuti ? 'selected' : '' }}>
                                                {{ $c->jenis_cuti }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kode_cuti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Pergi Dari</label>
                                    <input type="text" name="pergi_dari" value="{{ old('pergi_dari', 'Bandung') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pergi_dari') border-red-500 @else border-purple-400 @enderror">
                                    @error('pergi_dari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Tujuan</label>
                                    <input type="text" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Jakarta" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tujuan') border-red-500 @else border-purple-400 @enderror">
                                    @error('tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Transportasi</label>
                                    <input type="text" name="transportasi" value="{{ old('transportasi') }}" required placeholder="Kereta Api" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('transportasi') border-red-500 @else border-purple-400 @enderror">
                                    @error('transportasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Tanggal Mulai</label>
                                    <input type="date" name="mulai_tgl" value="{{ old('mulai_tgl') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('mulai_tgl') border-red-500 @else border-purple-400 @enderror">
                                    @error('mulai_tgl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Tanggal Selesai</label>
                                    <input type="date" name="sampai_tgl" value="{{ old('sampai_tgl') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('sampai_tgl') border-red-500 @else border-purple-400 @enderror">
                                    @error('sampai_tgl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Pengikut</label>
                                    <input type="text" name="pengikut" value="{{ old('pengikut') }}" placeholder="Tidak ada" 
                                           class="w-full px-4 py-3 rounded-lg border-2 border-purple-400">
                                </div>

                                <div>
                                    <label class="block text-purple-800 font-bold mb-2">Upload Bukti (PDF/JPG)</label>
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('bukti') border-red-500 @else border-purple-400 @enderror text-sm">
                                    @error('bukti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-purple-800 font-bold mb-2">Catatan Tambahan</label>
                                <textarea name="catatan" rows="4" placeholder="Alasan cuti, dll..." 
                                          class="w-full px-4 py-3 rounded-lg border-2 border-purple-400">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="text-center pt-6">
                                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xl px-16 py-5 rounded-xl shadow-2xl transform hover:scale-105 transition-all">
                                    <i class="fas fa-paper-plane mr-3"></i>
                                    KIRIM PENGAJUAN CUTI
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- FORM IZIN -->
                <div x-show="jenis === 'izin'" x-transition x-cloak>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 border-4 border-blue-400">
                        <h3 class="text-2xl font-bold text-blue-900 text-center mb-8 flex items-center justify-center gap-3">
                            <i class="fas fa-file-contract"></i> FORM PENGAJUAN IZIN
                        </h3>
                        <form action="{{ route('personel.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="personel_id" value="{{ Auth::guard('personel')->user()->nrp }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Keperluan</label>
                                    <input type="text" name="keperluan" value="{{ old('keperluan') }}" required placeholder="Dinas ke Polda" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('keperluan') border-red-500 @else border-blue-400 @enderror">
                                    @error('keperluan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Pengikut</label>
                                    <input type="text" name="pengikut" value="{{ old('pengikut') }}" required placeholder="Tidak ada" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pengikut') border-red-500 @else border-blue-400 @enderror">
                                    @error('pengikut') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Pergi Dari</label>
                                    <input type="text" name="pergi_dari" value="{{ old('pergi_dari', 'Bandung') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pergi_dari') border-red-500 @else border-blue-400 @enderror">
                                    @error('pergi_dari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Tujuan</label>
                                    <input type="text" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Polda Jabar" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tujuan') border-red-500 @else border-blue-400 @enderror">
                                    @error('tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Tanggal Berangkat</label>
                                    <input type="date" name="tgl_berangkat" value="{{ old('tgl_berangkat') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tgl_berangkat') border-red-500 @else border-blue-400 @enderror">
                                    @error('tgl_berangkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Tanggal Kembali</label>
                                    <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tgl_kembali') border-red-500 @else border-blue-400 @enderror">
                                    @error('tgl_kembali') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Transportasi</label>
                                    <input type="text" name="transportasi" value="{{ old('transportasi') }}" required placeholder="Mobil Dinas" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('transportasi') border-red-500 @else border-blue-400 @enderror">
                                    @error('transportasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-blue-800 font-bold mb-2">Upload Bukti (WAJIB)</label>
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('bukti') border-red-500 @else border-blue-400 @enderror text-sm">
                                    @error('bukti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-blue-800 font-bold mb-2">Catatan</label>
                                <textarea name="catatan" rows="4" placeholder="Detail kegiatan..." 
                                          class="w-full px-4 py-3 rounded-lg border-2 border-blue-400">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="text-center pt-6">
                                <button type="submit" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold text-xl px-16 py-5 rounded-xl shadow-2xl transform hover:scale-105 transition-all">
                                    <i class="fas fa-paper-plane mr-3"></i>
                                    KIRIM PENGAJUAN IZIN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush