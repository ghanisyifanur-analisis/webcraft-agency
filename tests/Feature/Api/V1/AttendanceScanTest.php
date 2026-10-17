<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScanTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
    private function getAuthToken(): string
    {
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'login' => 'guru@sekolah.sch.id',
            'password' => 'password123',
        ]);

        return $loginRes->json('data.token');
    }

    public function test_can_scan_kartu_pelajar_qr_successfully(): void
    {
        $token = $this->getAuthToken();

        // Plain token seeded in database for Ahmad Fauzan
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/scans', [
                'card_token' => 'KP_20261001_TELADAN',
                'idempotency_key' => 'scan_test_' . uniqid(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'hadir')
            ->assertJsonPath('data.student.name', 'Ahmad Fauzan');
    }

    public function test_scan_is_idempotent_and_prevents_duplicate_records(): void
    {
        $token = $this->getAuthToken();

        // 1st scan
        $first = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/scans', [
                'card_token' => 'KP_20261001_TELADAN',
                'idempotency_key' => 'scan_test_1',
            ]);
        $first->assertStatus(201);

        // 2nd scan of the same card on the same day (should be idempotent)
        $second = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/scans', [
                'card_token' => 'KP_20261001_TELADAN',
                'idempotency_key' => 'scan_test_2',
            ]);

        $second->assertStatus(200)
            ->assertJsonPath('message', 'Siswa sudah tercatat hadir sebelumnya.');
    }

    public function test_invalid_qr_token_returns_not_found(): void
    {
        $token = $this->getAuthToken();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/scans', [
                'card_token' => 'INVALID_FORGED_TOKEN',
            ]);

        $response->assertStatus(404);
    }
}
