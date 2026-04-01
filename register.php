<?php
include('db.php');
$message = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $check_user = "SELECT * FROM users WHERE username = '$username'";
    $query_check = mysqli_query($conn, $check_user);

    if (mysqli_num_rows($query_check) > 0) {
        $message = '<div class="alert alert-danger py-2" style="border-radius: 10px;">ชื่อผู้ใช้นี้ถูกใช้งานแล้ว!</div>';
    } else {
        $sql = "INSERT INTO users (username, password, firstname, lastname, email, phone, address, role) 
                VALUES ('$username', '$password', '$firstname', '$lastname', '$email', '$phone', '$address', 'user')";
        if (mysqli_query($conn, $sql)) {
            $message = '<div class="alert alert-success py-2" style="border-radius: 10px;">สมัครสำเร็จ! <a href="login.php" class="fw-bold text-decoration-none">เข้าสู่ระบบ</a></div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        /* พื้นหลังโทนสว่างดูสะอาดตาและปลอดภัย */
        .bg-register {
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                              url('https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        /* Card กระจกฝ้า (ขยายให้กว้างขึ้น) */
        .register-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 680px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            text-align: center;
        }

        /* เปลี่ยนจากโลโก้แบรนด์เป็นไอคอนกลางๆ */
        .brand-logo { font-size: 3rem; color: #fff; margin-bottom: 15px; display: inline-block; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3)); }

        /* ปรับแต่ง Input */
        .form-label { font-size: 0.85rem; color: rgba(255,255,255,0.9); margin-bottom: 5px; font-weight: 600; }
        .form-control { 
            background: rgba(255, 255, 255, 0.95); 
            border: none; 
            border-radius: 12px; 
            padding: 12px;
            color: #333;
        }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(255,255,255,0.25); }

        /* ปุ่มสีแดงสดตัดกับพื้นหลัง */
        .btn-register-main { 
            background-color: #f83a3a; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 12px; 
            font-weight: bold;
            transition: 0.3s;
            font-size: 1.1rem;
        }
        .btn-register-main:hover { background-color: #d32f2f; color: white; transform: translateY(-2px); }

        .login-link { color: #fff; text-decoration: none; font-weight: bold; border-bottom: 2px solid #fff; padding-bottom: 2px; }
        .login-link:hover { color: rgba(255,255,255,0.8); border-color: rgba(255,255,255,0.8); }
    </style>
</head>
<body>

<div class="bg-register">
    <div class="register-card">
        <div class="brand-logo">
            <i class="fas fa-shoe-prints"></i>
        </div>
        <h3 class="mb-1 fw-bold text-white">SHORE STORE </h3>
        <p class="text-white-50 mb-4">สมัครสมาชิกเพื่อเข้าสู่ระบบ</p>

        <?php echo $message; ?>

        <form method="POST" class="text-start mt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้สำหรับเข้าสู่ระบบ" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">ชื่อจริง</label>
                    <input type="text" name="firstname" class="form-control" placeholder="First Name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">นามสกุล</label>
                    <input type="text" name="lastname" class="form-control" placeholder="Last Name">
                </div>

                <div class="col-md-7">
                    <label class="form-label">อีเมลติดต่อ</label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" class="form-control" placeholder="08XXXXXXXX">
                </div>

                <div class="col-12">
                    <label class="form-label">ที่อยู่จัดส่งสินค้า</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="ระบุบ้านเลขที่ ถนน แขวง เขต จังหวัด และรหัสไปรษณีย์"></textarea>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" name="register" class="btn btn-register-main shadow-sm">CREATE ACCOUNT</button>
            </div>

            <p class="text-center mt-3 mb-0" style="font-size: 0.95rem; color: rgba(255,255,255,0.8);">
                Already a member? <a href="login.php" class="login-link">Log In.</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>