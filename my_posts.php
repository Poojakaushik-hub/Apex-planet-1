<?php
session_start();
require 'db.php';
require 'header.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Escape function if not exists
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

// --- Pagination setup ---
$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Count total user posts
$countSql = "SELECT COUNT(*) FROM posts WHERE user_id = ?";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute([$_SESSION['user_id']]);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

// Fetch posts for this user
$sql = "SELECT id, title, content, created_at FROM posts 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>

<div class="container mt-4">
  <h2>My Posts</h2>
  <a href="index.php" class="btn btn-secondary btn-sm mb-3">&laquo; Back to All Posts</a>

  <?php if (!$posts): ?>
    <div class="alert alert-warning">You haven’t created any posts yet.</div>
  <?php else: ?>
    <?php foreach ($posts as $p): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h4 class="card-title"><?= e($p['title']) ?></h4>
          <small class="text-muted"><?= e($p['created_at']) ?></small>
          <p class="card-text mt-2"><?= nl2br(e($p['content'])) ?></p>
          <a href="edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <form action="delete.php" method="post" class="d-inline" 
                onsubmit="return confirm('Delete this post?');">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>

</div>

<?php require 'footer.php'; ?>
