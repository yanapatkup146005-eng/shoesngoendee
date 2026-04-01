<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: ff.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// ดึงรายละเอียดสินค้าพร้อมข้อมูลรูปภาพสำรอง (image_variants)
$sql = "SELECT od.*, p.name, p.image as main_image, p.image_variants 
        FROM order_details od 
        JOIN products p ON od.product_id = p.id 
        WHERE od.order_id = $order_id";
$items = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดออเดอร์ #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .detail-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .product-img { width: 70px; height: 70px; object-fit: contain; background: #fff; border-radius: 8px; padding: 5px; }
        .badge-opt { font-size: 0.8rem; padding: 6px 12px; border-radius: 50px; }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="card detail-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0 text-dark">
                    <i class="bi bi-box-seam me-2 text-primary"></i>รายการสินค้าในออเดอร์ #<?php echo $order_id; ?>
                </h4>
                <span class="badge bg-primary rounded-pill px-3">Admin View</span>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="120">รูปสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th>ตัวเลือกที่ลูกค้าเลือก</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total = 0;
                        while($item = mysqli_fetch_assoc($items)): 
                            // 1. Logic การเลือกรูปภาพตามสีที่ลูกค้าเลือก
                            $display_image = $item['main_image']; 
                            $selected_color = $item['color'] ?? ''; 
                            $selected_size = $item['size'] ?? '';

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

                            $display_qty = $item['qty'] ?? 0;
                            $display_price = $item['price_at_purchase'] ?? 0;
                            $subtotal = $display_price * $display_qty;
                            $grand_total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="image/<?php echo htmlspecialchars($display_image); ?>" class="product-img border shadow-sm">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($item['name']); ?></div>
                                <small class="text-muted">Product ID: #<?php echo $item['product_id']; ?></small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-opt">
                                        <i class="bi bi-palette me-1"></i>สี: <?php echo htmlspecialchars($selected_color ?: '-'); ?>
                                    </span>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 badge-opt">
                                        <i class="bi bi-rulers me-1"></i>ไซส์: <?php echo htmlspecialchars($selected_size ?: '-'); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center fw-bold">x <?php echo $display_qty; ?></td>
                            <td class="text-end fw-bold text-primary">฿<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold py-3 fs-5">ยอดรวมสุทธิทั้งสิ้น:</td>
                            <td class="text-end fw-bold text-primary py-3 fs-4">฿<?php echo number_format($grand_total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <!-- <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i></p> -->
                <a href="ff.php" class="btn btn-dark px-5 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>กลับหน้าจัดการออเดอร์
                </a>
            </div>
        </div>
    </div>
</body>
</html>