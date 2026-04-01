<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: my_order.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_now = $_SESSION['username'];

// ดึงข้อมูลออเดอร์
$sql_order = "SELECT * FROM orders WHERE id = $order_id AND username = '$user_now'";
$res_order = mysqli_query($conn, $sql_order);
$order = mysqli_fetch_assoc($res_order);

if (!$order) {
    header("Location: my_order.php");
    exit();
}

// ดึงรายการสินค้าพร้อมตัวแปรภาพ
$sql_details = "SELECT od.*, p.name, p.image, p.image_variants 
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = $order_id";
$res_details = mysqli_query($conn, $sql_details);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)),
                              url('https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070');
            background-size: cover; background-attachment: fixed; background-position: center;
            font-family: 'Segoe UI', sans-serif; color: white; min-height: 100vh;
        }
        .navbar { background-color: #1a1a1a !important; border-bottom: 3px solid #4361ee; }
        .glass-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(15px);
            border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 30px; overflow: hidden;
        }
        .track-container { display: flex; justify-content: space-between; position: relative; margin-bottom: 40px; }
        .track-step { flex: 1; text-align: center; position: relative; z-index: 2; }
        .step-icon { width: 45px; height: 45px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; border: 3px solid #444; transition: 0.3s; }
        .track-step.active .step-icon { background: #4361ee; border-color: #4895ef; box-shadow: 0 0 20px rgba(67,97,238,0.6); }
        .track-step.active .step-text { color: #4895ef; font-weight: bold; }
        .track-container::after { content: ""; position: absolute; top: 22px; left: 10%; width: 80%; height: 3px; background: #333; z-index: 1; }
        .product-img { width: 80px; height: 80px; object-fit: contain; background: rgba(255,255,255,0.05); border-radius: 15px; padding: 5px; }
        .text-accent { color: #4895ef; }
        .info-box { background: rgba(0,0,0,0.3); border-radius: 20px; padding: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top mb-4 shadow">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="index.php">
                <i class="fas fa-shoe-prints me-2" style="color: #4895ef;"></i> SHORE STORE
            </a>
            <a href="my_order.php" class="btn btn-outline-light btn-sm rounded-pill px-4">
                <i class="fas fa-chevron-left me-2"></i>กลับไปหน้ารวม
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="glass-card p-4 p-md-5 shadow-lg">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
                <div>
                    <h2 class="fw-bold m-0">คำสั่งซื้อ #<?php echo $order_id; ?></h2>
                    <p class="text-white-50 m-0">สั่งซื้อเมื่อ: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?> น.</p>
                </div>
                <div class="info-box py-2 px-4">
                    <span class="text-white-50 small d-block">ยอดรวมสุทธิ</span>
                    <span class="h3 fw-bold text-accent">฿<?php echo number_format($order['total_price'], 2); ?></span>
                </div>
            </div>

            <div class="track-container">
                <?php $s = $order['status']; ?>
                <div class="track-step <?php echo ($s=='pending'||$s=='paid'||$s=='shipped')?'active':''; ?>">
                    <div class="step-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="step-text small">รอตรวจสอบ</div>
                </div>
                <div class="track-step <?php echo ($s=='paid'||$s=='shipped')?'active':''; ?>">
                    <div class="step-icon"><i class="fas fa-wallet"></i></div>
                    <div class="step-text small">ชำระเงินแล้ว</div>
                </div>
                <div class="track-step <?php echo ($s=='shipped')?'active':''; ?>">
                    <div class="step-icon"><i class="fas fa-truck-moving"></i></div>
                    <div class="step-text small">จัดส่งแล้ว</div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-7">
                    <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2">รายการสินค้า</h5>
                    <?php while ($item = mysqli_fetch_assoc($res_details)): 
                        $display_image = $item['image'];
                        $selected_color = $item['color'] ?? ''; // แก้ไข Error Undefined key
                        $selected_size = $item['size'] ?? '';

                        // เปลี่ยนรูปตามสีที่เลือก
                        if (!empty($selected_color) && !empty($item['image_variants'])) {
                            $variants = explode(',', $item['image_variants']);
                            foreach ($variants as $v) {
                                $part = explode(':', $v);
                                if (count($part) == 2 && trim($part[0]) == $selected_color) {
                                    $display_image = trim($part[1]);
                                    break;
                                }
                            }
                        }
                    ?>
                        <div class="d-flex align-items-center mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10">
                            <img src="image/<?php echo htmlspecialchars($display_image); ?>" class="product-img me-3">
                            <div class="flex-grow-1">
                                <div class="fw-bold fs-5"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="text-white-50 small">
                                    <?php if($selected_color) echo "สี: " . htmlspecialchars($selected_color); ?> 
                                    <?php if($selected_size) echo " | ไซส์: " . htmlspecialchars($selected_size); ?> 
                                    | จำนวน x<?php echo $item['qty']; ?>
                                </div>
                            </div>
                            <div class="text-end fw-bold text-accent fs-5">
                                ฿<?php echo number_format($item['price_at_purchase'] * $item['qty'], 2); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="col-lg-5">
                    <h5 class="fw-bold mb-4 border-bottom border-secondary pb-2">ข้อมูลการจัดส่ง</h5>
                    <div class="info-box shadow-sm">
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">ชื่อผู้รับ</label>
                            <span class="fw-bold fs-5 text-info"><?php echo htmlspecialchars($order['firstname'] . " " . $order['lastname']); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">เบอร์โทรศัพท์</label>
                            <span class="fw-bold"><?php echo htmlspecialchars($order['phone']); ?></span>
                        </div>
                        <div class="mb-0">
                            <label class="text-white-50 small d-block">ที่อยู่จัดส่ง</label>
                            <span class="text-white-50 small"><?php echo nl2br(htmlspecialchars($order['address'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>