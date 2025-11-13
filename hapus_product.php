<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
    exit;
}

// Pastikan ada ID produk
if (!isset($_GET['id'])) {
    echo "<script>alert('ID produk tidak ditemukan!'); window.location='index.php?page=product';</script>";
    exit;
}

$id = intval($_GET['id']);

// Hapus produk berdasarkan ID
$query = "DELETE FROM product WHERE id = $id";
if (mysqli_query($conn, $query)) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='index.php?page=product';</script>";
} else {
    echo "<script>alert('Gagal menghapus produk: " . mysqli_error($conn) . "'); window.location='index.php?page=product';</script>";
}
?>
