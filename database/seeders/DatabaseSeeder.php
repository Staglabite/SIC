<?php
// database/seeders/DatabaseSeeder.php → VERSI FINAL AMAN TANPA TRUNCATE!

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Renmin;
use App\Models\Pimpinan;
use App\Models\Satker;
use App\Models\Personel;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // MATIKAN FOREIGN KEY CHECK (BIAR BISA HAPUS DATA)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // HAPUS DATA LAMA DENGAN DELETE (BUKAN TRUNCATE!)
        Renmin::query()->delete();
        Pimpinan::query()->delete();
        Satker::query()->delete();
        Personel::query()->delete();

        // NYALAKAN LAGI FOREIGN KEY
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // INSERT DATA BARU (PASTI MASUK!)
        Renmin::create([
            'kode_renmin' => 1,
            'username'    => 'renmin01',
            'password'    => Hash::make('renmin123'),
        ]);

        Pimpinan::create([
            'kode_pimpinan' => 1,
            'username'      => 'pimpinan01',
            'password'      => Hash::make('pimpinan123'),
        ]);

        Satker::create([
            'kode_satker'   => 1,
            'kode_renmin'   => 1,
            'kode_pimpinan' => 1,
            'name'          => 'Polresta Bandung',
            'deskripsi'     => 'Satuan Kerja Utama',
        ]);

        Personel::create([
            'nrp'       => '1234567890123456',
            'password'  => Hash::make('personel123'),
            'name'      => 'Budi Santoso',
            'pangkat'   => 'IPTU',
            'golongan'  => 'III/c',
            'jabatan'   => 'Kapolsek',
            'role'      => 'personel',
            'satker_id' => 1,
        ]);
    }
}