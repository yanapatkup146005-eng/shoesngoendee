<?php
session_start();
include('db.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_now = $_SESSION['username'];
$sql_user = "SELECT * FROM users WHERE username = '$user_now'";
$user_result = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($user_result);

// --- ระบบแยกตามแบรนด์ ---
$brand_filter = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : 'all';

if ($brand_filter == 'all') {
    $sql_products = "SELECT * FROM products ORDER BY id DESC";
} else {
    $sql_products = "SELECT * FROM products WHERE brand = '$brand_filter' ORDER BY id DESC";
}
$product_result = mysqli_query($conn, $sql_products);

// ดึงรายชื่อแบรนด์ที่มีในระบบมาทำปุ่มกด
$sql_brands = "SELECT DISTINCT brand FROM products WHERE brand != ''";
$brand_list = mysqli_query($conn, $sql_brands);

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOE STORE - แบรนด์รองเท้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .navbar-brand { color: white !important; font-weight: bold; display: flex; align-items: center; }
        .navbar-brand i { color: #4895ef; margin-right: 10px; }
        
        /* สไตล์ปุ่มแบรนด์ */
        .brand-pill {
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 15px;
            background: white;
            color: #333;
            font-weight: 600;
            border: 1px solid #eee;
            transition: 0.3s;
            display: inline-block;
        }
        .brand-pill:hover, .brand-pill.active {
            background: #1a1a1a;
            color: #4895ef;
            border-color: #4361ee;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product-card {
            border: none; border-radius: 20px; transition: 0.3s;
            background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;
        }
        .product-card:hover { transform: translateY(-8px); }
        .card-img-top { height: 250px; object-fit: contain; padding: 25px; background-color: #f8f9fa; }
        .price-tag { color: #4361ee; font-size: 1.4rem; font-weight: 700; }
        .btn-view { background-color: #1a1a1a; color: white; border-radius: 12px; padding: 12px; text-decoration: none; display: block; text-align: center; font-weight: bold; }
        .btn-view:hover { background-color: #4361ee; }
        .offcanvas { border-right: 3px solid #4361ee; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-4">
    <div class="container">
        <button class="btn text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-shoe-prints"></i>
            <span>SHOE STORE</span>
        </a>
        <div class="ms-auto">
            <a href="cart.php" class="nav-link text-white position-relative">
                <i class="fas fa-shopping-cart fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMenu" style="width: 280px;">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title fw-bold">เมนูผู้ใช้งาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="profile.php" class="list-group-item list-group-item-action bg-dark text-white py-3">
                <i class="fas fa-user-cog me-3 text-info"></i> จัดการบัญชีส่วนตัว
            </a>
            <a href="my_order.php" class="list-group-item list-group-item-action bg-dark text-white py-3">
                <i class="fas fa-history me-3 text-warning"></i> ประวัติการสั่งซื้อ
            </a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="admin.index.php" class="list-group-item list-group-item-action bg-dark text-white py-3">
                    <i class="fas fa-tools me-3 text-danger"></i> แผงควบคุม Admin
                </a>
            <?php endif; ?>
            <div class="p-4 mt-auto">
                <a href="?logout=1" class="btn btn-outline-danger w-100">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="mb-5 text-center">
        <h4 class="fw-bold mb-4">หมวดสินค้า</h4>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="index.php?brand=all" class="brand-pill <?php echo $brand_filter == 'all' ? 'active' : ''; ?>">ทั้งหมด</a>
            <?php while($b = mysqli_fetch_assoc($brand_list)): ?>
                <a href="index.php?brand=<?php echo urlencode($b['brand']); ?>" 
                   class="brand-pill <?php echo $brand_filter == $b['brand'] ? 'active' : ''; ?>">
                   <?php echo strtoupper($b['brand']); ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="d-flex align-items-center mb-4">
        <div style="width: 5px; height: 30px; background: #4361ee; margin-right: 15px; border-radius: 5px;"></div>
        <h2 class="fw-bold m-0"><?php echo $brand_filter == 'all' ? 'สินค้าทั้งหมด' : 'แบรนด์: ' . strtoupper($brand_filter); ?></h2>
    </div>
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if(mysqli_num_rows($product_result) > 0): ?>
            <?php while($product = mysqli_fetch_assoc($product_result)): ?>
                <div class="col">
                    <div class="card h-100 product-card shadow-sm">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                            <img src="image/<?php echo $product['image']; ?>" class="card-img-top">
                        </a>
                        <div class="card-body d-flex flex-column text-center">
                            <small class="text-primary fw-bold text-uppercase mb-1"><?php echo $product['brand']; ?></small>
                            <h5 class="fw-bold text-dark mb-2"><?php echo $product['name']; ?></h5>
                            <div class="price-tag mb-3">฿<?php echo number_format($product['price'], 2); ?></div>
                            <div class="mt-auto">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-view">
                                    <i class="fas fa-eye me-2"></i> รายละเอียด
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h5 class="text-muted">ไม่พบสินค้าแบรนด์นี้ในขณะนี้</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>