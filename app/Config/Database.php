<?php
// app/Config/Database.php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    /**
     * Muat file .env secara otomatis jika ada
     */
    private static function loadEnv(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $value = trim($value, '"\'');
                    if (!isset($_ENV[$key]) && !getenv($key)) {
                        putenv("{$key}={$value}");
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            self::loadEnv();

            $dbDriver = getenv('DB_DRIVER') ?: 'sqlite';
            $dbPath = dirname(__DIR__, 2) . '/database/aksara.sqlite';

            try {
                if ($dbDriver === 'sqlite') {
                    $isNewDatabase = !file_exists($dbPath);

                    $dbDir = dirname($dbPath);
                    if (!is_dir($dbDir)) {
                        @mkdir($dbDir, 0777, true);
                    }

                    self::$pdo = new PDO("sqlite:{$dbPath}");
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$pdo->exec("PRAGMA foreign_keys = ON;");

                    if ($isNewDatabase || filesize($dbPath) === 0) {
                        self::migrateAndSeed(self::$pdo);
                    } else {
                        self::ensureSchemaUpdated(self::$pdo);
                    }
                } else {
                    // Konfigurasi MySQL / MariaDB (Untuk cPanel / Production Hosting / XAMPP)
                    $host = getenv('DB_HOST') ?: '127.0.0.1';
                    $port = getenv('DB_PORT') ?: '3306';
                    $dbname = getenv('DB_DATABASE') ?: 'aksara_unsulbar';
                    $username = getenv('DB_USERNAME') ?: 'root';
                    $password = getenv('DB_PASSWORD') ?: '';

                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    self::$pdo = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]);
                }
            } catch (PDOException $e) {
                // Jika koneksi MySQL gagal di localhost/shared hosting, coba fallback otomatis ke SQLite
                if ($dbDriver !== 'sqlite' && file_exists($dbPath)) {
                    try {
                        self::$pdo = new PDO("sqlite:{$dbPath}");
                        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                        self::$pdo->exec("PRAGMA foreign_keys = ON;");
                        return self::$pdo;
                    } catch (PDOException $e2) {
                        // ignore
                    }
                }

                die("<div style='font-family: sans-serif; padding: 2rem; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; max-width: 600px; margin: 40px auto;'>"
                    . "<h3 style='color: #9f1239; margin-top: 0;'>Koneksi Database Gagal</h3>"
                    . "<p style='color: #4c0519; font-size: 14px;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>"
                    . "<p style='color: #881337; font-size: 12px;'>Silakan periksa pengaturan database pada file <code>.env</code> atau pastikan server MySQL/SQLite aktif.</p>"
                    . "</div>");
            }
        }

        return self::$pdo;
    }

    /**
     * Otomatis inisialisasi tabel dan data awal untuk pengujian instan
     */
    public static function migrateAndSeed(PDO $pdo): void
    {
        $baseDir = dirname(__DIR__, 2);
        $schemaPath = $baseDir . '/database/schema.sql';
        $seedPath = $baseDir . '/database/seed.sql';

        if (file_exists($schemaPath)) {
            $schemaSql = file_get_contents($schemaPath);
            $pdo->exec($schemaSql);
        }

        if (file_exists($seedPath)) {
            $seedSql = file_get_contents($seedPath);
            $pdo->exec($seedSql);
        }
    }

    /**
     * Memastikan kolom baru (service_fee, delivery_distance) selalu ada di tabel orders
     */
    public static function ensureSchemaUpdated(PDO $pdo): void
    {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $cols = array_column($pdo->query("PRAGMA table_info(orders)")->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (!in_array('service_fee', $cols, true)) {
                    $pdo->exec("ALTER TABLE orders ADD COLUMN service_fee DECIMAL(12, 2) DEFAULT 0;");
                }
                if (!in_array('delivery_distance', $cols, true)) {
                    $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_distance DECIMAL(8, 2) DEFAULT 5.0;");
                }
            } else {
                $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'service_fee'");
                if ($stmt->rowCount() === 0) {
                    $pdo->exec("ALTER TABLE orders ADD COLUMN service_fee DECIMAL(12, 2) DEFAULT 0 AFTER shipping_fee;");
                }
                $stmt2 = $pdo->query("SHOW COLUMNS FROM orders LIKE 'delivery_distance'");
                if ($stmt2->rowCount() === 0) {
                    $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_distance DECIMAL(8, 2) DEFAULT 5.0 AFTER delivery_lng;");
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau izin terbatas
        }
    }
}
