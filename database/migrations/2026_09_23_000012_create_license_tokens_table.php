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
        Schema::create('license_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 32)->unique()->index(); // Contoh: NTRS-7K2M-Q9XA-4PLD
            $table->string('status', 20)->default('unused')->index(); // unused, active, revoked
            $table->string('device_fingerprint', 64)->nullable()->index(); // Hash unik hardware perangkat terikat
            $table->string('device_name', 100)->nullable(); // Info tipe/nama perangkat (Windows/Android)
            $table->string('supplier_name', 255)->nullable(); // Nama Usaha / Perusahaan Supplier
            $table->string('pic_name', 255)->nullable(); // Penanggung Jawab
            $table->string('contact', 100)->nullable(); // WhatsApp / Email
            $table->text('notes')->nullable(); // Catatan admin (misal: "Pembelian lisensi ke-2 perangkat gudang")
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_tokens');
    }
};
