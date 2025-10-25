<?php require 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    } else {
        // check unique
        $st = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $st->execute([$username]);
        if ($st->fetch()) {
            $errors[] = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $ins->execute([$username, $hash]);
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>
<?php foreach ($errors as $e) echo "<p style='color:red;'>".e($e)."</p>"; ?>
<form method="post">
    <label>Username: <input name="username" required></label><br><br>
    <label>Password: <input name="password" type="password" required></label><br><br>
    <button type="submit">Create account</button>
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
