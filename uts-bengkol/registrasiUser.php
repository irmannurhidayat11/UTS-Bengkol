<?php
// register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak sama!'); window.location='index.php?page=registrasiUser.php';</script>";
        exit;
    }
    
    // Check if username exists
    $check_query = "SELECT * FROM user WHERE username = ?";
    $stmt = $koneksi->prepare($check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.location='index.php?page=registrasiUser.php';</script>";
        exit;
    }
    
    // Hash password and insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO user (username, password) VALUES (?, ?)";
    $stmt = $koneksi->prepare($insert_query);
    $stmt->bind_param("ss", $username, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location='index.php?page=loginUser.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan!'); window.location='index.php?page=registrasiUser.php';</script>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Register</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
