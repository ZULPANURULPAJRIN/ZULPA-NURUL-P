<?php
include 'conn.php';
$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM supplier WHERE id=$id");
header("Location: index.php?page=supplier");
exit;
?>
