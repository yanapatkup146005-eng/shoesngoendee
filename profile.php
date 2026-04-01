<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_now = $_SESSION['username'];
$msg = "";

if (isset($_POST['update_profile'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql_update = "UPDATE users SET 
                    firstname = '$fname', 
                    lastname = '$lname',    
                    email = '$email',
                    phone = '$phone', 
                    address = '$address' 
                  WHERE username = '$user_now'";

    if (mysqli_query($conn, $sql_update)) {
        $msg = "<div class='alert alert-success alert-dismissible fade show py-2' role='alert'>
                    <i class='fas fa-check-circle me-2'></i> บันทึกข้อมูลสำเร็จ!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } else {
        $msg = "<div class='alert alert-danger py-2'>เกิดข้อผิดพลาด: " . mysqli_error($conn) . "</div>";
    }
}

$sql_user = "SELECT * FROM users WHERE username = '$user_now'";
$user_result = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบัญชี - SHOE STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                              url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Segoe UI', sans-serif;
            color: white;
            min-height: 100vh;
        }

        .navbar { background-color: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .navbar-brand { color: white !important; font-weight: bold; }

        .profile-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .form-label { color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 600; }
        .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: #333;
        }

        .btn-save {
            background-color: #4361ee;
            color: white;
            border: none;
            padding: 14px;
            font-weight: bold;
            border-radius: 12px;
            transition: 0.3s;
        }
        .btn-save:hover { background-color: #3f37c9; transform: translateY(-2px); color: white; }

        .offcanvas { border-right: 3px solid #4361ee; }
        .list-group-item { transition: 0.3s; }
        .list-group-item:hover { background-color: #4361ee !important; color: white !important; padding-left: 30px; }
        
        .profile-icon {
            font-size: 3.5rem;
            color: #4361ee;
            background: white;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 25px;
            border: 4px solid rgba(67, 97, 238, 0.5);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-4">
    <div class="container">
        <button class="btn text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shoe-prints me-2" style="color: #4895ef;"></i> SHOE STORE
        </a>
        <div class="ms-auto text-white-50 small d-none d-sm-block">
            <i class="fas fa-id-card me-1"></i> ข้อมูลส่วนตัวของคุณ
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title fw-bold">เมนูผู้ใช้งาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white border-secondary py-3">
                <i class="fas fa-home me-3 text-info"></i> หน้าหลักร้านค้า
            </a>
            <a href="profile.php" class="list-group-item list-group-item-action bg-dark text-white border-secondary py-3 active" style="background-color: #4361ee !important; border: none;">
                <i class="fas fa-user-cog me-3"></i> จัดการบัญชี
            </a>
            <a href="my_order.php" class="list-group-item list-group-item-action bg-dark text-white border-secondary py-3">
                <i class="fas fa-history me-3 text-warning"></i> ประวัติการสั่งซื้อ
            </a>
            <a href="index.php?logout=1" class="list-group-item list-group-item-action bg-dark text-white border-secondary py-3 text-danger">
                <i class="fas fa-sign-out-alt me-3"></i> ออกจากระบบ
            </a>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3 class="text-center fw-bold mb-4">ตั้งค่าบัญชีของคุณ</h3>
                
                <?php echo $msg; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อจริง</label>
                            <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user_data['firstname'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user_data['lastname'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label">อีเมลติดต่อ</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ที่อยู่จัดส่งสินค้า</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="ระบุเลขที่บ้าน ถนน จังหวัด และรหัสไปรษณีย์"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid mt-2">
                        <button type="submit" name="update_profile" class="btn btn-save shadow">
                            <i class="fas fa-save me-2"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form> 
            </div> 
        </div>
     </div>
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>