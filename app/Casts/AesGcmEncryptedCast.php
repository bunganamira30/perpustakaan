<?php

namespace App\Casts;

use App\Services\Security\AesGcmEncryptionService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AesGcmEncryptedCast implements CastsAttributes
{
    /**
     * Cast the given value from database storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $aad = $this->resolveAad($model, $attributes);

        /** @var AesGcmEncryptionService $service */
        $service = app(AesGcmEncryptionService::class);

        return $service->safeDecrypt($value, $aad);
    }

    /**
     * Prepare the given value for database storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // If already encrypted envelope, do not re-encrypt
        if (is_string($value) && str_starts_with($value, 'v1:')) {
            return $value;
        }

        $aad = $this->resolveAad($model, $attributes);

        /** @var AesGcmEncryptionService $service */
        $service = app(AesGcmEncryptionService::class);

        return $service->encrypt((string) $value, $aad);
    }

    /**
     * Resolve Additional Authenticated Data (AAD) bound to the user.
     *
     * @param  Model  $model
     * @param  array<string, mixed>  $attributes
     * @return string
     */
    protected function resolveAad(Model $model, array $attributes): string
    {
        $userId = $attributes['user_id'] ?? ($model->user_id ?? null);
        return $userId !== null ? 'user:' . $userId : '';
    }
}
