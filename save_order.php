<?php
session_start();
include('db.php');

if (!isset($_SESSION['username']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// ดึงข้อมูลผู้ใช้งาน
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
$u = mysqli_fetch_assoc($user_query);

$firstname = mysqli_real_escape_string($conn, $u['firstname']);
$lastname = mysqli_real_escape_string($conn, $u['lastname']);
$address = mysqli_real_escape_string($conn, $u['address']);
$phone = mysqli_real_escape_string($conn, $u['phone']);

$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += ($item['price'] * $item['quantity']);
}

// บันทึกลงตาราง orders
$sql_order = "INSERT INTO orders (username, firstname, lastname, total_price, address, phone, status, created_at) 
              VALUES ('$username', '$firstname', '$lastname', '$total_price', '$address', '$phone', 'pending', NOW())";

if (mysqli_query($conn, $sql_order)) {
    $order_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $item) {
        $p_id = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        // ดึงค่า size และ color มาบันทึก
        $size = mysqli_real_escape_string($conn, $item['size']);
        $color = mysqli_real_escape_string($conn, $item['color']);

        $sql_detail = "INSERT INTO order_details (order_id, product_id, size, color, qty, price_at_purchase) 
                       VALUES ('$order_id', '$p_id', '$size', '$color', '$qty', '$price')";
        mysqli_query($conn, $sql_detail);
    }

    unset($_SESSION['cart']);
    header("Location: index.php?order=success");
} else {
    die("Error: " . mysqli_error($conn)); 
}
?>