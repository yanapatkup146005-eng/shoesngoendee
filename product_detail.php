<?php
session_start();
include('db.php');

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_now = $_SESSION['username'];

// ดึงข้อมูลผู้ใช้สำหรับ Navbar
$sql_user = "SELECT * FROM users WHERE username = '$user_now'";
$user_result = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($user_result);

// 2. ดึงข้อมูลสินค้าจาก ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - SHORE STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .navbar-brand { color: white !important; font-weight: bold; display: flex; align-items: center; }
        .offcanvas { border-right: 3px solid #4361ee; }
        .list-group-item:hover { background-color: #4361ee !important; color: white !important; padding-left: 30px; transition: 0.3s; }

        /* Style รายละเอียดสินค้า */
        .detail-card { border: none; border-radius: 30px; background: white; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .product-img-large { width: 100%; height: 500px; object-fit: contain; background: #ffffff; padding: 40px; transition: opacity 0.4s ease-in-out; }
        .price-tag { color: #4361ee; font-size: 2.8rem; font-weight: 800; }
        .description-box { background: #fff; padding: 20px; border-radius: 15px; border-left: 5px solid #4361ee; color: #555; line-height: 1.8; margin-bottom: 20px; }
        .btn-add-cart { background: #1a1a1a; color: white; padding: 18px; border-radius: 18px; font-weight: bold; border: none; transition: 0.3s; }
        .btn-add-cart:hover { background: #4361ee; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-5 shadow">
    <div class="container">
        <button class="btn text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand text-white" href="index.php">
            <i class="fas fa-shoe-prints me-2" style="color: #4895ef;"></i> SHORE STORE
        </a>
        <div class="ms-auto d-flex align-items-center">
            <a href="cart.php" class="text-white text-decoration-none me-3">
                <i class="fas fa-shopping-cart fa-lg"></i>
            </a>
            <div class="text-white-50 small d-none d-md-block">
                สวัสดี, <?php echo htmlspecialchars($user_data['firstname'] ?? $user_now); ?>
            </div>
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
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white py-3">
                <i class="fas fa-home me-3 text-info"></i> หน้าหลักร้านค้า
            </a>
            <a href="my_order.php" class="list-group-item list-group-item-action bg-dark text-white py-3">
                <i class="fas fa-history me-3 text-warning"></i> ประวัติการสั่งซื้อ
            </a>
            <a href="index.php?logout=1" class="list-group-item list-group-item-action bg-dark text-white py-3 text-danger">
                <i class="fas fa-sign-out-alt me-3"></i> ออกจากระบบ
            </a>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <div class="detail-card sticky-top" style="top: 100px;">
                <img src="image/<?php echo $product['image']; ?>" id="mainProductImg" class="product-img-large">
            </div>
        </div>

        <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-dark mb-2"><?php echo $product['name']; ?></h1>
            <div class="price-tag mb-4">฿<?php echo number_format($product['price'], 2); ?></div>

            <label class="fw-bold mb-2">รายละเอียดสินค้า</label>
            <div class="description-box shadow-sm">
                <?php echo nl2br(htmlspecialchars($product['description'] ?? 'ไม่มีรายละเอียด')); ?>
            </div>

            <form action="cart.php?action=add" method="POST">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold mb-2">ไซส์</label>
                        <select name="size" class="form-select py-3" required>
                            <?php 
                            $sizes = explode(',', $product['sizes']); 
                            foreach($sizes as $s) echo "<option value='".trim($s)."'>Size ".trim($s)."</option>";
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-2">สี</label>
                        <select name="color" id="colorSelect" class="form-select py-3" required>
                            <option value="" data-img="<?php echo $product['image']; ?>">กรุณาเลือกสี</option>
                            <?php 
                            // ดึงรายการสีหลัก
                            $colors_list = explode(',', $product['colors']); 
                            
                            // จัดการข้อมูลรูปภาพแยกตามสีจาก image_variants
                            $variants = [];
                            if (!empty($product['image_variants'])) {
                                $v_pairs = explode(',', $product['image_variants']);
                                foreach ($v_pairs as $pair) {
                                    $ex = explode(':', $pair);
                                    if (count($ex) == 2) { $variants[trim($ex[0])] = trim($ex[1]); }
                                }
                            }

                            foreach($colors_list as $c): 
                                $c = trim($c);
                                // ตรวจสอบว่าสีนี้มีรูประบุไว้ไหม ถ้าไม่มีให้ใช้รูปหลัก
                                $img_to_show = isset($variants[$c]) ? $variants[$c] : $product['image'];
                            ?>
                                <option value="<?php echo $c; ?>" data-img="<?php echo $img_to_show; ?>">
                                    <?php echo $c; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-add-cart w-100 shadow-lg">เพิ่มลงตะกร้า</button>
            </form>
        </div>
    </div>
</div>

<script>
// สคริปต์สลับรูปภาพตามสีที่ระบุใน image_variants
document.getElementById('colorSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const newImgName = selectedOption.getAttribute('data-img');
    const mainImg = document.getElementById('mainProductImg');
    
    const finalImg = newImgName ? newImgName : "<?php echo $product['image']; ?>";
    const newSrc = "image/" + finalImg;

    mainImg.style.opacity = 0;
    setTimeout(() => {
        mainImg.src = newSrc;
        mainImg.style.opacity = 1;
    }, 300);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>