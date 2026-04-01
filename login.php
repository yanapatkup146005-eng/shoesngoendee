<?php
session_start();
include('db.php');

if (isset($_SESSION['username'])) {
    header("Location: index.php");
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
                header("Location: admin.index.php"); 
            } else {
                header("Location: index.php");
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
    <title>shoe store Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .bg-login {
            background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                              url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070&auto=format&fit=crop');
            background-size: cover; background-position: center;
            height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 40px; border-radius: 24px;
            width: 100%; max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        .brand-icon { 
            font-size: 3rem; color: #fff; margin-bottom: 15px;
            display: inline-block;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
        }
        .form-control { 
            background: rgba(255, 255, 255, 0.9); 
            border: none; padding: 12px; border-radius: 10px; margin-bottom: 15px; 
        }
        .btn-login-main { 
            background-color: #f83a3a; color: white; border: none; 
            border-radius: 10px; padding: 12px; font-weight: bold; transition: 0.3s; 
        }
        .btn-login-main:hover { background-color: #d32f2f; transform: translateY(-2px); }
        .btn-signup-main { 
            background-color: transparent; color: white; border: 2px solid #fff; 
            border-radius: 10px; padding: 10px; text-decoration: none; display: block; 
            font-weight: bold; transition: 0.3s; 
        }
        .btn-signup-main:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

    <div class="bg-login">
        <div class="login-card">
            <div class="brand-icon">
                <i class="fas fa-shoe-prints"></i>
            </div>
            <h4 class="text-white fw-bold mb-4">SHORE STORE</h4>
            
            <?php if($error != ""): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert" style="font-size: 0.85rem; border-radius: 10px;">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                
                <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-top: 10px;"></p>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <button type="submit" name="login" class="btn btn-login-main w-100">Log In</button>
                    </div>
                    <div class="col-6">
                        <a href="register.php" class="btn btn-signup-main w-100">Sign Up</a>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="card border-0" style="width: 100%; border-radius: 15px; background: rgba(255, 255, 255, 0.95);">
                        <div class="card-body p-3">
                            <h6 class="text-center fw-bold mb-2" style="color: #333; font-size: 0.85rem;">
                                🔐 รหัสสำหรับเข้าระบบ
                            <div class="row text-start">
                                <div class="col-12 mb-1 p-2 bg-light rounded shadow-sm">
                                    <small class="d-block text-primary fw-bold" style="font-size: 0.7rem;">USER ROLE</small>
                                    <code style="font-size: 0.8rem; color: #333;">User: user2 / P: user22</code>
                                </div>
                                <div class="col-12 p-2 bg-light rounded shadow-sm">
                                    <small class="d-block text-danger fw-bold" style="font-size: 0.7rem;">ADMIN ROLE</small>
                                    <code style="font-size: 0.8rem; color: #333;">Admin: user3 / P: user33</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>