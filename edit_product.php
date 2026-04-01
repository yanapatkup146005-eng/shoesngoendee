<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    $product = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
    $colors = mysqli_real_escape_string($conn, $_POST['colors']);
    
    if ($_FILES['image']['name'] != "") {
        $new_filename = time() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["image"]["tmp_name"], "image/" . $new_filename);
        $sql = "UPDATE products SET name='$name', brand='$brand', price='$price', description='$description', sizes='$sizes', colors='$colors', image='$new_filename' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', brand='$brand', price='$price', description='$description', sizes='$sizes', colors='$colors' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location='admin.index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า - SHOE STORE ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .admin-navbar { background-color: #1a1a1a; border-bottom: 3px solid #4361ee; }
        .card-custom { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .current-img { border: 2px solid #4361ee; padding: 5px; border-radius: 10px; }
        .btn-update { background-color: #4361ee; color: white; border-radius: 12px; font-weight: bold; border: none; }
        .btn-update:hover { background-color: #3f37c9; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<nav class="navbar admin-navbar py-3 mb-5">
    <div class="container">
        <span class="navbar-brand text-white fw-bold">
            <i class="bi bi-pencil-square me-2 text-primary"></i> EDIT PRODUCT DETAILS
        </span>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card card-custom p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ชื่อรุ่นรองเท้า</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">แบรนด์</label>
                            <input type="text" name="brand" class="form-control" value="<?php echo $product['brand']; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ไซส์ที่มี</label>
                            <input type="text" name="sizes" class="form-control" value="<?php echo $product['sizes']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">สีที่มี</label>
                            <input type="text" name="colors" class="form-control" value="<?php echo $product['colors']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ราคาขาย (฿)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block fw-bold">รูปภาพปัจจุบัน</label>
                        <img src="image/<?php echo $product['image']; ?>" width="150" class="current-img mb-3 shadow-sm">
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="update" class="btn btn-update py-3 shadow-sm">
                            <i class="bi bi-save me-2"></i> บันทึกการแก้ไข
                        </button>
                        <a href="admin.index.php" class="btn btn-light border text-muted fw-bold">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>