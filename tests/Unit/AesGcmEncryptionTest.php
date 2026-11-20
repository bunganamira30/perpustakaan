<?php

namespace Tests\Unit;

use App\Exceptions\Security\DecryptionException;
use App\Services\Security\AesGcmEncryptionService;
use PHPUnit\Framework\TestCase;

class AesGcmEncryptionTest extends TestCase
{
    protected AesGcmEncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Menggunakan kunci 256-bit (64 hex characters)
        $this->service = new AesGcmEncryptionService('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');
    }

    public function test_encrypts_plaintext_into_versioned_envelope_format(): void
    {
        $plaintext = '081234567890';
        $aad = 'member-101';

        $encrypted = $this->service->encrypt($plaintext, $aad);

        // Format amplop versi: v1:<nonce>:<ciphertext>:<tag>
        $this->assertStringStartsWith('v1:', $encrypted);
        $parts = explode(':', $encrypted);
        $this->assertCount(4, $parts);
        $this->assertEquals('v1', $parts[0]);
        // Nonce 12 byte di-base64-kan panjangnya 16 karakter
        $this->assertEquals(12, strlen(base64_decode($parts[1])));
        // Tag 16 byte di-base64-kan panjangnya 24 karakter (atau 22 + ==)
        $this->assertEquals(16, strlen(base64_decode($parts[3])));
    }

    public function test_encryption_generates_unique_12_byte_nonces_for_same_plaintext(): void
    {
        $plaintext = '081234567890';
        $aad = 'member-101';

        $enc1 = $this->service->encrypt($plaintext, $aad);
        $enc2 = $this->service->encrypt($plaintext, $aad);

        // Nonce acak baru pada setiap eksekusi menjamin ciphertext tidak pernah sama
        $this->assertNotEquals($enc1, $enc2);

        $nonce1 = explode(':', $enc1)[1];
        $nonce2 = explode(':', $enc2)[1];
        $this->assertNotEquals($nonce1, $nonce2);
    }

    public function test_decrypts_ciphertext_back_to_original_plaintext_with_matching_aad(): void
    {
        $plaintext = '081234567890';
        $aad = 'member-101';

        $encrypted = $this->service->encrypt($plaintext, $aad);
        $decrypted = $this->service->decrypt($encrypted, $aad);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function test_decryption_fails_when_aad_is_tampered(): void
    {
        $plaintext = '081234567890';
        $encrypted = $this->service->encrypt($plaintext, 'member-101');

        $this->expectException(DecryptionException::class);
        // Dekripsi menggunakan ID anggota lain (tampering AAD) wajib ditolak oleh AEAD
        $this->service->decrypt($encrypted, 'member-102');
    }

    public function test_decryption_fails_when_ciphertext_is_tampered(): void
    {
        $plaintext = '081234567890';
        $encrypted = $this->service->encrypt($plaintext, 'member-101');
        $parts = explode(':', $encrypted);
        // Bit-flip pada byte pertama ciphertext untuk mensimulasikan manipulasi data
        $rawCt = base64_decode($parts[2]);
        $rawCt[0] = chr(ord($rawCt[0]) ^ 0xFF);
        $parts[2] = base64_encode($rawCt);
        $tampered = implode(':', $parts);

        $this->expectException(DecryptionException::class);
        $this->service->decrypt($tampered, 'member-101');
    }

    public function test_safe_decrypt_returns_fallback_string_on_corruption(): void
    {
        $corrupted = 'v1:aW52YWxpZGFh:dGFtcGVyZWQ=:aW52YWxpZHRhZw==';
        $result = $this->service->safeDecrypt($corrupted, 'member-101');

        $this->assertStringContainsString('DATA TERPROTEKSI', $result);
    }
}
