<?php
// app/Models/Product.php
namespace App\Models;

use App\Config\Database;
use PDO;

class Product
{
    public static function all(array $filters = []): array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.badge_color 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";
        $params = [];

        // Filter berdasarkan pencarian nama produk
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        // Filter kategori
        if (!empty($filters['category'])) {
            $sql .= " AND (c.slug = :category OR c.id = :category_id)";
            $params[':category'] = $filters['category'];
            $params[':category_id'] = $filters['category'];
        }

        // Filter rentang harga minimal
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = (float)$filters['min_price'];
        }

        // Filter rentang harga maksimal
        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && $filters['max_price'] > 0) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }

        // Filter stok tersedia
        if (!empty($filters['in_stock_only'])) {
            $sql .= " AND p.stock > 0";
        }

        // Filter produk unggulan
        if (!empty($filters['featured_only'])) {
            $sql .= " AND p.is_featured = 1";
        }

        // Pengurutan
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.stock ASC, p.id DESC";
                break;
            case 'stock_low':
                $sql .= " ORDER BY p.stock ASC";
                break;
            case 'stock_high':
                $sql .= " ORDER BY p.stock DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.id ASC";
                break;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function featured(int $limit = 9): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.badge_color 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.is_active = 1 AND p.is_featured = 1 
                               ORDER BY p.id ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function checkStock(int $id, int $quantity): bool
    {
        $product = self::find($id);
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }
        return true;
    }

    public static function reduceStock(int $id, int $quantity, PDO $pdo): bool
    {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity");
        $stmt->execute([
            ':quantity' => $quantity,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Admin: Update Stok Produk secara presisi
     */
    public static function updateStock(int $id, int $newStock): bool
    {
        $pdo = Database::getConnection();
        $newStock = max(0, $newStock);
        $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $success = $stmt->execute([
            ':stock' => $newStock,
            ':id' => $id
        ]);

        // Jika produk merupakan bagian dari produk_budidaya, sinkronkan juga
        try {
            $stmtBudidaya = $pdo->prepare("UPDATE produk_budidaya SET stock = :stock WHERE product_id = :id");
            $stmtBudidaya->execute([':stock' => $newStock, ':id' => $id]);
        } catch (\Throwable $e) {
            // ignore jika tabel tidak ada
        }

        return $success;
    }

    /**
     * Admin: Tambah / Kurang Stok secara cepat (+/- delta)
     */
    public static function adjustStock(int $id, int $delta): array
    {
        $pdo = Database::getConnection();
        $product = self::find($id);
        if (!$product) {
            return ['success' => false, 'error' => 'Produk tidak ditemukan'];
        }

        $newStock = max(0, (int)$product['stock'] + $delta);
        $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $stmt->execute([
            ':stock' => $newStock,
            ':id' => $id
        ]);

        try {
            $stmtBudidaya = $pdo->prepare("UPDATE produk_budidaya SET stock = :stock WHERE product_id = :id");
            $stmtBudidaya->execute([':stock' => $newStock, ':id' => $id]);
        } catch (\Throwable $e) {
            // ignore
        }

        return [
            'success' => true,
            'id' => $id,
            'name' => $product['name'],
            'previous_stock' => (int)$product['stock'],
            'current_stock' => $newStock
        ];
    }
}
