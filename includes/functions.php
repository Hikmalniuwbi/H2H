<?php
// Helper umum untuk aplikasi Hearts2Hearts

if (!function_exists('h')) {
    function h(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount): string {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}

if (!function_exists('formatDateIndo')) {
    function formatDateIndo(?string $datetime): string {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        if (!$timestamp) return $datetime;

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $day = date('j', $timestamp);
        $month = $months[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);

        return "$day $month $year";
    }
}

if (!function_exists('assetUrl')) {
    function assetUrl(string $path): string {
        return ltrim($path, '/');
    }
}
