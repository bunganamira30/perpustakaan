<?php

namespace App\Services\Security;

use App\Exceptions\Security\DecryptionException;
use InvalidArgumentException;
use RuntimeException;

class AesGcmEncryptionService
{
    /**
     * Cipher algorithm constant.
     */
    protected const CIPHER = 'aes-256-gcm';

    /**
     * Nonce size in bytes (NIST SP 800-38D recommended for GCM).
     */
    protected const NONCE_LENGTH = 12;

    /**
     * Authentication tag size in bytes (128-bit integrity tag).
     */
    protected const TAG_LENGTH = 16;

    /**
     * Version prefix for envelope.
     */
    protected const VERSION_PREFIX = 'v1';

    /**
     * 32-byte raw binary key.
     */
    protected string $binaryKey;

    /**
     * Create a new AesGcmEncryptionService instance.
     *
     * @param  string|null  $key  Hexadecimal 64-char key or raw 32-byte key
     * @throws InvalidArgumentException
     */
    public function __construct(?string $key = null)
    {
        $rawKey = $key ?? config('security.phone_key') ?? env('APP_PHONE_ENCRYPTION_KEY');

        if (empty($rawKey)) {
            throw new InvalidArgumentException(
                'APP_PHONE_ENCRYPTION_KEY is not configured. Please set a 256-bit (64-character hex) key in .env.'
            );
        }

        if (ctype_xdigit($rawKey) && strlen($rawKey) === 64) {
            $this->binaryKey = hex2bin($rawKey);
        } elseif (strlen($rawKey) === 32) {
            $this->binaryKey = $rawKey;
        } elseif (str_starts_with($rawKey, 'base64:')) {
            $this->binaryKey = base64_decode(substr($rawKey, 7));
        } else {
            throw new InvalidArgumentException(
                'APP_PHONE_ENCRYPTION_KEY must be a 64-character hexadecimal string representing a 256-bit key.'
            );
        }

        if (strlen($this->binaryKey) !== 32) {
            throw new InvalidArgumentException(
                'Decoded phone encryption key must be exactly 32 bytes (256 bits).'
            );
        }
    }

    /**
     * Encrypt plaintext using AES-256-GCM with a fresh 12-byte nonce.
     *
     * @param  string  $plaintext
     * @param  string  $aad  Additional Authenticated Data bound to entity
     * @return string  Versioned envelope: v1:<b64_nonce>:<b64_ciphertext>:<b64_tag>
     * @throws RuntimeException
     */
    public function encrypt(string $plaintext, string $aad = ''): string
    {
        $nonce = random_bytes(self::NONCE_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->binaryKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad,
            self::TAG_LENGTH
        );

        if ($ciphertext === false || strlen($tag) !== self::TAG_LENGTH) {
            throw new RuntimeException('Failed to encrypt data using AES-256-GCM cipher.');
        }

        return sprintf(
            '%s:%s:%s:%s',
            self::VERSION_PREFIX,
            base64_encode($nonce),
            base64_encode($ciphertext),
            base64_encode($tag)
        );
    }

    /**
     * Decrypt a versioned envelope using AES-256-GCM and verify integrity tag.
     *
     * @param  string  $envelope
     * @param  string  $aad  Additional Authenticated Data bound to entity
     * @return string  Original plaintext
     * @throws DecryptionException
     */
    public function decrypt(string $envelope, string $aad = ''): string
    {
        if (!str_starts_with($envelope, self::VERSION_PREFIX . ':')) {
            throw new DecryptionException(
                'Unsupported encryption envelope version: expected prefix "' . self::VERSION_PREFIX . ':".'
            );
        }

        $parts = explode(':', $envelope);
        if (count($parts) !== 4) {
            throw new DecryptionException(
                'Malformed encryption envelope: expected 4 segments (version:nonce:ciphertext:tag).'
            );
        }

        [, $b64Nonce, $b64Ciphertext, $b64Tag] = $parts;

        $nonce = base64_decode($b64Nonce, true);
        $ciphertext = base64_decode($b64Ciphertext, true);
        $tag = base64_decode($b64Tag, true);

        if ($nonce === false || strlen($nonce) !== self::NONCE_LENGTH) {
            throw new DecryptionException('Invalid nonce length: expected 12 bytes.');
        }

        if ($tag === false || strlen($tag) !== self::TAG_LENGTH) {
            throw new DecryptionException('Invalid authentication tag length: expected 16 bytes.');
        }

        if ($ciphertext === false) {
            throw new DecryptionException('Invalid ciphertext base64 encoding.');
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->binaryKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad
        );

        if ($plaintext === false) {
            throw new DecryptionException(
                'AEAD authentication tag verification failed: data has been tampered with or AAD mismatch.'
            );
        }

        return $plaintext;
    }

    /**
     * Fail-secure decryption that catches exceptions and returns a masked placeholder.
     *
     * @param  string  $envelope
     * @param  string  $aad
     * @return string
     */
    public function safeDecrypt(string $envelope, string $aad = ''): string
    {
        try {
            return $this->decrypt($envelope, $aad);
        } catch (\Throwable $e) {
            return '[DATA TERPROTEKSI: INTEGRITAS GAGAL]';
        }
    }
}
