<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Đăng nhập thành công</title>
<style>
body {
    background: #f0f8ff;
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
}

.container {
    margin: 100px auto;
    padding: 30px;
    background: #d4edda;
    color: #155724;
    border: 2px solid #c3e6cb;
    border-radius: 8px;
    width: 350px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.container h2 {
    margin-bottom: 10px;
    color: #155724;
}

.container p {
    margin: 5px 0;
}

a.logout {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    background: #28a745;
    color: #fff;
    padding: 10px 20px;
    border-radius: 4px;
    transition: 0.3s;
}
input[type="submit"]  {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    background: #28a745;
    color: #fff;
    padding: 10px 20px;
    border-radius: 4px;
    transition: 0.3s;
}

a.logout:hover {
    background: #218838;
}
</style>
</head>
<body>
<div class="container">
    <h2>Đăng nhập thành công</h2>

    <a class="logout" href="login.php">Đăng xuất</a>

    <br><br>
    <a class="logout" href="delete.php?id=<?= $_SESSION['user_id'] ?>">Xóa tài khoản</a>

    <br><br>
    <a class="logout" href="view.php?id=<?= $_SESSION['user_id'] ?>">Xem thông tin</a>

    <br><br>
    <a class="logout" href="add.php?id=<?= $_SESSION['user_id'] ?>">Thêm tài khoản</a>

    <br><br>
    <a class="logout" href="update.php?id=<?= $_SESSION['user_id'] ?>">Sửa tài khoản</a>
</div>

</body>
</html>
