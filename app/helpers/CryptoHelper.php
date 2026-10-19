<?php
namespace App\Helpers;

use Exception;

class CryptoHelper {
    private static function getKey(): string {
        $key = config('APP_KEY');
        if (empty($key)) {
            $key = 'c2VjdXJlX2FlczI1Nl9rZXlfcmVhbHR5c21hcnR6cGF0aHNoYWxhMjAyNg==';
        }
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        } else {
            $key = base64_decode($key);
        }
        return hash('sha256', $key, true);
    }

    /**
     * Encrypt a sensitive string using AES-256-CBC + HMAC-SHA256
     */
    public static function encrypt(?string $data): ?string {
        if ($data === null || $data === '') {
            return null;
        }

        $key = self::getKey();
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);

        $ciphertext = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

        return base64_encode($iv . $hmac . $ciphertext);
    }

    /**
     * Decrypt a string
     */
    public static function decrypt(?string $encrypted): ?string {
        if ($encrypted === null || $encrypted === '') {
            return null;
        }

        try {
            $raw = base64_decode($encrypted, true);
            if ($raw === false) return null;

            $key = self::getKey();
            $ivLength = openssl_cipher_iv_length('aes-256-cbc');
            $hmacLength = 32;

            if (strlen($raw) < ($ivLength + $hmacLength)) {
                return null;
            }

            $iv = substr($raw, 0, $ivLength);
            $hmac = substr($raw, $ivLength, $hmacLength);
            $ciphertext = substr($raw, $ivLength + $hmacLength);

            $calculatedHmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
            if (!hash_equals($hmac, $calculatedHmac)) {
                return null;
            }

            $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            return ($plaintext !== false) ? $plaintext : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Mask Aadhaar: 1234 5678 9012 -> XXXXXXXX9012
     */
    public static function maskAadhaar(?string $aadhaar): string {
        $clean = preg_replace('/\D/', '', $aadhaar ?? '');
        if (strlen($clean) < 4) {
            return '•••• •••• ••••';
        }
        $last4 = substr($clean, -4);
        return 'XXXX XXXX ' . $last4;
    }

    /**
     * Mask Bank Account: 123456789012 -> XXXXXXXX9012
     */
    public static function maskAccount(?string $account): string {
        $clean = preg_replace('/\D/', '', $account ?? '');
        if (strlen($clean) < 4) {
            return '••••••••';
        }
        $len = strlen($clean);
        $maskedLen = max(0, $len - 4);
        return str_repeat('X', min(8, $maskedLen)) . substr($clean, -4);
    }

    /**
     * Mask PAN: ABCDE1234F -> XXXXX1234F
     */
    public static function maskPan(?string $pan): string {
        $clean = strtoupper(trim($pan ?? ''));
        if (strlen($clean) === 10) {
            return 'XXXXX' . substr($clean, 5);
        }
        return $clean;
    }
}
