<?php
// ตัวอย่างรับค่าฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ยังไม่เช็ค DB แค่ทดสอบ
    echo "<script>alert('Email: $email');</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: url('assets/bg.jpg') no-repeat center center/cover;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    width: 900px;
    height: 450px;
    background: rgba(0,0,0,0.5);
    border-radius: 20px;
    display: flex;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

.left {
    width: 40%;
    background: linear-gradient(180deg, #b30000, #ff0000);
    color: white;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.left h1 {
    font-size: 40px;
    margin-bottom: 30px;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    border-radius: 8px;
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.login-btn {
    background: #ff3300;
    color: white;
}

.signup-btn {
    background: #1e90ff;
    color: white;
}

.right {
    width: 60%;
    background: url('assets/bg.jpg') no-repeat center center/cover;
}
</style>
</head>

<body>

<div class="container">
    
    <div class="left">
        <h1>NIKE</h1>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" class="login-btn">Log In</button>
            <button type="button" class="signup-btn">Sign Up</button>
        </form>
    </div>

    <div class="right">
        <!-- รูปรองเท้า -->
    </div>

</div>

</body>
</html>
