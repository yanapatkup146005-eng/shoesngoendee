<?php
session_start();
include('db.php');

// 1. ตรวจสอบสิทธิ์ Admin (ถ้าไม่ใช่ admin ให้เด้งไปหน้า login)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. จัดการการลบสินค้า
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: admin.index.php");
    exit();
}

// 3. ดึงข้อมูลสรุปสำหรับ Dashboard
$count_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$count_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// 4. ดึงรายการสินค้าทั้งหมด
$result_products = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shoe Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .admin-sidebar { min-height: 100vh; background: #212529; color: white; }
        .nav-link { color: #adb5bd; }
        .nav-link:hover, .nav-link.active { color: white; background: #343a40; }
        .stat-card { border: none; border-left: 4px solid; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 admin-sidebar d-none d-md-block">
            <div class="p-3 text-center">
                <h4>👟 Shoe Admin</h4>
                <small class="text-success"><i class="bi bi-circle-fill"></i> Online: <?php echo $_SESSION['username']; ?></small>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item"><a href="admin.index.php" class="nav-link active p-3">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="add_product.php" class="nav-link p-3">➕ เพิ่มสินค้าใหม่</a></li>
                <li class="nav-item"><a href="index.php" class="nav-link p-3 text-info">🌐 ไปหน้าร้านค้า</a></li>
                <li class="nav-item"><a href="ff.php" class="nav-link p-3 text-warning">📦 จัดการคำสั่งซื้อ</a></li>
                <li class="nav-item"><a href="index.php?logout=1" class="nav-link p-3 text-danger">Logout</a></li>
            </ul>

        </div>

        <div class="col-md-10 p-4">
            <div class="alert alert-primary d-flex justify-content-between align-items-center shadow-sm">
                <span><i class="bi bi-shield-lock-fill"></i> <strong>โหมดผู้ดูแลระบบ:</strong> ยินดีต้อนรับคุณ <?php echo $_SESSION['username']; ?> เข้าสู่ระบบจัดการหลังบ้าน</span>
                <span class="badge bg-light text-primary"><?php echo date('d M Y'); ?></span>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card border-primary shadow-sm p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">สินค้าทั้งหมด</p>
                                <h3 class="fw-bold"><?php echo $count_products; ?></h3>
                            </div>
                            <i class="bi bi-box-seam fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card border-success shadow-sm p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">สมาชิกในระบบ</p>
                                <h3 class="fw-bold"><?php echo $count_users; ?></h3>
                            </div>
                            <i class="bi bi-people fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">📦 รายการสินค้าและจัดการ</h5>
                    <a href="add_product.php" class="btn btn-success btn-sm">+ เพิ่มสินค้าใหม่</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">รูปภาพ</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา</th>
                                    <th>รายละเอียด</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result_products)): ?>
                                <tr>
                                    <td class="ps-3">
                                        <img src="image/<?php echo $row['image']; ?>" class="rounded" width="50" height="50" style="object-fit: cover;">
                                    </td>
                                    <td class="fw-bold"><?php echo $row['name']; ?></td>
                                    <td class="text-primary">฿<?php echo number_format($row['price'], 2); ?></td>
                                    <td class="text-muted small"><?php echo mb_strimwidth($row['description'], 0, 50, "..."); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> แก้ไข
                                            </a>
                                            <a href="admin.index.php?delete=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ยืนยันการลบสินค้าชิ้นนี้?')">
                                                <i class="bi bi-trash"></i> ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>