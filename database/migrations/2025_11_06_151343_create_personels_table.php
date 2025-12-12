<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personel', function (Blueprint $table) {
            $table->string('nrp', 8)->primary();
            $table->unsignedBigInteger('kode_satker');
            $table->string('password');
            $table->string('name');
            $table->string('pangkat');
            $table->string('golongan');
            $table->string('jabatan');
            $table->string('role');
            $table->timestamps();

            $table->foreign('kode_satker')
                  ->references('kode_satker')
                  ->on('satker')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel');
    }
};