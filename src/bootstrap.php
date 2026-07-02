<?php

declare(strict_types=1);

const BASE_PATH = __DIR__.'/..';

function getBaseUrl(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $scriptName = preg_replace('#/+(index\.php)?$#', '', $scriptName);
    $baseUrl = rtrim($scriptName, '/');
    return $baseUrl === '' ? '' : $baseUrl;
}

define('BASE_URL', getBaseUrl());

function loadEnv(string $file): void
{
    if (!is_file($file)) {
        return;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv($key.'='.$value);
        }
    }
}

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

function jsonResponse(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($contentType, 'application/json')) {
        $decoded = json_decode(file_get_contents('php://input') ?: '{}', true);
        return is_array($decoded) ? $decoded : [];
    }
    return $_POST;
}

function requireAdmin(): void
{
    if (empty($_SESSION['admin_id'])) {
        jsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
    }
}

function cleanString(array $data, string $key, bool $required = false, int $max = 0): string
{
    $value = trim((string) ($data[$key] ?? ''));
    if ($required && $value === '') {
        throw new InvalidArgumentException(ucfirst($key).' wajib diisi.');
    }
    if ($max > 0 && mb_strlen($value) > $max) {
        throw new InvalidArgumentException(ucfirst($key)." maksimal {$max} karakter.");
    }
    return $value;
}

function uploadImage(string $field, int $maxBytes, array $allowedMimes, string $prefix = ''): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > $maxBytes) {
        throw new InvalidArgumentException('File gagal diunggah atau ukurannya terlalu besar.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowedMimes[$mime])) {
        throw new InvalidArgumentException('Format gambar tidak didukung.');
    }

    $directory = BASE_PATH.'/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Folder upload tidak dapat dibuat.');
    }
    $filename = $prefix.time().'-'.bin2hex(random_bytes(5)).'.'.$allowedMimes[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory.'/'.$filename)) {
        throw new RuntimeException('Gambar gagal disimpan.');
    }
    return (BASE_URL === '' ? '' : BASE_URL).'/uploads/'.$filename;
}

loadEnv(BASE_PATH.'/.env');

session_name('nusahomelab_session');
session_set_cookie_params([
    'lifetime' => 28800,
    'path' => BASE_URL ?: '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

require_once __DIR__.'/Database.php';
