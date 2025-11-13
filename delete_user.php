<?php
include 'conn.php';
$id = $_GET['id'] ?? 0;
mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
header("Location: index.php?page=dashboard");
exit;
?>
