<?php

declare(strict_types=1);

$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$staticFile = __DIR__.$uri;
if (PHP_SAPI === 'cli-server' && $uri !== '/' && is_file($staticFile)) {
    return false;
}

require_once __DIR__.'/../src/bootstrap.php';

if (defined('BASE_URL') && BASE_URL !== '' && str_starts_with($uri, BASE_URL)) {
    $uri = substr($uri, strlen(BASE_URL)) ?: '/';
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper((string) $_POST['_method']);
}

try {
    if ($method === 'GET' && $uri === '/') {
        require BASE_PATH.'/views/home.php';
        exit;
    }
    if ($method === 'GET' && $uri === '/admin') {
        require empty($_SESSION['admin_id'])
            ? BASE_PATH.'/views/login.php'
            : BASE_PATH.'/views/admin/dashboard.php';
        exit;
    }
    if ($method === 'GET' && $uri === '/admin/dashboard') {
        if (empty($_SESSION['admin_id'])) {
            header('Location: '.(BASE_URL ?: '').'/admin');
            exit;
        }
        require BASE_PATH.'/views/admin/dashboard.php';
        exit;
    }

    if (!str_starts_with($uri, '/api/')) {
        http_response_code(404);
        echo 'Halaman tidak ditemukan.';
        exit;
    }

    $db = Database::connection();
    $data = requestData();

    if ($method === 'POST' && $uri === '/api/auth/login') {
        $username = cleanString($data, 'username', true, 100);
        $password = (string) ($data['password'] ?? '');
        $statement = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $statement->execute([$username]);
        $admin = $statement->fetch();
        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            jsonResponse(['success' => false, 'message' => 'Username atau password salah.'], 401);
        }
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        jsonResponse(['success' => true, 'user' => ['username' => $admin['username']]]);
    }
    if ($method === 'GET' && $uri === '/api/auth/me') {
        requireAdmin();
        jsonResponse(['success' => true, 'user' => ['username' => $_SESSION['admin_username']]]);
    }
    if ($method === 'POST' && $uri === '/api/auth/logout') {
        $_SESSION = [];
        session_destroy();
        jsonResponse(['success' => true]);
    }

    if ($method === 'GET' && $uri === '/api/portfolio') {
        jsonResponse(['success' => true, 'data' => $db->query('SELECT * FROM projects ORDER BY sort_order,id')->fetchAll()]);
    }
    if ($method === 'GET' && $uri === '/api/services') {
        jsonResponse(['success' => true, 'data' => $db->query('SELECT * FROM services ORDER BY sort_order,id')->fetchAll()]);
    }
    if ($method === 'GET' && $uri === '/api/settings') {
        $rows = $db->query('SELECT `key`,`value` FROM settings')->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        jsonResponse(['success' => true, 'data' => $settings]);
    }

    requireAdmin();

    if ($method === 'POST' && $uri === '/api/admin/portfolio') {
        $image = uploadImage('image', 5 * 1024 * 1024, [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif',
        ]);
        $statement = $db->prepare('INSERT INTO projects (title,category,description,year,image,link,sort_order) VALUES (?,?,?,?,?,?,?)');
        $statement->execute([
            cleanString($data, 'title', true, 255), cleanString($data, 'category', true, 255),
            cleanString($data, 'description'), cleanString($data, 'year', false, 20),
            $image ?? '', cleanString($data, 'link', false, 500), (int) ($data['sort_order'] ?? 0),
        ]);
        jsonResponse(['success' => true, 'id' => (int) $db->lastInsertId()]);
    }
    if (preg_match('#^/api/admin/portfolio/(\d+)$#', $uri, $matches)) {
        $id = (int) $matches[1];
        if ($method === 'DELETE') {
            $statement = $db->prepare('DELETE FROM projects WHERE id=?');
            $statement->execute([$id]);
            jsonResponse(['success' => true]);
        }
        if ($method === 'PUT') {
            $statement = $db->prepare('SELECT * FROM projects WHERE id=?');
            $statement->execute([$id]);
            $project = $statement->fetch();
            if (!$project) {
                jsonResponse(['success' => false, 'message' => 'Proyek tidak ditemukan.'], 404);
            }
            $image = uploadImage('image', 5 * 1024 * 1024, [
                'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif',
            ]) ?? $project['image'];
            $statement = $db->prepare('UPDATE projects SET title=?,category=?,description=?,year=?,image=?,link=?,sort_order=? WHERE id=?');
            $statement->execute([
                cleanString($data, 'title', true, 255), cleanString($data, 'category', true, 255),
                cleanString($data, 'description'), cleanString($data, 'year', false, 20), $image,
                cleanString($data, 'link', false, 500), (int) ($data['sort_order'] ?? 0), $id,
            ]);
            jsonResponse(['success' => true]);
        }
    }

    if ($method === 'POST' && $uri === '/api/admin/services') {
        $statement = $db->prepare('INSERT INTO services (title,description,sort_order) VALUES (?,?,?)');
        $statement->execute([
            cleanString($data, 'title', true, 255), cleanString($data, 'description', true),
            (int) ($data['sort_order'] ?? 0),
        ]);
        jsonResponse(['success' => true, 'id' => (int) $db->lastInsertId()]);
    }
    if (preg_match('#^/api/admin/services/(\d+)$#', $uri, $matches)) {
        $id = (int) $matches[1];
        if ($method === 'DELETE') {
            $statement = $db->prepare('DELETE FROM services WHERE id=?');
            $statement->execute([$id]);
            jsonResponse(['success' => true]);
        }
        if ($method === 'PUT') {
            $statement = $db->prepare('UPDATE services SET title=?,description=?,sort_order=? WHERE id=?');
            $statement->execute([
                cleanString($data, 'title', true, 255), cleanString($data, 'description', true),
                (int) ($data['sort_order'] ?? 0), $id,
            ]);
            jsonResponse(['success' => true]);
        }
    }

    if ($method === 'POST' && $uri === '/api/admin/settings/logo') {
        $logo = uploadImage('logo', 2 * 1024 * 1024, [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        ], 'logo-');
        if ($logo === null) {
            throw new InvalidArgumentException('Pilih logo JPG, PNG, atau WebP maksimal 2MB.');
        }
        $statement = $db->prepare('INSERT INTO settings (`key`,`value`) VALUES ("logo",?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        $statement->execute([$logo]);
        jsonResponse(['success' => true, 'data' => ['logo' => $logo]]);
    }

    if ($method === 'PUT' && $uri === '/api/admin/settings') {
        $allowed = [
            'name','role','location','email','instagram','linkedin','topbar_text','topbar_link_text',
            'hero_title','hero_description','hero_primary_text','hero_secondary_text','trust_label','trust_items',
            'project_count','rating','rating_label','benefit_1_title','benefit_1_text','benefit_2_title',
            'benefit_2_text','benefit_3_title','benefit_3_text','services_label','services_title',
            'services_description','service_link_text','portfolio_label','portfolio_title','portfolio_cta',
            'about_label','about_title','about_description','experience_years','experience_label',
            'stat_1_value','stat_1_label','stat_2_value','stat_2_label','stat_3_value','stat_3_label',
            'contact_label','contact_title','contact_description','contact_cta','footer_description','copyright',
        ];
        $statement = $db->prepare('INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        $db->beginTransaction();
        foreach ($allowed as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $statement->execute([$key, trim($data[$key])]);
            }
        }
        $db->commit();
        jsonResponse(['success' => true]);
    }

    jsonResponse(['success' => false, 'message' => 'Endpoint tidak ditemukan.'], 404);
} catch (InvalidArgumentException $error) {
    jsonResponse(['success' => false, 'message' => $error->getMessage()], 422);
} catch (Throwable $error) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log($error->__toString());
    jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
}
