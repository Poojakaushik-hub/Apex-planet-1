<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database connection ---
$dsn  = "mysql:host=localhost;dbname=blog;charset=utf8mb4";
$user = "root";     // XAMPP default username
$pass = "";         // XAMPP default password (empty by default)

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("Database Connection Failed: " . $e->getMessage());
}

// --- HTML Escape Helper (to prevent XSS) ---
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// --- Flash Message Helpers ---
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function display_flash() {
    if (!empty($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $msg = $_SESSION['flash']['message'];

        $alertClass = ($type === 'success') ? 'alert-success' : 'alert-danger';

        echo "
        <div class='alert $alertClass alert-dismissible fade show mt-3 container' role='alert'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
        ";

        unset($_SESSION['flash']); // clear after display
    }
}
?>
