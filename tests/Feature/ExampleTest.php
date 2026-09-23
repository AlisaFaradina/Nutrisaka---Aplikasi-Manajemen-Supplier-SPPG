<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Saat belum berlisensi, mengarahkan ke halaman registrasi token
        $response = $this->get('/');
        $response->assertRedirect('/register-token');

        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertRedirect('/register-token');

        // Setelah berlisensi, dashboard dapat diakses normal
        app(\App\Services\LicenseService::class)->activate('NTRS-DEMO-2026-DEV1', [
            'supplier_name' => 'CV Mitra Sehat',
            'pic_name' => 'Budi',
            'supplier_phone' => '0812345678',
            'pin' => '1234',
        ]);

        $licensedResponse = $this->get('/dashboard');
        $licensedResponse->assertStatus(200);
    }
}
