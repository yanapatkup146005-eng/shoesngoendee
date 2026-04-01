<?php
session_start();
include('db.php');

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_now = $_SESSION['username'];

// 2. จัดการ Action ต่างๆ ในตะกร้าสินค้า
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $key = $_GET['key'] ?? '';

    // --- กรณี: ปรับเพิ่ม/ลด จำนวนสินค้า (ปุ่ม + และ -) ---
    if (($action == 'increase' || $action == 'decrease') && !empty($key)) {
        if (isset($_SESSION['cart'][$key])) {
            if ($action == 'increase') {
                $_SESSION['cart'][$key]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$key]['quantity'] -= 1;
                if ($_SESSION['cart'][$key]['quantity'] < 1) unset($_SESSION['cart'][$key]);
            }
        }
        header("Location: cart.php");
        exit();
    }

    // --- กรณี: เพิ่มสินค้าลงตะกร้า ---
    if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $size = mysqli_real_escape_string($conn, $_POST['size']);
        $color = mysqli_real_escape_string($conn, $_POST['color']);
        
        $sql = "SELECT * FROM products WHERE id = '$id'";
        $result = mysqli_query($conn, $sql);
        $product = mysqli_fetch_assoc($result);
        
        if ($product) {
            $image_to_cart = $product['image']; 

            // ตรวจสอบรูปภาพเฉพาะสีจาก image_variants
            if (!empty($product['image_variants'])) {
                $v_pairs = explode(',', $product['image_variants']);
                foreach ($v_pairs as $pair) {
                    $ex = explode(':', $pair);
                    if (count($ex) == 2 && trim($ex[0]) == $color) {
                        $image_to_cart = trim($ex[1]); 
                        break;
                    }
                }
            }

            $cart_key = $id . "_" . $size . "_" . $color;
            if (!isset($_SESSION['cart'][$cart_key])) {
                $_SESSION['cart'][$cart_key] = [
                    'id' => $id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'size' => $size,
                    'color' => $color,
                    'image' => $image_to_cart,
                    'quantity' => 1
                ];
            } else {
                $_SESSION['cart'][$cart_key]['quantity'] += 1;
            }
        }
        header("Location: cart.php");
        exit();
    }

    if ($action == 'remove') { unset($_SESSION['cart'][$key]); header("Location: cart.php"); exit(); }
    if ($action == 'clear') { unset($_SESSION['cart']); header("Location: cart.php"); exit(); }
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
    <title>ตะกร้าสินค้า - SHORE STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .cart-card { border: none; border-radius: 20px; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.03); margin-bottom: 15px; overflow: hidden; }
        .cart-product-img { width: 100px; height: 100px; object-fit: contain; background: #f8f9fa; border-radius: 15px; padding: 10px; }
        .btn-qty { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: 0.2s; border: 1px solid #dee2e6; background: white; color: #333; text-decoration: none; }
        .btn-qty:hover { background: #4361ee; color: white; border-color: #4361ee; }
        .btn-checkout { background: #1a1a1a; color: white; padding: 16px; border-radius: 15px; font-weight: bold; width: 100%; border: none; transition: 0.3s; text-decoration: none; display: block; text-align: center; }
        .btn-checkout:hover { background: #4361ee; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(67,97,238,0.2); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-5 shadow">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="index.php">
            <i class="fas fa-shoe-prints me-2" style="color: #4895ef;"></i> SHORE STORE
        </a>
        <div class="ms-auto text-white small">
            สวัสดีคุณ, <strong><?php echo htmlspecialchars($user_data['firstname'] ?? $user_now); ?></strong>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h2 class="fw-bold m-0 text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>ตะกร้าสินค้า</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <a href="cart.php?action=clear" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('คุณต้องการล้างตะกร้าสินค้าทั้งหมด?')">
                <i class="fas fa-trash-alt me-1"></i> ล้างตะกร้า
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php 
                $total_price = 0;
                foreach ($_SESSION['cart'] as $key => $item): 
                    $price = $item['price'] ?? 0;
                    $quantity = $item['quantity'] ?? 0;
                    $sub_total = $price * $quantity;
                    $total_price += $sub_total;
                ?>
                <div class="card cart-card p-3">
                    <div class="row align-items-center g-3">
                        <div class="col-4 col-md-2 text-center">
                            <img src="image/<?php echo htmlspecialchars($item['image'] ?? 'no_image.jpg'); ?>" class="cart-product-img">
                        </div>
                        <div class="col-8 col-md-5">
                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name'] ?? 'สินค้า'); ?></h6>
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border">ไซส์: <?php echo $item['size']; ?></span>
                                <span class="badge bg-light text-dark border">สี: <?php echo $item['color']; ?></span>
                            </div>
                            <div class="fw-bold text-primary">฿<?php echo number_format($price, 2); ?></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center justify-content-md-center">
                                <a href="cart.php?action=decrease&key=<?php echo $key; ?>" class="btn-qty"><i class="fas fa-minus small"></i></a>
                                <span class="mx-3 fw-bold fs-5"><?php echo $quantity; ?></span>
                                <a href="cart.php?action=increase&key=<?php echo $key; ?>" class="btn-qty"><i class="fas fa-plus small"></i></a>
                            </div>
                        </div>
                        <div class="col-6 col-md-2 text-end">
                            <div class="fw-bold text-dark mb-1">฿<?php echo number_format($sub_total, 2); ?></div>
                            <a href="cart.php?action=remove&key=<?php echo $key; ?>" class="text-danger small text-decoration-none" onclick="return confirm('ลบรายการนี้?')">
                                <i class="fas fa-times me-1"></i> ลบออก
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="card cart-card p-4 shadow-sm">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">สรุปคำสั่งซื้อ</h5>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>ยอดรวมสินค้า</span>
                        <span>฿<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-success fw-bold">
                        <span>ค่าจัดส่ง</span>
                        <span>ฟรี</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 h4 fw-bold text-primary">
                        <span>ยอดสุทธิ</span>
                        <span>฿<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <a href="save_order.php" class="btn btn-checkout mb-3" onclick="return confirm('ยืนยันการสั่งซื้อสินค้าทั้งหมดใช่หรือไม่?')">
                        สั่งซื้อสินค้า <i class="fas fa-check-circle ms-1"></i>
                    </a>
                    
                    <a href="index.php" class="btn btn-outline-dark w-100 py-2 rounded-pill btn-sm shadow-sm">
                        <i class="fas fa-shopping-bag me-2"></i> เลือกซื้อสินค้าต่อ
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-5 shadow-sm border mt-5">
            <i class="fas fa-shopping-basket fa-4x text-muted mb-4 opacity-25"></i>
            <h4 class="text-muted fw-bold">ตะกร้าของคุณยังว่างเปล่า</h4>
            <p class="text-secondary">ไปเลือกช้อปรองเท้าที่คุณถูกใจกันเลย!</p>
            <a href="index.php" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold shadow">
                <i class="fas fa-home me-2"></i> กลับไปหน้าหลัก
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>