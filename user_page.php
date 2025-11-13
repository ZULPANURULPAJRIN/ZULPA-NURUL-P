<?php
include 'conn.php';

// Hanya admin yang boleh akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location='index.php?page=home';</script>";
    exit;
}

echo "<h2>👥 Master User</h2>";

$result = mysqli_query($conn, "SELECT id, username, email, role FROM users ORDER BY id DESC");

if (mysqli_num_rows($result) > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['role']) . "</td>
                <td>
                    <a href='index.php?page=edit_user&id={$row['id']}' class='action-link edit'>Edit</a> |
                    <a href='index.php?page=delete_user&id={$row['id']}' class='action-link delete' onclick='return confirm(\"Yakin ingin menghapus user ini?\")'>Hapus</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada data user.</p>";
}
?>
