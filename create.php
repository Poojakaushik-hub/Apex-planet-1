<?php
session_start();
require 'db.php';

if (empty($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$title = $content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        set_flash('error', 'Title and content are required.');
    } else {
        // Insert post with user_id
        $st = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
        $st->execute([$title, $content, $_SESSION['user_id']]);
        set_flash('success', 'Post created successfully!');
        header("Location: index.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Post</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<a href="index.php" class="btn btn-secondary btn-sm mb-3">&laquo; Back</a>
<h2>Create New Post</h2>

<!-- ✅ Flash messages -->
<?php display_flash(); ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" value="<?= e($title) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Content</label>
        <textarea name="content" class="form-control" rows="6" required><?= e($content) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
