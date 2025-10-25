session_start();
session_destroy();
require 'db.php';
set_flash('success', 'You have been logged out.');
header("Location: index.php");
exit;
