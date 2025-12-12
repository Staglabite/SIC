<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pimpinan', function (Blueprint $table) {
            $table->unsignedBigInteger('kode_pimpinan')->primary();
            $table->unsignedBigInteger('kode_satker');
            $table->string('nama');
            $table->string('nrp',8);
            $table->string('jabatan');
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();

            $table->foreign('kode_satker')
                  ->references('kode_satker')
                  ->on('satker')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pimpinan');
    }
};