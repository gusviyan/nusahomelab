<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = (string) env('DB_HOST', '127.0.0.1');
        $port = (int) env('DB_PORT', 3306);
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', (string) env('DB_NAME', 'nusahomelab'));
        $user = (string) env('DB_USER', 'root');
        $password = (string) env('DB_PASSWORD', '');
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $bootstrap = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $password, $options);
        $bootstrap->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $password, $options);
        self::initialize(self::$pdo);

        return self::$pdo;
    }

    private static function initialize(PDO $db): void
    {
        $db->exec('CREATE TABLE IF NOT EXISTS admins (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $db->exec('CREATE TABLE IF NOT EXISTS projects (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(255) NOT NULL,
            description TEXT NULL,
            year VARCHAR(20) NULL,
            image VARCHAR(500) NULL,
            link VARCHAR(500) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $db->exec('CREATE TABLE IF NOT EXISTS services (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            sort_order INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $db->exec('CREATE TABLE IF NOT EXISTS settings (
            `key` VARCHAR(100) PRIMARY KEY,
            `value` TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        self::seed($db);
    }

    private static function seed(PDO $db): void
    {
        if ((int) $db->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {
            $statement = $db->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
            $statement->execute([
                env('ADMIN_USERNAME', 'admin'),
                password_hash((string) env('ADMIN_PASSWORD', 'admin123'), PASSWORD_DEFAULT),
            ]);
        }

        if ((int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn() === 0) {
            $statement = $db->prepare('INSERT INTO projects (title,category,description,year,sort_order) VALUES (?,?,?,?,?)');
            foreach ([
                ['Nusa Essentials', 'E-commerce · Art Direction · Development', 'Pengalaman belanja untuk brand kebutuhan sehari-hari.', '2026', 1],
                ['Kala Studio', 'Brand Identity · Web Design', 'Identitas digital untuk studio arsitektur.', '2025', 2],
                ['Ruang Sehat', 'Product Design · Mobile Experience', 'Aplikasi kebugaran yang terasa personal.', '2025', 3],
            ] as $project) {
                $statement->execute($project);
            }
        }

        if ((int) $db->query('SELECT COUNT(*) FROM services')->fetchColumn() === 0) {
            $statement = $db->prepare('INSERT INTO services (title,description,sort_order) VALUES (?,?,?)');
            foreach ([
                ['Website Development', 'Website cepat, responsif, dan mudah dikelola dengan fondasi teknis yang solid.', 1],
                ['UI/UX Design', 'Antarmuka intuitif yang berangkat dari kebutuhan pengguna dan tujuan bisnis.', 2],
                ['Creative Direction', 'Arah visual konsisten agar brand punya karakter dan daya ingat yang kuat.', 3],
            ] as $service) {
                $statement->execute($service);
            }
        }

        $defaults = [
            'name' => 'Nusa HomeLab', 'role' => 'Creative Developer', 'location' => 'Jakarta',
            'email' => 'hello@nusahomelab.id', 'instagram' => '#', 'linkedin' => '#', 'logo' => '',
            'hero_title' => 'Membuat ide terasa|hidup di layar.',
            'hero_description' => 'Nusa HomeLab mengubah ide kompleks menjadi pengalaman digital yang jernih, hangat, dan berkesan.',
        ];
        $statement = $db->prepare('INSERT IGNORE INTO settings (`key`,`value`) VALUES (?,?)');
        foreach ($defaults as $key => $value) {
            $statement->execute([$key, $value]);
        }
    }
}
