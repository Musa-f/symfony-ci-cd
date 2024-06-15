<?php

namespace App\Service;

use Firebase\JWT\JWT;

class TokenGeneratorService
{
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}