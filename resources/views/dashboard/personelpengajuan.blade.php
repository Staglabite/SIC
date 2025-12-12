{{-- resources/views/dashboard/personelpengajuan.blade.php --}}
{{-- VERSI FULL BENAR – FORM KECIL, AMAN, CANTIK, POLRI READY --}}
@extends('layouts.app')
@section('title', 'Dashboard Pengajuan')

@section('content')
<div class="min-h-screen bg-white py-2">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl border-2 border-gray-300 overflow-hidden">
            <!-- HEADER POLRI -->
            <div class="bg-white p-6 text-black">
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    <i class="fas fa-plus-circle text-green-400"></i>
                    Pengajuan Baru
                </h1>
                <p class=" mt-2 text-xl opacity-90">Silahkan isi data dibawah</p>
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
                <div class="flex justify-center gap-8 mb-7">
                    <button @click="jenis = 'cuti'"
                            :class="jenis === 'cuti' ? 'bg-gray-200 text-black ring-1 ring-gray-300 shadow-2xl' : 'bg-white text-black'"
                            class="w-20 h-20 rounded-2xl font-bold text-xl transition-all hover:scale-110 shadow-xl flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-calendar-alt text-xl text-black"></i>
                        <span>Cuti</span>
                    </button>
                    <button @click="jenis = 'izin'"
                            :class="jenis === 'izin' ? 'bg-gray-200 text-black ring-1 ring-gray-300 shadow-2xl' : 'bg-white text-black'"
                            class="w-20 h-20 rounded-2xl font-bold text-xl transition-all hover:scale-110 shadow-xl flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-file-signature text-xl"></i>
                        <span>Izin</span>
                    </button>
                </div>

                <!-- FORM CUTI -->
                <div x-show="jenis === 'cuti'" x-transition x-cloak>
                    <div class="bg-white rounded-2xl p-8 border-2 border-gray-200">
                        <h3 class="text-2xl font-bold text-black text-center mb-8 flex items-center justify-center gap-3">
                            <i class="fas fa-calendar-check"></i> Form Pengajuan Cuti
                        </h3>
                        <form action="{{ route('personel.cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="personel_id" value="{{ Auth::guard('personel')->user()->nrp }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-black font-bold mb-2">Jenis Cuti</label>
                                    <select name="kode_cuti" required class="w-full px-4 py-3 rounded-lg border-2 border-gray-500 focus:ring-1 focus:ring-black">
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
                                    <label class="block text-black font-bold mb-2">Pergi Dari</label>
                                    <input type="text" name="pergi_dari" value="{{ old('pergi_dari', 'Bandung') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pergi_dari') border-red-500 @else border-gray-500 @enderror">
                                    @error('pergi_dari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Tujuan</label>
                                    <input type="text" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Jakarta" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tujuan') border-red-500 @else border-gray-500 @enderror">
                                    @error('tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Transportasi</label>
                                    <input type="text" name="transportasi" value="{{ old('transportasi') }}" required placeholder="Kereta Api" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('transportasi') border-red-500 @else border-gray-500 @enderror">
                                    @error('transportasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block black font-bold mb-2">Tanggal Mulai</label>
                                    <input type="date" name="mulai_tgl" value="{{ old('mulai_tgl') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('mulai_tgl') border-red-500 @else border-gray-500 @enderror">
                                    @error('mulai_tgl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Tanggal Selesai</label>
                                    <input type="date" name="sampai_tgl" value="{{ old('sampai_tgl') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('sampai_tgl') border-red-500 @else border-gray-500 @enderror">
                                    @error('sampai_tgl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Pengikut</label>
                                    <input type="text" name="pengikut" value="{{ old('pengikut') }}" placeholder="Tidak ada" 
                                           class="w-full px-4 py-3 rounded-lg border-2 border-gray-500">
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Upload Bukti (PDF/JPG)</label>
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('bukti') border-red-500 @else border-gray-500 @enderror text-sm">
                                    @error('bukti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-black font-bold mb-2">Catatan Tambahan</label>
                                <textarea name="catatan" rows="4" placeholder="Alasan cuti, dll..." 
                                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-500">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="text-center pt-6">
                                <button type="submit" class="bg-white hover-gray-400 text-black font-bold text-l px-16 py-5 rounded-xl shadow-2xl transform hover:scale-105 border border-gray-500 transition-all">
                                    <i class="fas fa-paper-plane mr-3"></i>
                                    Kirim Pengajuan Cuti
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- FORM IZIN -->
                <div x-show="jenis === 'izin'" x-transition x-cloak>
                    <div class="bg-white rounded-2xl p-8 border-2 border-gray-200">
                        <h3 class="text-2xl font-bold text-black text-center mb-8 flex items-center justify-center gap-3">
                            <i class="fas fa-file-contract"></i> Form Pengajuan Izin
                        </h3>
                        <form action="{{ route('personel.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="personel_id" value="{{ Auth::guard('personel')->user()->nrp }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-black font-bold mb-2">Keperluan</label>
                                    <input type="text" name="keperluan" value="{{ old('keperluan') }}" required placeholder="Dinas ke Polda" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('keperluan') border-red-500 @else border-gray-500 @enderror">
                                    @error('keperluan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Pengikut</label>
                                    <input type="text" name="pengikut" value="{{ old('pengikut') }}" required placeholder="Tidak ada" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pengikut') border-red-500 @else border-gray-500 @enderror">
                                    @error('pengikut') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Pergi Dari</label>
                                    <input type="text" name="pergi_dari" value="{{ old('pergi_dari', 'Bandung') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('pergi_dari') border-red-500 @else border-gray-500 @enderror">
                                    @error('pergi_dari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Tujuan</label>
                                    <input type="text" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Polda Jabar" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tujuan') border-red-500 @else border-gray-500 @enderror">
                                    @error('tujuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Tanggal Berangkat</label>
                                    <input type="date" name="tgl_berangkat" value="{{ old('tgl_berangkat') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tgl_berangkat') border-red-500 @else border-gray-500 @enderror">
                                    @error('tgl_berangkat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Tanggal Kembali</label>
                                    <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali') }}" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('tgl_kembali') border-red-500 @else border-gray-500 @enderror">
                                    @error('tgl_kembali') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Transportasi</label>
                                    <input type="text" name="transportasi" value="{{ old('transportasi') }}" required placeholder="Mobil Dinas" 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('transportasi') border-red-500 @else border-gray-500 @enderror">
                                    @error('transportasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-black font-bold mb-2">Upload Bukti (WAJIB)</label>
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required 
                                           class="w-full px-4 py-3 rounded-lg border-2 @error('bukti') border-red-500 @else border-gray-500 @enderror text-sm">
                                    @error('bukti') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-black font-bold mb-2">Catatan</label>
                                <textarea name="catatan" rows="4" placeholder="Detail kegiatan..." 
                                          class="w-full px-4 py-3 rounded-lg border-2 border-gray-500">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="text-center pt-6">
                                <button type="submit" class="bg-white hover-gray-400 text-black font-bold text-l px-16 py-5 rounded-xl shadow-2xl transform hover:scale-105 border border-gray-500 transition-all">
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