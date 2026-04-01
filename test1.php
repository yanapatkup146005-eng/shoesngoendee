<?php
session_start();
include('db.php');

if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($_SESSION['role'] == 'admin') {
                header("Location: addmin.index.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้งานนี้";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Shoe Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            /* ใส่ URL รูปรองเท้าที่คุณต้องการที่นี่ */
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                              url('https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Kanit', sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            border-top: 5px solid #ffc107; /* สีเหลือง */
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            margin: auto;
        }

        .login-header {
            background: #212529; /* สีดำ */
            color: #ffc107; /* สีเหลือง */
            padding: 30px;
            text-align: center;
        }

        .btn-yellow {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }

        .btn-yellow:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            color: #000;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .input-group-text {
            background-color: #212529;
            color: #ffc107;
            border: none;
        }

        .register-link {
            color: #212529;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card">
        <div class="login-header">
            <h3 class="mb-0"><i class="fas fa-shoe-prints me-2"></i> SHOE STORE</h3>
            <small class="text-white-50">ยินดีต้อนรับสู่ร้านรองเท้าของเรา</small>
        </div>
        
        <div class="p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small text-center">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-yellow w-100 py-2 mb-3 shadow-sm">
                    เข้าสู่ระบบ <i class="fas fa-sign-in-alt ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-2">
                <p class="text-muted small">ยังไม่มีบัญชีใช่หรือไม่? 
                    <a href="register.php" class="register-link">สมัครสมาชิกที่นี่</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>