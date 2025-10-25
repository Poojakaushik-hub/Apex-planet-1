<?php
session_start();
require 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $st = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $st->execute([$username]);
    $user = $st->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "Invalid username or password.";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<body>
<?php if (isset($_GET['registered'])) echo "<p style='color:green;'>Registered! Please log in.</p>"; ?>
<h2>Login</h2>
<!-- ✅ Flash messages -->
<?php display_flash(); ?>

<?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
<form method="post">
    <label>Username: <input name="username" required></label><br><br>
    <label>Password: <input name="password" type="password" required></label><br><br>
    <button type="submit">Login</button>
</form>
<p>No account? <a href="register.php">Register</a></p>
<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
