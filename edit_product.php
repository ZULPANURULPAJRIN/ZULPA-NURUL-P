<?php
include 'conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "<script>alert('ID produk tidak ditemukan!'); window.location='index.php?page=product';</script>";
    exit;
}

// Ambil data produk
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE id='$id'"));
if (!$product) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php?page=product';</script>";
    exit;
}

// Ambil daftar supplier hanya untuk admin
$suppliers = [];
if ($_SESSION['user']['role'] === 'admin') {
    $supplierQuery = mysqli_query($conn, "SELECT * FROM supplier ORDER BY nama_supplier ASC");
    while ($row = mysqli_fetch_assoc($supplierQuery)) {
        $suppliers[] = $row;
    }
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);

    // Admin bisa update stok & supplier
    if ($_SESSION['user']['role'] === 'admin') {
        $stok = intval($_POST['stok']);
        $supplier_id = intval($_POST['supplier_id']);
        $update = "UPDATE product SET nama='$nama', harga='$harga', stok='$stok', supplier_id='$supplier_id' WHERE id='$id'";
    } else {
        // User hanya bisa update nama/harga (misal untuk simulasi beli)
        $update = "UPDATE product SET nama='$nama', harga='$harga' WHERE id='$id'";
    }

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='index.php?page=product';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f6fa; }
        .container { width: 450px; margin: 50px auto; background: white; border-radius: 10px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px; }
        button { background-color: #007bff; border: none; padding: 10px 15px; color: white; border-radius: 6px; margin-top: 15px; cursor: pointer; width: 100%; }
        button:hover { background-color: #0056b3; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Produk</h2>
    <form method="POST">
        <label>Nama Produk:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($product['nama']) ?>" required>

        <label>Harga:</label>
        <input type="number" name="harga" value="<?= htmlspecialchars($product['harga']) ?>" required>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <label>Stok:</label>
            <input type="number" name="stok" value="<?= htmlspecialchars($product['stok']) ?>" required>

            <label>Supplier:</label>
            <select name="supplier_id" required>
                <?php foreach ($suppliers as $sup) { ?>
                    <option value="<?= $sup['id'] ?>" <?= ($product['supplier_id'] == $sup['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sup['nama_supplier']) ?>
                    </option>
                <?php } ?>
            </select>
        <?php endif; ?>

        <button type="submit">💾 Simpan Perubahan</button>
    </form>
    <a href="index.php?page=product" class="back-link">← Kembali ke Master Product</a>
</div>
</body>
</html>
