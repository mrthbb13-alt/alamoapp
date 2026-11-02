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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 30)->unique()->index();
            $table->string('category', 50)->index();
            $table->string('department', 100);
            $table->string('priority', 20)->default('media');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('colonia', 100)->index();
            $table->string('address_reference', 255)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('citizen_name', 100)->default('Ciudadano Anónimo');
            $table->string('citizen_phone', 30)->nullable();
            $table->string('evidence_initial')->nullable();
            $table->string('evidence_resolution')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->enum('status', ['pendiente', 'en_proceso', 'resuelto', 'rechazado'])->default('pendiente')->index();
            $table->unsignedInteger('upvotes')->default(1);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
