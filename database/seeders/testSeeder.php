<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class testSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // sementara matikan biar aman
        DB::table('personel')->truncate();
        DB::table('pimpinan')->truncate();
        DB::table('renmin')->truncate();
        DB::table('satker')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================
        // 4. PERSONEL (2 per satker = total 10)
        // ========================================
        DB::table('personel')->insert([
            // Admin
            ['nrp' => '11111111', 'kode_satker' => 0, 'name' => 'Administrator','pangkat' => '-',  'golongan' => '-',  'jabatan' => '-','role' => '2',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('Seeder Manual Selesai! Semua password = 1234');
    }
}