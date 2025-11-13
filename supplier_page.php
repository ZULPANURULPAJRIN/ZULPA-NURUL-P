<?php
include 'conn.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location='index.php?page=home';</script>";
    exit;
}

echo "<h2>🏭 Master Supplier</h2>";

$result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id DESC");

if (mysqli_num_rows($result) > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Nama Supplier</th>
                <th>Kontak</th>
                <th>Aksi</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['nama_supplier']) . "</td>
                <td>" . htmlspecialchars($row['kontak']) . "</td>
                <td>
                    <a href='index.php?page=edit_supplier&id={$row['id']}' class='action-link edit'>Edit</a> |
                    <a href='index.php?page=hapus_supplier&id={$row['id']}' class='action-link delete' onclick='return confirm(\"Yakin ingin menghapus supplier ini?\")'>Hapus</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada data supplier.</p>";
}

// ✅ Tombol tambah supplier dipindah ke bawah tabel
echo "<div style='text-align:right; margin:20px 10%;'>
        <a href='index.php?page=tambah_supplier' class='add-btn'>+ Tambah Supplier</a>
      </div>";
?>
