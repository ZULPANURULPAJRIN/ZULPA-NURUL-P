<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // bisa username atau email
    $password   = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        echo "<p style='color:red; text-align:center;'>Username/Email dan password wajib diisi!</p>";
    } else {
        // Cek apakah input berupa email atau username
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $query = "SELECT * FROM users WHERE email = ?";
        } else {
            $query = "SELECT * FROM users WHERE username = ?";
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password (gunakan password_hash di pendaftaran)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // Simpan semua data penting ke session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'] // ✅ simpan role juga
                ];

                // Arahkan ke home
                echo "<p style='color:green; text-align:center;'>Login berhasil! Mengarahkan ke halaman utama...</p>";
                echo "<meta http-equiv='refresh' content='2;url=index.php?page=home'>";
            } else {
                echo "<script>alert('Password salah!'); window.location.href='index.php?page=login';</script>";
            }
        } else {
            echo "<script>alert('Username/Email tidak ditemukan!'); window.location.href='index.php?page=login';</script>";
        }
    }
}
?>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f3f4f6;
        margin: 0;
        padding: 0;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 70vh;
    }

    .login-box {
        background: white;
        padding: 40px;
        border-radius: 11px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center; /* agar kotaknya tetap rapi di tengah */
    }

    h2 {
        margin-bottom: 25px;
        color: #222;
        font-size: 26px;
        text-align: center;
    }

    /* --- bagian ini penting --- */
    label {
        display: block;
        text-align: left; /* ubah dari center ke left */
        margin-bottom: 6px;
        font-weight: bold;
        color: #444;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 15px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
        outline: none;
    }

    button {
        width: 100%;
        background-color: #007bff;
        color: white;
        padding: 14px 0;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.25s, transform 0.1s;
    }

    button:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    p {
        font-size: 14px;
        margin-top: 15px;
    }

    a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        <form method="post" action="">
            <label for="username">Username atau Email:</label>
            <input type="text" name="identifier" placeholder="Masukkan username atau email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="index.php?page=register">Daftar di sini</a></p>
    </div>
</div>
