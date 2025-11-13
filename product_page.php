<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'conn.php';

// 🔹 Ambil semua produk + supplier
$products = mysqli_query($conn, "
    SELECT p.id, p.nama, p.harga, p.stok, s.nama_supplier 
    FROM product p 
    LEFT JOIN supplier s ON p.supplier_id = s.id
    ORDER BY p.id ASC
");
?>

<h2>📦 Master Product</h2>

<?php if($_SESSION['user']['role'] === 'admin'): ?>
    <a href="index.php?page=tambah_product" class="add-btn">+ Tambah Produk</a>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Supplier</th>
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <th>Action</th>
            <?php elseif($_SESSION['user']['role'] === 'user'): ?>
            
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
                <td><?= $row['stok'] ?></td>
                <td><?= htmlspecialchars($row['nama_supplier'] ?? '-') ?></td>
                
                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    <td>
                        <a href="index.php?page=edit_product&id=<?= $row['id'] ?>" class="action-link edit">Edit</a>
                        <a href="index.php?page=hapus_product&id=<?= $row['id'] ?>" class="action-link delete" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                <?php elseif($_SESSION['user']['role'] === 'user'): ?>
                    
                <?php endif; ?>

            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
