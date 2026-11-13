<?php
// app/Models/User.php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Security;
use PDO;

class User
{
    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, full_name, email, phone, address, maps_url, role, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): ?int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, address, maps_url, password_hash, role, created_at) 
                               VALUES (:name, :email, :phone, :address, :maps_url, :password_hash, :role, CURRENT_TIMESTAMP)");
        $success = $stmt->execute([
            ':name' => Security::sanitize($data['full_name']),
            ':email' => strtolower(trim($data['email'])),
            ':phone' => Security::sanitize($data['phone'] ?? ''),
            ':address' => Security::sanitize($data['address'] ?? ''),
            ':maps_url' => Security::sanitize($data['maps_url'] ?? ''),
            ':password_hash' => Security::hashPassword($data['password']),
            ':role' => $data['role'] ?? 'customer'
        ]);

        return $success ? (int)$pdo->lastInsertId() : null;
    }

    public static function updateAddress(int $userId, string $address, ?string $mapsUrl = null): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET address = :address, maps_url = :maps_url WHERE id = :id");
        return $stmt->execute([
            ':id' => $userId,
            ':address' => Security::sanitize($address),
            ':maps_url' => Security::sanitize($mapsUrl ?? '')
        ]);
    }

    public static function updateProfile(int $userId, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET full_name = :name, phone = :phone, address = :address, maps_url = :maps_url WHERE id = :id");
        return $stmt->execute([
            ':id' => $userId,
            ':name' => Security::sanitize($data['full_name'] ?? ''),
            ':phone' => Security::sanitize($data['phone'] ?? ''),
            ':address' => Security::sanitize($data['address'] ?? ''),
            ':maps_url' => Security::sanitize($data['maps_url'] ?? '')
        ]);
    }

    public static function authenticate(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if (!$user) {
            return null;
        }

        if (Security::verifyPassword($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }

        return null;
    }
}
