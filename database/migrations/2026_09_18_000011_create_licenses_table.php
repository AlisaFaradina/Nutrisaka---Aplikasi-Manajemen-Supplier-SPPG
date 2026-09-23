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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('token_masked', 32); // Contoh: NTRS-••••-••••-4PLD
            $table->text('license_payload');    // JSON payload terenkripsi/terstruktur
            $table->text('signature');          // Tanda tangan kriptografis dari server lisensi
            $table->text('public_key')->nullable(); // Kunci publik verifikator
            $table->string('device_fingerprint', 64)->index(); // Hash unik perangkat fisik
            $table->string('status', 20)->default('active');   // active, grace, locked
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamp('last_validated_at')->nullable();
            $table->timestamp('grace_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
