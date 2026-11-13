<?php
// app/Helpers/Security.php
namespace App\Helpers;

class Security
{
    /**
     * Sanitasi teks untuk mencegah Cross-Site Scripting (XSS)
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Hasilkan atau ambil token Anti-CSRF untuk sesi aktif
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi token Anti-CSRF dengan timing-safe comparison
     */
    public static function validateCsrf(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Output input hidden CSRF untuk formulir HTML
     */
    public static function csrfField(): string
    {
        $token = self::csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Hash password dengan algoritma Bcrypt (cost 12)
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifikasi kesesuaian password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate unique Order Code: AKS-YYYYMMDD-XXXX
     */
    public static function generateOrderCode(): string
    {
        $date = date('Ymd');
        $random = strtoupper(bin2hex(random_bytes(2)));
        return "AKS-{$date}-{$random}";
    }

    /**
     * Sanitasi input string umum
     */
    public static function sanitize(string $input): string
    {
        return trim(strip_tags($input));
    }
}
