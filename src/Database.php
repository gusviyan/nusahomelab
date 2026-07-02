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

        $host = (string) env('DB_HOST', 'sql311.infinityfree.com');
        $port = (int) env('DB_PORT', 3306);
        $name = (string) env('DB_NAME', 'if0_42313223_nusahomelab');
        $user = (string) env('DB_USER', 'if0_42313223');
        $password = (string) env('DB_PASSWORD', 'Neurotoxin4869');

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $user,
                $password,
                $options
            );

            self::initialize(self::$pdo);

            return self::$pdo;

        } catch (PDOException $e) {
            die(
                '<h2>Database Connection Failed</h2><pre>'.
                htmlspecialchars($e->getMessage()).
                '</pre>'
            );
        }
    }

    private static function initialize(PDO $db): void
    {
        $db->exec('CREATE TABLE IF NOT EXISTS admins (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $db->exec('CREATE TABLE IF NOT EXISTS services (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            sort_order INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $db->exec('CREATE TABLE IF NOT EXISTS settings (
            `key` VARCHAR(100) PRIMARY KEY,
            `value` TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        self::seed($db);
    }

    private static function seed(PDO $db): void
    {
        if ((int) $db->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {

            $stmt = $db->prepare(
                'INSERT INTO admins (username,password_hash) VALUES (?,?)'
            );

            $stmt->execute([
                env('ADMIN_USERNAME', 'admin'),
                password_hash(
                    (string) env('ADMIN_PASSWORD', 'admin123'),
                    PASSWORD_DEFAULT
                ),
            ]);
        }

        if ((int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn() === 0) {

            $stmt = $db->prepare(
                'INSERT INTO projects(title,category,description,year,sort_order)
                 VALUES(?,?,?,?,?)'
            );

            $projects = [
                [
                    'Nusa Essentials',
                    'E-commerce · Art Direction · Development',
                    'Pengalaman belanja untuk brand kebutuhan sehari-hari.',
                    '2026',
                    1
                ],
                [
                    'Kala Studio',
                    'Brand Identity · Web Design',
                    'Identitas digital untuk studio arsitektur.',
                    '2025',
                    2
                ],
                [
                    'Ruang Sehat',
                    'Product Design · Mobile Experience',
                    'Aplikasi kebugaran yang terasa personal.',
                    '2025',
                    3
                ]
            ];

            foreach ($projects as $project) {
                $stmt->execute($project);
            }
        }

        if ((int) $db->query('SELECT COUNT(*) FROM services')->fetchColumn() === 0) {

            $stmt = $db->prepare(
                'INSERT INTO services(title,description,sort_order)
                 VALUES(?,?,?)'
            );

            $services = [
                [
                    'Website Development',
                    'Website cepat, responsif, dan mudah dikelola.',
                    1
                ],
                [
                    'UI/UX Design',
                    'Antarmuka modern dan mudah digunakan.',
                    2
                ],
                [
                    'Creative Direction',
                    'Branding dan identitas visual profesional.',
                    3
                ]
            ];

            foreach ($services as $service) {
                $stmt->execute($service);
            }
        }

        $defaults = [
            'name' => 'Nusa HomeLab',
            'role' => 'Creative Developer',
            'location' => 'Jakarta',
            'email' => 'hello@nusahomelab.my.id',
            'instagram' => '#',
            'linkedin' => '#',
            'logo' => '',
            'hero_title' => 'Membuat ide terasa|hidup di layar.',
            'hero_description' => 'Nusa HomeLab mengubah ide menjadi pengalaman digital yang berkesan.',
        ];

        $stmt = $db->prepare(
            'INSERT IGNORE INTO settings(`key`,`value`) VALUES(?,?)'
        );

        foreach ($defaults as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }
}