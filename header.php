<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Blog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">MyBlog</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="create.php">Create Post</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <li class="nav-item"><span class="nav-link disabled">Hi, <?= e($_SESSION['username']) ?></span></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<?php display_flash(); ?>

<div class="container mt-4">
