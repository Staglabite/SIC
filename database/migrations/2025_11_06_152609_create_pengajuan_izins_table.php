<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuanizin', function (Blueprint $table) {
            $table->id();

            $table->string('personel_id');

            $table->unsignedBigInteger('renmin_id');
            $table->unsignedBigInteger('pimpinan_id');

            // Kolom lain
            $table->string('keperluan');
            $table->string('pengikut');
            $table->string('pergi_dari');
            $table->string('tujuan');
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->string('transportasi');
            $table->text('catatan', 50)->nullable();
            $table->string('namaFile_bukti');
            $table->string('pathFile_bukti');
            $table->string('status')->default('Proses');

            $table->timestamps();

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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuanizin');
    }
};