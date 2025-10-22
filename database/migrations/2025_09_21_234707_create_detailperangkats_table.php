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
        Schema::create('detailperangkats', function (Blueprint $table) {
            $table->id();

            $table->string('namaperangkat');
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();

            // relasi ke jenis perangkat
            $table->unsignedBigInteger('jenisperangkat_id');
            $table->foreign('jenisperangkat_id')->references('id')->on('jenisperangkats')->onDelete('cascade');

            // relasi ke perangkat
            $table->unsignedBigInteger('perangkat_id');
            $table->foreign('perangkat_id')->references('id')->on('perangkats')->onDelete('cascade');

            $table->enum('status',[0,1])->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailperangkats');
    }
};
