<?php
include 'conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
    exit;
}

// Ambil daftar supplier untuk dropdown
$supplierResult = mysqli_query($conn, "SELECT id, nama_supplier FROM supplier");

// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $supplier_id = $_POST['supplier_id'];

    $query = "INSERT INTO product (nama, harga, stok, supplier_id) VALUES ('$nama', '$harga', '$stok', '$supplier_id')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='index.php?page=product';</script>";
    } else {
        echo "<script>alert('Gagal menambah produk: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<h2>Tambah Produk Baru</h2>
<form method="POST">
    <label>Nama Produk:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Harga:</label><br>
    <input type="number" name="harga" required><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stok" required><br><br>

    <label>Supplier:</label><br>
    <select name="supplier_id" required>
        <option value="">-- Pilih Supplier --</option>
        <?php while ($row = mysqli_fetch_assoc($supplierResult)) { ?>
            <option value="<?= $row['id'] ?>"><?= $row['nama_supplier'] ?></option>
        <?php } ?>
    </select><br><br>

    <button type="submit">Tambah</button>
    <a href="index.php?page=product">Kembali</a>
</form>
 