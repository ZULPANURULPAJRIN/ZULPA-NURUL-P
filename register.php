<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        echo "<p style='color:red; text-align:center;'>Semua kolom wajib diisi!</p>";
    } else {
        
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<p style='color:red; text-align:center;'>Email sudah terdaftar! Gunakan email lain.</p>";
        } else {
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo "<p style='color:green; text-align:center;'>Registrasi berhasil! Silakan login.</p>";
                echo "<meta http-equiv='refresh' content='2;url=index.php?page=login'>";
            } else {
                echo "<p style='color:red; text-align:center;'>Terjadi kesalahan saat menyimpan data.</p>";
            }
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

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 90vh;
    }

    .register-box {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 400px; /* <<< kecil seperti login */
        text-align: center;
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    h2 {
        margin-bottom: 25px;
        color: #222;
        font-size: 26px;
    }

    label {
        display: block;
        text-align: left;
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

    @media (max-width: 480px) {
        .register-box {
            width: 90%;
            padding: 30px 20px;
        }
    }
</style>

<div class="register-container">
    <div class="register-box">
        <h2>Register</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="index.php?page=login">Login di sini</a></p>
    </div>
</div>
