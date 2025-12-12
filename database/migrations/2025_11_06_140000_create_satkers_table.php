<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satker', function (Blueprint $table) {
            $table->bigIncrements('kode_satker'); // atau integer() + primary() jika mau int biasa
            $table->string('name');
            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satker');
    }
};