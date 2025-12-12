<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CutiSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        DB::table('cuti')->insert([
            [
                'jenis_cuti' => 'Cuti Tahunan',
                'deskripsi' => 'Cuti tahunan diberikan setiap tahun kepada personel untuk keperluan pribadi.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jatah' => 10,
            ],
            [
                'jenis_cuti' => 'Cuti Sakit',
                'deskripsi' => 'Cuti yang diberikan kepada personel yang tidak dapat bekerja karena alasan kesehatan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jatah' => 10,
            ],
            [
                'jenis_cuti' => 'Cuti Melahirkan',
                'deskripsi' => 'Cuti yang diberikan kepada personel wanita dalam masa persalinan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jatah' => 10,
            ],
            [
                'jenis_cuti' => 'Cuti Besar',
                'deskripsi' => 'Cuti istimewa yang diberikan setelah periode kerja tertentu untuk kepentingan pribadi.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jatah' => 10,
            ],
            [
                'jenis_cuti' => 'Cuti Karena Alasan Penting',
                'deskripsi' => 'Diberikan untuk keperluan mendesak seperti keluarga sakit, kematian, atau urusan penting lainnya.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'jatah' => 10,
            ],
        ]);
    }
}
