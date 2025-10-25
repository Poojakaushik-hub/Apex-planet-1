<?php
session_start();
require 'db.php';
require 'header.php';

// --- Security: Check Login ---
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// --- Escape function if not exists ---
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

// --- Get post ID ---
$id = (int)($_GET['id'] ?? 0);

// --- Fetch post from DB ---
$st = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$st->execute([$id]);
$post = $st->fetch();

if (!$post) {
    echo "<div class='alert alert-danger'>Post not found.</div>";
    exit;
}

// --- Security: ensure only post owner can edit ---
if ($post['user_id'] != $_SESSION['user_id']) {
    echo "<div class='alert alert-danger'>Unauthorized Access!</div>";
    exit;
}

$errors = [];
$title = $post['title'];
$content = $post['content'];

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $errors[] = "Both Title and Content are required.";
    } else {
        $update = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $update->execute([$title, $content, $id]);
        header("Location: my_posts.php");
        exit;
    }
}
?>

<div class="container mt-4">
  <h2>Edit Post</h2>
  <a href="my_posts.php" class="btn btn-secondary btn-sm mb-3">&laquo; Back to My Posts</a>

  <?php foreach ($errors as $err): ?>
      <div class="alert alert-danger"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" 
             value="<?= e($title) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Content</label>
      <textarea name="content" class="form-control" rows="6" required><?= e($content) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Update Post</button>
  </form>
</div>

<?php require 'footer.php'; ?>
