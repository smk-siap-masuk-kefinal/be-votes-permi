<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemilihs', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('qr_code')->nullable();
            $table->string('path_qr_code')->nullable();
            $table->string('url_qr_code')->nullable();
            $table->string('kode_logout')->nullable();
            $table->boolean('is_voted')->default(false);
            $table->boolean('is_logout')->default(false);
            $table->string('tps')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilihs');
    }
};
