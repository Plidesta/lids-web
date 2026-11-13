<?php
// app/Helpers/Response.php
namespace App\Helpers;

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(string $message, array $extra = [], int $statusCode = 200): void
    {
        self::json(array_merge([
            'status' => 'success',
            'message' => $message
        ], $extra), $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $extra = []): void
    {
        self::json(array_merge([
            'status' => 'error',
            'message' => $message
        ], $extra), $statusCode);
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
