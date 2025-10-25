<?php
session_start();
require 'db.php';
require 'header.php'; // includes <head> with Bootstrap + style.css

// Escape helper
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

// --- Search ---
$search = $_GET['q'] ?? '';

// --- Pagination ---
$limit = 5; // posts per page
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Base SQL
$sql = "
    SELECT posts.id, posts.title, posts.content, posts.created_at, posts.user_id, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
";

$params = [];

// --- If search term entered ---
if ($search !== '') {
    $sql .= " WHERE posts.title LIKE ? OR posts.content LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// --- Count total posts for pagination ---
$countSql = "SELECT COUNT(*) FROM posts";
if ($search !== '') {
    $countSql .= " WHERE title LIKE ? OR content LIKE ?";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

// --- Final SQL with limit ---
$sql .= " ORDER BY posts.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<div class="container mt-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Posts</h2>
    <div>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <span class="me-2 text-success fw-bold">Welcome, <?= e($_SESSION['username']) ?></span>
        <a href="create.php" class="btn btn-primary btn-sm">Create Post</a>
        <a href="my_posts.php" class="btn btn-outline-info btn-sm">My Posts</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-primary btn-sm">Login</a>
        <a href="register.php" class="btn btn-outline-success btn-sm">Register</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Flash Message -->
  <?php if (function_exists('display_flash')) display_flash(); ?>

  <!-- Search Form -->
  <form method="get" action="index.php" class="mb-4 d-flex">
    <input type="text" name="q" class="form-control me-2" placeholder="Search posts..." 
           value="<?= e($search) ?>">
    <button type="submit" class="btn btn-success">Search</button>
  </form>

  <!-- Posts List -->
  <?php if (!$posts): ?>
    <div class="alert alert-warning">No posts found.</div>
  <?php else: ?>
    <?php foreach ($posts as $p): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h4 class="card-title"><?= e($p['title']) ?></h4>
          <h6 class="card-subtitle text-muted mb-2">
            By <?= e($p['username']) ?> on <?= e($p['created_at']) ?>
          </h6>
          <p class="card-text"><?= nl2br(e($p['content'])) ?></p>

          <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $p['user_id']): ?>
            <a href="edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <form action="delete.php" method="post" class="d-inline" 
                  onsubmit="return confirm('Delete this post?');">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
          <?php endif; ?>
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
            <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>

</div>

<?php require 'footer.php'; ?>
