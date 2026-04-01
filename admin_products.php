<?php
session_start();
include('db.php');

// ตรวจสอบว่าเป็น Admin หรือไม่ (ป้องกันคนทั่วไปแอบเข้า)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    // หมายเหตุ: ใน login.php คุณต้องเก็บ $_SESSION['role'] = $row['role']; ไว้ด้วยนะครับ
    echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    exit();
}

// จัดการการลบสินค้า
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM products WHERE id = $id";
    mysqli_query($conn, $sql);
    header("Location: admin_products.php");
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin - จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin.index.php">Admin Dashboard</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">ไปหน้าร้านค้า</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>จัดการรายการรองเท้า</h2>
            <a href="add_product.php" class="btn btn-success">+ เพิ่มสินค้าใหม่</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>รูป</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><img src="image/<?php echo $row['image']; ?>" width="60"></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>฿<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                <a href="admin_products.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>