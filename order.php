<?php
session_start();
include('db.php');

// ตรวจสอบว่ามีสินค้าในตะกร้าไหม
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$user_now = $_SESSION['username'];
$sql_user = "SELECT * FROM users WHERE username = '$user_now'";
$user_result = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($user_result);

// จัดการเมื่อกดปุ่มบันทึกการสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_order'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $total_price = $_POST['total_price'];

    // 1. บันทึกลงตาราง orders
    $sql_order = "INSERT INTO orders (username, firstname, lastname, total_price, address, phone)
                  VALUES ('$user_now', '$fname', '$lname', '$total_price', '$address', '$phone')";
    
    if (mysqli_query($conn, $sql_order)) {
        $order_id = mysqli_insert_id($conn);

        // 2. บันทึกรายการสินค้าลง order_details (ปรับปรุงจุดนี้)
        foreach ($_SESSION['cart'] as $key => $item) {
            $pid = mysqli_real_escape_string($conn, $item['id']);
            $qty = $item['qty'];
            $size = mysqli_real_escape_string($conn, $item['size']);
            $color = mysqli_real_escape_string($conn, $item['color']);

            $p_sql = "SELECT price FROM products WHERE id = '$pid'";
            $p_res = mysqli_query($conn, $p_sql);
            $p_data = mysqli_fetch_assoc($p_res);
            $price = $p_data['price'];

            // เพิ่ม selected_size และ selected_color ลงในคำสั่ง INSERT
            $sql_detail = "INSERT INTO order_details (order_id, product_id, selected_size, selected_color, quantity, price) 
                           VALUES ('$order_id', '$pid', '$size', '$color', '$qty', '$price')";
            mysqli_query($conn, $sql_detail);
        }

        unset($_SESSION['cart']);
        echo "<script>alert('สั่งซื้อสำเร็จ! เลขที่ใบสั่งซื้อของคุณคือ #$order_id'); window.location='my_order.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสั่งซื้อ - SHOE STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .checkout-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .navbar-checkout { background-color: #1a1a1a; border-bottom: 3px solid #4361ee; }
        .navbar-brand { color: white !important; font-weight: bold; }
        .navbar-brand i { color: #4361ee; }
        .btn-confirm { background-color: #4361ee; color: white; font-weight: bold; border-radius: 12px; padding: 15px; border: none; transition: 0.3s; }
        .btn-confirm:hover { background-color: #3f37c9; transform: translateY(-2px); }
        .badge-opt { background: #f0f2f5; color: #555; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; margin-right: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<nav class="navbar navbar-checkout py-3 mb-5">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shoe-prints me-2"></i> SHOE STORE
        </a>
        <a href="cart.php" class="text-white-50 text-decoration-none small">
            <i class="fas fa-chevron-left me-1"></i> กลับไปตะกร้าสินค้า
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card checkout-card p-4">
                <h3 class="fw-bold mb-4 text-center">สรุปรายการสั่งซื้อ</h3>
                
                <div class="table-responsive mb-4">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>รายการสินค้า</th>
                                <th class="text-center">Size&Color</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-end">ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            // แก้ไข Loop ให้ดึงข้อมูลจากโครงสร้างใหม่
                            foreach ($_SESSION['cart'] as $key => $item): 
                                $pid = mysqli_real_escape_string($conn, $item['id']);
                                $res = mysqli_query($conn, "SELECT * FROM products WHERE id = '$pid'");
                                $row = mysqli_fetch_assoc($res);
                                $subtotal = $row['price'] * $item['qty'];
                                $grand_total += $subtotal;
                            ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $row['name']; ?></span></td>
                                <td class="text-center">
                                    <span class="badge-opt">S: <?php echo $item['size']; ?></span>
                                    <span class="badge-opt">C: <?php echo $item['color']; ?></span>
                                </td>
                                <td class="text-center">x <?php echo $item['qty']; ?></td>
                                <td class="text-end fw-bold">฿<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="3" class="py-3 fw-bold fs-5 text-end">ยอดรวมสุทธิ</td>
                                <td class="text-end py-3 fw-bold fs-5 text-primary">฿<?php echo number_format($grand_total, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-truck me-2 text-primary"></i> ข้อมูลการจัดส่ง</h5>

                <form method="POST">
                    <input type="hidden" name="total_price" value="<?php echo $grand_total; ?>">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อจริง</label>
                            <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user_data['firstname'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user_data['lastname'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ที่อยู่สำหรับการจัดส่ง</label>
                        <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="submit_order" class="btn btn-confirm btn-lg shadow">
                            ยืนยันการสั่งซื้อและชำระเงิน <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>