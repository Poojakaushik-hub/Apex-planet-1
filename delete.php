<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $user_id = $_SESSION['user_id'] ?? 0;

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    set_flash('success', 'Post deleted successfully!');
}
header("Location: index.php");
exit;
?>
