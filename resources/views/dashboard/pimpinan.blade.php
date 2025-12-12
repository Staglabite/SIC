{{-- resources/views/dashboard/pimpinan.blade.php --}}
{{-- DASHBOARD PIMPINAN – STATISTIK LENGKAP --}}
@extends('layouts.app')
@section('title', 'Dashboard Pimpinan - Statistik Pengajuan')

@section('content')
<div class="min-h-screen bg-gray-50 py-2">
    <div class="max-w-7xl mx-auto px-4">

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium">Total Cuti</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_cuti'] }}</p>
                        <p class="text-indigo-200 text-xs mt-2">Semua waktu</p>
                    </div>
                    <i class="fas fa-calendar-times text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-teal-600 to-teal-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-sm font-medium">Total Izin</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_izin'] }}</p>
                        <p class="text-teal-200 text-xs mt-2">Semua waktu</p>
                    </div>
                    <i class="fas fa-id-card text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Total 2025</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['total_tahun_ini'] }}</p>
                        <p class="text-emerald-200 text-xs mt-2">Cuti + Izin</p>
                    </div>
                    <i class="fas fa-chart-line text-7xl opacity-30"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-rose-600 to-rose-800 text-white rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-rose-100 text-sm font-medium">Belum Disetujui</p>
                        <p class="text-5xl font-bold mt-3">{{ $stats['belum_divalidasi'] }}</p>
                        <p class="text-rose-200 text-xs mt-2">Menunggu persetujuan Anda</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-7xl opacity-30"></i>
                </div>
            </div>
        </div>

        <!-- GRAFIK BULANAN -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
            <h3 class="text-3xl font-bold text-gray-800 mb-8 text-center">
                Statistik Pengajuan per Bulan Tahun 2025
            </h3>
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
                backgroundColor: 'rgba(79, 70, 229, 0.85)',  // Indigo
                borderRadius: 8
            },
            {
                label: 'Izin',
                data: @json($stats['bulanan_izin']),
                backgroundColor: 'rgba(13, 148, 136, 0.85)', // Teal
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { position: 'top' },
            title: {
                display: true,
                text: 'Tren Pengajuan Cuti & Izin 2025',
                font: { size: 16 }
            }
        },
        scales: { 
            y: { 
                beginAtZero: true, 
                grid: { display: false } 
            } 
        }
    }
});
</script>
@endsection