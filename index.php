<?php
/**
 * Entry Point Pengalihan untuk XAMPP Apache
 * Mengarahkan akses dari root folder langsung ke direktori public/
 */
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'public/' . ($queryString ? '?' . $queryString : '');
header("Location: $target");
exit;
