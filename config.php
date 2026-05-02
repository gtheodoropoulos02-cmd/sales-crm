<?php
// ============================================================
//  Sales CRM — Configuration
//  Edit these values to match your hosting environment
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'sales_crm');
define('DB_USER', 'root');       // Change to your DB username
define('DB_PASS', '');           // Change to your DB password
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', '/uploads/');   // URL path to uploads folder
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['image/jpeg','image/png','image/gif','image/webp','application/pdf']);

define('SESSION_NAME', 'sales_crm_sess');
define('APP_NAME', 'Sales CRM');

// ============================================================
//  Database connection (PDO)
// ============================================================
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
    }
    return $pdo;
}

// ============================================================
//  Session helpers
// ============================================================
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

function requireLogin(): array {
    startSession();
    if (empty($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
    return $_SESSION['user'];
}

function requireAdmin(): array {
    $user = requireLogin();
    if ($user['role'] !== 'admin') {
        header('Location: partner.php');
        exit;
    }
    return $user;
}

function isAdmin(): bool {
    startSession();
    return !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

// ============================================================
//  JSON response helper
// ============================================================
function jsonResponse(bool $ok, $data = null, string $msg = ''): void {
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'data' => $data, 'msg' => $msg]);
    exit;
}

// ============================================================
//  Generate application code
// ============================================================
function generateAppCode(): string {
    $db = getDB();
    $row = $db->query("SELECT COUNT(*) as c FROM applications")->fetch();
    return 'APP' . str_pad($row['c'] + 1, 4, '0', STR_PAD_LEFT);
}

// ============================================================
//  Status badge helper
// ============================================================
function statusBadge(string $name, string $color): string {
    $map = [
        'blue'   => 'badge-blue',
        'green'  => 'badge-green',
        'yellow' => 'badge-yellow',
        'red'    => 'badge-red',
    ];
    $cls = $map[$color] ?? 'badge-gray';
    return '<span class="badge ' . $cls . '">' . htmlspecialchars($name) . '</span>';
}
