<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();

            // FK: personel_id → nrp (string) di tabel personel
            $table->string('personel_id');

            // FK: renmin_id & pimpinan_id → unsignedBigInteger
            $table->unsignedBigInteger('renmin_id');
            $table->unsignedBigInteger('pimpinan_id');

            // FK: kode_cuti → int di tabel cuti
            $table->integer('kode_cuti');

            $table->string('pengikut')->nullable();
            $table->string('pergi_dari');
            $table->date('mulai_tgl');
            $table->date('sampai_tgl');
            $table->string('tujuan');
            $table->string('transportasi');
            $table->text('catatan')->nullable();
            $table->string('namaFile_bukti')->nullable();
            $table->string('pathFile_bukti')->nullable();
            $table->string('status')->default('Proses'); // pending, disetujui, ditolak

            $table->timestamps();

            // FOREIGN KEYS YANG BENAR (NO ERROR 150!)
            $table->foreign('personel_id')
                  ->references('nrp')
                  ->on('personel')
                  ->onDelete('cascade');

            $table->foreign('renmin_id')
                  ->references('kode_renmin')
                  ->on('renmin')
                  ->onDelete('cascade');

            $table->foreign('pimpinan_id')
                  ->references('kode_pimpinan')
                  ->on('pimpinan')
                  ->onDelete('cascade');

            $table->foreign('kode_cuti')
                  ->references('kode_cuti')
                  ->on('cuti')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};