<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Relasi dengan pengguna dan wilayah
            $table->foreignId('reporter_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('area_id')
                ->constrained('areas')
                ->restrictOnDelete();

            // Informasi kejadian
            $table->dateTime('incident_at');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('condition', 150);
            $table->text('description');

            // Status proses laporan
            $table->string('status', 20)
                ->default('baru')
                ->index();

            // Bukti pendukung laporan
            $table->string('evidence_path')->nullable();

            $table->timestamps();

            // Mendukung pencarian laporan berdasarkan area dan status
            $table->index(['area_id', 'status']);

            // Mendukung pencarian laporan milik pengguna
            $table->index(['reporter_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
