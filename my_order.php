<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_now = $_SESSION['username'];

// ระบบยกเลิกออเดอร์
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $check = mysqli_query($conn, "SELECT status FROM orders WHERE id = $cancel_id AND username = '$user_now'");
    $order_data = mysqli_fetch_assoc($check);

    if ($order_data && $order_data['status'] == 'pending') {
        mysqli_query($conn, "DELETE FROM order_details WHERE order_id = $cancel_id");
        mysqli_query($conn, "DELETE FROM orders WHERE id = $cancel_id");
        echo "<script>alert('ยกเลิกเรียบร้อย'); window.location='my_order.php';</script>";
    }
}

$sql_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user_now'");
$user_data = mysqli_fetch_assoc($sql_user);

// ดึงข้อมูลออเดอร์โดยใช้ created_at
$sql = "SELECT * FROM orders WHERE username = '$user_now' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ - SHORE STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070');
            background-size: cover; background-attachment: fixed; color: white; font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        
        /* Navbar & Sidebar */
        .navbar { background: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .offcanvas { background-color: #1a1a1a !important; border-right: 2px solid #4361ee; }
        .list-group-item { background: transparent !important; color: white !important; border: none; padding: 15px 25px; transition: 0.3s; }
        .list-group-item:hover { background: rgba(67, 97, 238, 0.2) !important; color: #4895ef !important; }
        .list-group-item.active { background: #4361ee !important; border: none; }

        /* Order Cards */
        .order-card { 
            background: rgba(255, 255, 255, 0.08); 
            backdrop-filter: blur(15px); 
            border-radius: 20px; 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            margin-bottom: 20px; 
            transition: 0.3s; 
        }
        .order-card:hover { border-color: #4361ee; transform: translateY(-5px); }
        .status-badge { padding: 6px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: bold; }
        .bg-pending { background: #f39c12; }
        .bg-paid { background: #4361ee; }
        .bg-shipped { background: #2ecc71; }
        .price-total { color: #4895ef; font-size: 1.6rem; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-4">
    <div class="container">
        <button class="btn text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand text-white fw-bold" href="index.php">
            <i class="fas fa-shoe-prints me-2" style="color: #4895ef;"></i> SHORE STORE
        </a>
        <div class="ms-auto text-white-50 small">
            สวัสดีคุณ: <?php echo htmlspecialchars($user_data['firstname'] ?? $user_now); ?>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start text-white" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title fw-bold"><i class="fas fa-user-circle me-2"></i>เมนูผู้ใช้งาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-home me-3"></i>หน้าหลัก
            </a>
            <a href="my_order.php" class="list-group-item list-group-item-action active">
                <i class="fas fa-history me-3"></i>ประวัติการสั่งซื้อ
            </a>
            <a href="cart.php" class="list-group-item list-group-item-action">
                <i class="fas fa-shopping-cart me-3"></i>ตะกร้าสินค้า
            </a>
            <hr class="mx-3 my-2 border-secondary">
            <a href="index.php?logout=1" class="list-group-item list-group-item-action text-danger">
                <i class="fas fa-sign-out-alt me-3"></i>ออกจากระบบ
            </a>
        </div>
    </div>
</div>

<div class="container">
    <div class="d-flex align-items-center mb-4">
        <div style="width: 5px; height: 35px; background: #4361ee; margin-right: 15px; border-radius: 10px;"></div>
        <h2 class="fw-bold m-0">ประวัติการสั่งซื้อ</h2>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($order = mysqli_fetch_assoc($result)): 
            // กำหนดสี Badge ตามสถานะ
            $status = $order['status'];
            $st_class = "bg-pending"; $st_text = "รอตรวจสอบ";
            if($status == 'paid') { $st_class = "bg-paid"; $st_text = "ชำระเงินแล้ว"; }
            if($status == 'shipped') { $st_class = "bg-shipped"; $st_text = "จัดส่งแล้ว"; }
        ?>
            <div class="order-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="mb-2">
                            <span class="status-badge <?php echo $st_class; ?>">
                                ออเดอร์ #<?php echo $order['id']; ?> | <?php echo $st_text; ?>
                            </span>
                        </div>
                        <p class="mb-1 text-white-50 small">
                            <i class="far fa-calendar-alt me-2"></i>วันที่สั่ง: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>ส่งที่: <?php echo htmlspecialchars($order['address']); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="text-white-50 small">ยอดชำระสุทธิ</div>
                        <div class="price-total">฿<?php echo number_format($order['total_price'], 2); ?></div>
                        <div class="mt-3">
                            <?php if($order['status'] == 'pending'): ?>
                                <a href="my_order.php?cancel_id=<?php echo $order['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('คุณต้องการยกเลิกออเดอร์นี้ใช่หรือไม่?')">ยกเลิก</a>
                            <?php endif; ?>
                            <a href="order_details_user.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4 ms-2">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5 shadow-lg" style="background: rgba(255,255,255,0.05); border-radius: 30px;">
            <i class="fas fa-box-open fa-4x text-white-50 mb-4"></i>
            <h4 class="text-white-50">คุณยังไม่มีประวัติการสั่งซื้อสินค้า</h4>
            <a href="index.php" class="btn btn-primary mt-3 px-5 rounded-pill shadow">ไปช้อปปิ้งเลยตอนนี้</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>