<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
    $colors = mysqli_real_escape_string($conn, $_POST['colors']);
    
    $filename = $_FILES["image"]["name"];
    $tempname = $_FILES["image"]["tmp_name"];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $new_filename = time() . "." . $ext;
    $folder = "image/" . $new_filename;

    $sql = "INSERT INTO products (name, brand, price, description, sizes, colors, image) 
            VALUES ('$name', '$brand', '$price', '$description', '$sizes', '$colors', '$new_filename')";
    
    if (mysqli_query($conn, $sql)) {
        if (!is_dir('image')) { mkdir('image'); }
        if (move_uploaded_file($tempname, $folder)) {
            echo "<script>alert('เพิ่มสินค้าสำเร็จ'); window.location='admin.index.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า - SHOE STORE ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .admin-navbar { background-color: #1a1a1a; border-bottom: 3px solid #4361ee; }
        .card-custom { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-save { background-color: #4361ee; color: white; border-radius: 12px; font-weight: bold; border: none; }
        .btn-save:hover { background-color: #3f37c9; color: white; transform: translateY(-2px); }
        .form-label { font-weight: 600; color: #555; }
    </style>
</head>
<body>

<nav class="navbar admin-navbar py-3 mb-5">
    <div class="container">
        <span class="navbar-brand text-white fw-bold">
            <i class="bi bi-plus-circle-dotted me-2 text-primary"></i> SHOE STORE MANAGEMENT
        </span>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card card-custom p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold mt-2">เพิ่มรายการสินค้าใหม่</h3>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อรุ่นรองเท้า</label>
                            <input type="text" name="name" class="form-control" placeholder="เช่น Air Max 270" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">แบรนด์</label>
                            <input type="text" name="brand" class="form-control" placeholder="Nike / Adidas" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ไซส์ที่มี (คั่นด้วย ,)</label>
                            <input type="text" name="sizes" class="form-control" placeholder="38,39,40,41" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">สีที่มี (คั่นด้วย ,)</label>
                            <input type="text" name="colors" class="form-control" placeholder="Black,White" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ราคาขาย (฿)</label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียดสินค้า</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">รูปภาพสินค้า</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="submit" class="btn btn-save py-3 shadow-sm">
                            <i class="bi bi-cloud-arrow-up me-2"></i> บันทึกรายการ
                        </button>
                        <a href="admin.index.php" class="btn btn-light py-2 text-muted fw-bold">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>