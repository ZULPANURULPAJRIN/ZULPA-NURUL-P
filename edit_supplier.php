<?php
include 'conn.php';
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM supplier WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_supplier'];
    $kontak = $_POST['kontak'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    $update = "UPDATE supplier SET 
                nama_supplier='$nama',
                kontak='$kontak',
                email='$email',
                alamat='$alamat'
               WHERE id=$id";
    if (mysqli_query($conn, $update)) {
        header("Location: index.php?page=supplier");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<form method="POST" style="text-align:center; margin-top:30px;">
    <h3>Edit Supplier</h3>
    <p><input type="text" name="nama_supplier" value="<?= $data['nama_supplier'] ?>" required></p>
    <p><input type="text" name="kontak" value="<?= $data['kontak'] ?>"></p>
    <p><input type="email" name="email" value="<?= $data['email'] ?>"></p>
    <p><textarea name="alamat"><?= $data['alamat'] ?></textarea></p>
    <p><button type="submit">Update</button></p>
</form>
