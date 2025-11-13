<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php'; // ✅ koneksi ke database phpMyAdmin
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .content {
            text-align: center;
            margin-top: 40px;
        }
        h2 {
            color: #333;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .card {
            background: white;
            width: 400px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 80%;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        table th, table td {
            border: none;
            padding: 10px 15px;
            text-align: left;
        }
        table th {
            background-color: rgb(134, 147, 149);
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a.action-link {
            font-weight: bold;
            text-decoration: none;
            margin: 0 5px;
        }
        a.edit { color: #007bff; }
        a.delete { color: #dc3545; }
        a.edit:hover, a.delete:hover { text-decoration: underline; }
        p { margin: 5px 0; }
        .add-btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 15px;
        }
        .add-btn:hover { background-color: #1e7e34; }
    </style>
</head>
<body>

    <div class="navbar">
    <?php if (isset($_SESSION['user'])): ?>
        <a href="index.php?page=home">Home</a>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=product">Master Product</a>
            <a href="index.php?page=user">Master User</a>
            <a href="index.php?page=supplier">Master Supplier</a>
        <?php else: ?>
            <a href="index.php?page=product">Master Product</a>

            <?php
            // 🔹 Hitung total item di keranjang user
            $user_id = $_SESSION['user']['id'];
            $cart_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM keranjang WHERE user_id=$user_id"))['total'];
            $cart_count = $cart_count ?? 0; // jika kosong, set 0
            ?>

        <?php endif; ?>

        <a href="index.php?page=logout">Logout</a>
    <?php else: ?>
        <a href="index.php?page=login">Login</a>
        <a href="index.php?page=register">Register</a>
    <?php endif; ?>
</div>

    <div class="content">
        <?php
        // 🔒 Cegah akses tanpa login
        if (!isset($_SESSION['user']) && isset($_GET['page']) && !in_array($_GET['page'], ['login', 'register'])) {
            echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
            exit;
        }

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

           $allowed_pages = [
    'login', 'register', 'home', 'dashboard',
    'product', 'user', 'supplier',
    'tambah_product', 'edit_product', 'hapus_product',
    'tambah_supplier', 'edit_supplier', 'hapus_supplier',
    'edit_user', 'delete_user', 'logout',

    // 🟢 Halaman baru untuk fitur pembelian
    'beli_product', 'riwayat', 'detail_transaksi', 'keranjang'
];


          $allowed_pages = [
    'login', 'register', 'home', 'dashboard',
    'product', 'user', 'supplier',
    'tambah_product', 'edit_product', 'hapus_product',
    'tambah_supplier', 'edit_supplier', 'hapus_supplier',
    'edit_user', 'delete_user', 'logout',

    // 🟢 Halaman baru untuk fitur pembelian
    'beli_product', 'riwayat', 'detail_transaksi'
];

    $admin_only_pages = [
        'dashboard', 'user', 'supplier',
        'tambah_supplier', 'edit_supplier', 'hapus_supplier',
        'edit_user', 'delete_user'
    ];

    if (in_array($page, $admin_only_pages) && $_SESSION['user']['role'] !== 'admin') {
        echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location='index.php?page=home';</script>";
        exit;
    }

    // ================= DASHBOARD =================
    if ($page === 'dashboard') {
    echo "<h2>📊 Dashboard</h2>";

    // 🔹 Ambil data ringkasan
    $totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
    $totalSupplier = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM supplier"))['total'];
    $totalProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM product"))['total'];

    // 🔹 Kartu statistik ringkasan
    echo "
    <div style='display:flex;justify-content:center;gap:25px;margin-top:30px;flex-wrap:wrap;'>
        <div style=\"background:#007bff;color:white;padding:25px;border-radius:12px;width:220px;text-align:center;box-shadow:0 4px 8px rgba(0,0,0,0.15);\">
            <h3 style='margin:0;font-size:32px;'>{$totalUser}</h3>
            <p style='margin:5px 0 0;'>User</p>
        </div>
        <div style=\"background:#28a745;color:white;padding:25px;border-radius:12px;width:220px;text-align:center;box-shadow:0 4px 8px rgba(0,0,0,0.15);\">
            <h3 style='margin:0;font-size:32px;'>{$totalSupplier}</h3>
            <p style='margin:5px 0 0;'>Supplier</p>
        </div>
        <div style=\"background:#ffc107;color:white;padding:25px;border-radius:12px;width:220px;text-align:center;box-shadow:0 4px 8px rgba(0,0,0,0.15);\">
            <h3 style='margin:0;font-size:32px;'>{$totalProduct}</h3>
            <p style='margin:5px 0 0;'>Product</p>
        </div>
    </div>";

    // 🔹 Produk terbaru
    $recentProducts = mysqli_query($conn, "
        SELECT p.id, p.nama, p.harga, p.stok, s.nama_supplier 
        FROM product p 
        LEFT JOIN supplier s ON p.supplier_id = s.id 
        ORDER BY p.id DESC 
        LIMIT 5
    ");

    echo "<div style='margin-top:50px;text-align:center;'>
            <h3>📦 5 Produk Terbaru</h3>";

    if ($recentProducts && mysqli_num_rows($recentProducts) > 0) {
        echo "<table style='margin:25px auto;width:85%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 3px 10px rgba(0,0,0,0.1);'>
                <thead style='background:#6c757d;color:white;'>
                    <tr>
                        <th style='padding:10px;'>ID</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = mysqli_fetch_assoc($recentProducts)) {
            echo "<tr style='text-align:center;'>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['nama']) . "</td>
                    <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                    <td>{$row['stok']}</td>
                    <td>" . htmlspecialchars($row['nama_supplier'] ?? '-') . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Tidak ada produk ditemukan.</p>";
    }
    echo "</div>";

    // 🔹 Grafik stok produk
    $chartQuery = mysqli_query($conn, "SELECT nama, stok FROM product ORDER BY id ASC LIMIT 10");
    $productNames = [];
    $productStocks = [];
    while ($row = mysqli_fetch_assoc($chartQuery)) {
        $productNames[] = $row['nama'];
        $productStocks[] = $row['stok'];
    }

    $namesJSON = json_encode($productNames);
    $stocksJSON = json_encode($productStocks);

    echo "
    <div style='margin-top:60px;text-align:center;'>
        <h3>📈 Grafik Stok Produk</h3>
        <div style='max-width:850px;margin:30px auto;background:#fff;padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);'>
            <canvas id='stokChart' style='width:100%;height:400px;'></canvas>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script>
        const ctx = document.getElementById('stokChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: $namesJSON,
                datasets: [{
                    label: 'Jumlah Stok Produk',
                    data: $stocksJSON,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(0, 204, 102, 0.6)',
                        'rgba(255, 102, 204, 0.6)',
                        'rgba(102, 153, 255, 0.6)',
                        'rgba(255, 153, 51, 0.6)'
                    ],
                    borderColor: 'rgba(0,0,0,0.2)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    title: {
                        display: true,
                        text: 'Jumlah Stok Setiap Produk',
                        font: { size: 18 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#333',
                            font: { size: 13 }
                        },
                        grid: { color: 'rgba(200,200,200,0.3)' }
                    },
                    x: {
                        ticks: { color: '#333', font: { size: 13 } },
                        grid: { color: 'rgba(200,200,200,0.2)' }
                    }
                }
            }
        });
    </script>
    ";
}


    // ================= MASTER USER =================
    elseif ($page === 'user') {
        include 'user_page.php';
    }

    // ================= MASTER SUPPLIER =================
    elseif ($page === 'supplier') {
        include 'supplier_page.php';
    }

    // ================= MASTER PRODUCT =================
    elseif ($page === 'product') {
        include 'product_page.php';
    }
    // ================= RIWAYAT PAGE =================
    elseif ($page === 'riwayat') {
        include 'riwayat_page.php';
    }

    // ================= HOME =================
    elseif ($page === 'home') {
        echo "<div class='card'><H1>Wellcome</H1><p>Halo, " . htmlspecialchars($_SESSION['user']['username']) . "!</p></div>";
    }

    // ================= FILE TERPISAH =================
    else {
        $filename = $page . ".php";
        if (file_exists($filename)) {
            include $filename;
        } else {
            echo "<h3>⚠️ Halaman '$page' tidak ditemukan.</h3>";
        }
    }

} else {
            // Jika tidak ada parameter ?page di URL
            if (isset($_SESSION['user'])) {
                echo "<h2>Halo, selamat datang <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b>!</h2>";
                echo "<p>Anda login sebagai <b>" . htmlspecialchars($_SESSION['user']['role']) . "</b></p>";
            } else {
                echo "<h2>Selamat datang di User Authentication System</h2>";
                echo "<p>Silakan login untuk melanjutkan.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
