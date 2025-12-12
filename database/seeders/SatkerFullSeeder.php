<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SatkerFullSeeder extends Seeder
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
        // 1. SATKER (5 data)
        // ========================================
        DB::table('satker')->insert([
            ['kode_satker' => 0, 'name' => 'Admin',    'deskripsi' => 'Admin SIC','created_at' => now(), 'updated_at' => now()],
            ['kode_satker' => 110001, 'name' => 'Satker 1',       'deskripsi' => 'Kantor Pusat Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['kode_satker' => 120002, 'name' => 'Satker 2',     'deskripsi' => 'Satker Jawa Barat',    'created_at' => now(), 'updated_at' => now()],
            ['kode_satker' => 130003, 'name' => 'Satker 3',    'deskripsi' => 'Satker Jawa Timur',    'created_at' => now(), 'updated_at' => now()],
            ['kode_satker' => 140004, 'name' => 'Satker 4',       'deskripsi' => 'Satker Sumatera Utara','created_at' => now(), 'updated_at' => now()],
            ['kode_satker' => 150005, 'name' => 'Satker 5',    'deskripsi' => 'Satker Sulawesi Selatan','created_at' => now(), 'updated_at' => now()],
        ]);

        // ========================================
        // 2. PIMPINAN (1 per satker)
        // ========================================
        DB::table('pimpinan')->insert([
            // Satker 110001 - Jakarta
            ['kode_pimpinan' => 11000101, 'kode_satker' => 110001, 'nama' => 'Kolonel Budi Santoso',        'nrp' => '12345678', 'jabatan' => 'Kepala Satker 1', 'username' => 'pim1',   'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker 120002 - Bandung
            ['kode_pimpinan' => 12000201, 'kode_satker' => 120002, 'nama' => 'Letkol Rina Wijayanti',      'nrp' => '22345678', 'jabatan' => 'Kepala Satker 2', 'username' => 'pim2', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker 130003 - Surabaya
            ['kode_pimpinan' => 13000301, 'kode_satker' => 130003, 'nama' => 'Kolonel Hendra Gunawan',     'nrp' => '32345678', 'jabatan' => 'Kepala Satker 3', 'username' => 'pim3', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker 140004 - Medan
            ['kode_pimpinan' => 14000401, 'kode_satker' => 140004, 'nama' => 'Letkol Ahmad Fauzi',         'nrp' => '42345678', 'jabatan' => 'Kepala Satker 4', 'username' => 'pim4',    'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker 150005 - Makassar
            ['kode_pimpinan' => 15000501, 'kode_satker' => 150005, 'nama' => 'Kolonel Siti Nurhaliza',     'nrp' => '52345678', 'jabatan' => 'Kepala Satker 5', 'username' => 'pim5', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========================================
        // 3. RENMIN (1 per satker)
        // ========================================
        DB::table('renmin')->insert([
            ['kode_renmin' => 11000199, 'kode_satker' => 110001, 'username' => 'renmin1', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['kode_renmin' => 12000299, 'kode_satker' => 120002, 'username' => 'renmin2', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['kode_renmin' => 13000399, 'kode_satker' => 130003, 'username' => 'renmin3', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['kode_renmin' => 14000499, 'kode_satker' => 140004, 'username' => 'renmin4', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['kode_renmin' => 15000599, 'kode_satker' => 150005, 'username' => 'renmin5', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========================================
        // 4. PERSONEL (2 per satker = total 10)
        // ========================================
        DB::table('personel')->insert([
            // Satker Jakarta (110001)
            ['nrp' => '11000101', 'kode_satker' => 110001, 'name' => 'Agus Suprapto',     'pangkat' => 'Serma',  'golongan' => 'III/b', 'jabatan' => 'Kasubag Umum',     'role' => 'user', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['nrp' => '11000102', 'kode_satker' => 110001, 'name' => 'Dewi Lestari',      'pangkat' => 'Serda',  'golongan' => 'II/c',  'jabatan' => 'Staf Keuangan',    'role' => 'user',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker Bandung (120002)
            ['nrp' => '12000201', 'kode_satker' => 120002, 'name' => 'Joko Widodo',       'pangkat' => 'Koptu',  'golongan' => 'III/a', 'jabatan' => 'Kasubag Logistik', 'role' => 'user', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['nrp' => '12000202', 'kode_satker' => 120002, 'name' => 'Rina Amelia',       'pangkat' => 'Pratu',  'golongan' => 'II/a',  'jabatan' => 'Staf Administrasi','role' => 'user',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker Surabaya (130003)
            ['nrp' => '13000301', 'kode_satker' => 130003, 'name' => 'Budi Hartono',      'pangkat' => 'Serka',  'golongan' => 'III/c', 'jabatan' => 'Kasubag Personel', 'role' => 'user', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['nrp' => '13000302', 'kode_satker' => 130003, 'name' => 'Santi Wulandari',   'pangkat' => 'Kopda',  'golongan' => 'II/d',  'jabatan' => 'Staf Umum',        'role' => 'user',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker Medan (140004)
            ['nrp' => '14000401', 'kode_satker' => 140004, 'name' => 'Rahman Siregar',    'pangkat' => 'Sertu',  'golongan' => 'III/b', 'jabatan' => 'Kasubag Keuangan', 'role' => 'user', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['nrp' => '14000402', 'kode_satker' => 140004, 'name' => 'Putri Ayu',         'pangkat' => 'Prada',  'golongan' => 'II/a',  'jabatan' => 'Staf Logistik',    'role' => 'user',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Satker Makassar (150005)
            ['nrp' => '15000501', 'kode_satker' => 150005, 'name' => 'Andi Mallarangeng', 'pangkat' => 'Kopda', 'golongan' => 'III/a', 'jabatan' => 'Kasubag Umum',    'role' => 'user', 'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
            ['nrp' => '15000502', 'kode_satker' => 150005, 'name' => 'Purnama Sari Daeng','pangkat' => 'Pratu',  'golongan' => 'II/b',  'jabatan' => 'Staf Administrasi','role' => 'user',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],

            // Admin
            ['nrp' => 'ADMINSIC', 'kode_satker' => 0, 'name' => 'Purnama Sari Daeng','pangkat' => 'Pratu',  'golongan' => 'II/b',  'jabatan' => 'Staf Administrasi','role' => 'admin',  'password' => Hash::make('1234'), 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('Seeder Manual Selesai! Semua password = 1234');
    }
}