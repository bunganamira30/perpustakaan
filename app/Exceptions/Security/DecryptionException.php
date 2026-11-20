<?php

namespace App\Exceptions\Security;

use RuntimeException;

class DecryptionException extends RuntimeException
{
    // Custom exception thrown when AES-256-GCM authentication or decryption fails
}
