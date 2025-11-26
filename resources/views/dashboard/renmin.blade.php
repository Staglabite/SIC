{{-- resources/views/dashboard/renmin.blade.php --}}
{{-- DASHBOARD RENMIN – STATISTIK LENGKAP --}}
@extends('layouts.app')
@section('title', 'Dashboard Renmin - Statistik Pengajuan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-10">
    <div class="max-w-7xl mx-auto px-4">

        <!-- HEADER -->
        <div class="bg-white rounded-3xl shadow-2xl border-4 border-indigo-300 p-10 mb-12 text-center">
            <h1 class="text-5xl font-bold text-indigo-800 flex items-center justify-center gap-5">
                <i class="fas fa-user-shield text-yellow-500 text-6xl"></i>
                DASHBOARD RENMIN
            </h1>
            <p class="text-2xl text-gray-700 mt-4">Statistik Pengajuan Cuti & Izin Personel</p>
        </div>

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div class="bg-gradient-to-br from-purple-600 to-purple-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Cuti</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_cuti'] }}</p>
                        <p class="text-purple-200 text-xs mt-2">Semua waktu</p>
                    </div>
                    <i class="fas fa-calendar-times text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Izin</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_izin'] }}</p>
                        <p class="text-blue-200 text-xs mt-2">Semua waktu</p>
                    </div>
                    <i class="fas fa-id-card text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-600 to-emerald-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total 2025</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_tahun_ini'] }}</p>
                        <p class="text-green-200 text-xs mt-2">Cuti + Izin</p>
                    </div>
                    <i class="fas fa-chart-line text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-600 to-red-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Belum Divalidasi</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['belum_divalidasi'] }}</p>
                        <p class="text-red-200 text-xs mt-2">Butuh tindakan segera</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-7xl opacity-30"></i>
                </div>
            </div>
        </div>

        <!-- GRAFIK BULANAN -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 border-4 border-indigo-200">
            <h3 class="text-3xl font-bold text-indigo-800 mb-8 text-center">Pengajuan per Bulan Tahun 2025</h3>
            <canvas id="chartPengajuan" height="120"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartPengajuan'), {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [
            {
                label: 'Cuti',
                data: @json($stats['bulanan_cuti']),
                backgroundColor: 'rgba(147, 51, 234, 0.85)',
                borderRadius: 8
            },
            {
                label: 'Izin',
                data: @json($stats['bulanan_izin']),
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { display: false } } }
    }
});
</script>
@endsection