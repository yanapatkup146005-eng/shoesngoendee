<?php
session_start();
include('db.php');

// 1. ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. ระบบอัปเดตสถานะ (ทำงานร่วมกับโครงสร้าง ENUM ใน SQL)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    // ต้องเป็นตัวพิมพ์เล็ก: pending, paid, shipped, cancelled ตามโครงสร้าง DB
    $new_status = mysqli_real_escape_string($conn, $_POST['status']); 
    
    $sql_update = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('อัปเดตสถานะออเดอร์ #$order_id เรียบร้อยแล้ว'); window.location='ff.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// 3. ระบบลบรายการสั่งซื้อ
if (isset($_GET['delete_order_id'])) {
    $order_id = intval($_GET['delete_order_id']); 
    mysqli_query($conn, "DELETE FROM order_details WHERE order_id = $order_id");
    mysqli_query($conn, "DELETE FROM orders WHERE id = $order_id");
    header("Location: ff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER MANAGEMENT - SHOE STORE ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #4361ee; --bg-color: #f8f9fa; }
        body { background-color: var(--bg-color); font-family: 'Segoe UI', sans-serif; }
        .navbar-admin { background-color: #1a1a1a; border-bottom: 2px solid var(--primary-color); }
        
        .order-item-card {
            background: white; border-radius: 16px; border: none;
            transition: 0.3s; margin-bottom: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .order-item-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

        /* Badge ตามค่า ENUM ใน SQL */
        .badge-status { padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .bg-pending { background-color: #fff3cd; color: #856404; }
        .bg-paid { background-color: #d1e7dd; color: #0f5132; }
        .bg-shipped { background-color: #cfe2ff; color: #084298; }
        .bg-cancelled { background-color: #f8d7da; color: #842029; }

        .btn-update { background-color: var(--primary-color); color: white; border-radius: 10px; border: none; font-weight: 600; padding: 6px 20px; }
        .btn-update:hover { background-color: #3f37c9; color: white; }
        .icon-user-bg { width: 45px; height: 45px; background: #f0f2f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-admin py-3 mb-5 shadow-sm">
    <div class="container">
        <span class="navbar-brand text-white fw-bold">
            <i class="bi bi-box-seam me-2 text-primary"></i> ORDER MANAGEMENT
        </span>
        <a href="admin.index.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Dashboard</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row">
        <?php
        $orders_query = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
        if(mysqli_num_rows($orders_query) > 0):
            while($row = mysqli_fetch_assoc($orders_query)):
                $current_status = $row['status'];
        ?>
        <div class="col-12">
            <div class="card order-item-card p-3">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-3">
                        <h5 class="fw-bold text-primary mb-0">#<?php echo $row['id']; ?></h5>
                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small>
                    </div>

                    <div class="col-lg-3 col-md-5">
                        <div class="d-flex align-items-center">
                            <div class="icon-user-bg me-3 text-secondary"><i class="bi bi-person fs-4"></i></div>
                            <div>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['firstname'].' '.$row['lastname']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($row['phone']); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 text-md-center">
                        <div class="h5 fw-bold mb-1">฿<?php echo number_format($row['total_price'], 2); ?></div>
                        <span class="badge-status bg-<?php echo $current_status; ?>"><?php echo strtoupper($current_status); ?></span>
                    </div>

                    <div class="col-lg-3 col-md-8">
                        <form method="POST" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <select name="status" class="form-select form-select-sm rounded-3">
                                <option value="pending" <?php if($current_status == 'pending') echo 'selected'; ?>>รอตรวจสอบ</option>
                                <option value="paid" <?php if($current_status == 'paid') echo 'selected'; ?>>ชำระเงินแล้ว</option>
                                <option value="shipped" <?php if($current_status == 'shipped') echo 'selected'; ?>>จัดส่งแล้ว</option>
                                <option value="cancelled" <?php if($current_status == 'cancelled') echo 'selected'; ?>>ยกเลิก</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-update btn-sm shadow-sm">Update</button>
                        </form>
                    </div>

                    <div class="col-lg-2 col-md-4 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="order_details.php?order_id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye"></i> ดูออเดอร์
                            </a>
                            <a href="ff.php?delete_order_id=<?php echo $row['id']; ?>" class="text-danger p-2" onclick="return confirm('ลบออเดอร์นี้?')">
                                <i class="bi bi-trash fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5 text-muted"><h5>ยังไม่มีรายการสั่งซื้อ</h5></div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>