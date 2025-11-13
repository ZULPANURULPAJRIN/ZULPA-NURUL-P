<?php
// Pastikan sesi sudah dimulai dan koneksi database (conn.php) sudah di-include
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

// 1. Cek Akses & ID Transaksi 🔑
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID Transaksi tidak valid!'); window.location='index.php?page=riwayat';</script>";
    exit;
}

// Ambil ID Transaksi dari URL dan User ID dari Session
$transaksi_id = (int)$_GET['id'];
$user_id = $_SESSION['user']['id'];

// 2. Ambil Data Transaksi Utama 💸
// Gunakan Prepared Statement untuk mencegah SQL Injection, terutama pada $transaksi_id dan $user_id
$query_transaksi = "
    SELECT id, tanggal, total, status
    FROM transaksi
    WHERE id = ? AND user_id = ?
";
$stmt = $conn->prepare($query_transaksi);
$stmt->bind_param("ii", $transaksi_id, $user_id);
$stmt->execute();
$result_transaksi = $stmt->get_result();
$transaksi_data = $result_transaksi->fetch_assoc();

if (!$transaksi_data) {
    // Jika transaksi tidak ditemukan atau bukan milik user ini
    echo "<script>alert('Transaksi tidak ditemukan atau akses ditolak!'); window.location='index.php?page=riwayat';</script>";
    exit;
}

echo "<h2>🧾 Detail Transaksi #{$transaksi_data['id']}</h2>";
echo "<p><strong>Tanggal:</strong> {$transaksi_data['tanggal']}</p>";
echo "<p><strong>Status:</strong> {$transaksi_data['status']}</p>";

// 3. Ambil Item-Item dalam Transaksi (Detail Produk) 🛍️
$query_items = "
    SELECT oi.qty, oi.harga, p.nama AS nama_produk
    FROM order_items oi
    JOIN product p ON oi.product_id = p.id
    WHERE oi.transaksi_id = ?
";
$stmt_items = $conn->prepare($query_items);

// Pemeriksaan Error Tambahan:
if ($stmt_items === false) {
    // 1. Dapatkan error dari koneksi database
    die("Error prepare query: " . $conn->error . "<br>Query: " . $query_items);
}

// Lanjutkan ke bind_param jika tidak ada error...
$stmt_items->bind_param("i", $transaksi_id);
$stmt_items->bind_param("i", $transaksi_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

echo "<h3>Daftar Produk</h3>";

if (mysqli_num_rows($result_items) > 0) {
    echo "<table border='1'>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah (Qty)</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>";
    $grand_total = 0;
    while ($item = $result_items->fetch_assoc()) {
        $subtotal = $item['qty'] * $item['harga'];
        $grand_total += $subtotal;
        
        echo "<tr>
                <td>{$item['nama_produk']}</td>
                <td style='text-align: center;'>{$item['qty']}</td>
                <td style='text-align: right;'>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>
                <td style='text-align: right;'>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
              </tr>";
    }
    echo "<tr>
            <td colspan='3' style='text-align: right;'><strong>TOTAL AKHIR</strong></td>
            <td style='text-align: right;'><strong>Rp " . number_format($transaksi_data['total'], 0, ',', '.') . "</strong></td>
          </tr>";
    echo "</table>";
} else {
    echo "<p>Tidak ada detail produk untuk transaksi ini.</p>";
}

$stmt->close();
$stmt_items->close();
?>