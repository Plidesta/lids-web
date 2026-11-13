<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Config\App;
use App\Helpers\Response;
use App\Helpers\Security;
use App\Models\User;

class AuthController
{
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Security::validateCsrf($csrfToken)) {
            Response::error('Token keamanan tidak valid.', 403);
            return;
        }

        $email = Security::sanitize($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::error('Email dan kata sandi wajib diisi.', 422);
            return;
        }

        $user = User::authenticate($email, $password);
        if (!$user) {
            Response::error('Email atau kata sandi yang Anda masukkan salah.', 401);
            return;
        }

        // Regenerasi session ID untuk proteksi Session Fixation
        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        // Auto-set delivery address from user profile if exists
        if (!empty($user['address'])) {
            App::setDeliveryAddress([
                'recipient_name' => $user['full_name'],
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'],
                'maps_url' => $user['maps_url'] ?? '',
                'notes' => ''
            ]);
        }

        Response::success("Selamat datang kembali, {$user['full_name']}!", [
            'user' => $user
        ]);
    }

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Security::validateCsrf($csrfToken)) {
            Response::error('Token keamanan tidak valid.', 403);
            return;
        }

        $fullName = Security::sanitize($input['full_name'] ?? '');
        $email = Security::sanitize($input['email'] ?? '');
        $phone = Security::sanitize($input['phone'] ?? '');
        $address = Security::sanitize($input['address'] ?? '');
        $mapsUrl = Security::sanitize($input['maps_url'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($fullName) || empty($email) || empty($password)) {
            Response::error('Semua kolom bertanda bintang wajib diisi.', 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Format alamat email tidak valid.', 422);
            return;
        }

        if (strlen($password) < 6) {
            Response::error('Kata sandi minimal 6 karakter.', 422);
            return;
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            Response::error('Email tersebut sudah terdaftar. Silakan masuk.', 409);
            return;
        }

        $userId = User::create([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'maps_url' => $mapsUrl,
            'password' => $password,
            'role' => 'customer'
        ]);

        if (!$userId) {
            Response::error('Gagal mendaftarkan akun. Silakan coba beberapa saat lagi.', 500);
            return;
        }

        $user = User::find($userId);
        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        if (!empty($address)) {
            App::setDeliveryAddress([
                'recipient_name' => $fullName,
                'phone' => $phone,
                'address' => $address,
                'maps_url' => $mapsUrl,
                'notes' => ''
            ]);
        }

        Response::success("Pendaftaran berhasil! Selamat datang di Aksara Unsulbar.", [
            'user' => $user
        ]);
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        Response::redirect('?page=dashboard');
    }
}
