<?php 
session_start();
// kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sqlinjection";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $taikhoan = $_POST['username'];
    $password = $_POST['password'];

    // Thực hiện truy vấn không an toàn - dễ bị SQL INJECTION
    $sql = "SELECT * FROM user WHERE TenUser = '$taikhoan' AND Pass = '$password'";
 
    // Dùng multi_query để chạy nhiều truy vấn - dễ thêm xóa sửa dữ liệu
    if (mysqli_multi_query($conn, $sql)) {
        if ($result = mysqli_store_result($conn)) {
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['TenUser'];
                $_SESSION['password'] = $user['Pass'];
                mysqli_free_result($result); // giải phóng bộ nhớ
                header("Location: result.php");
                exit();
            } else {
                mysqli_free_result($result);
                $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu!";
                header("Location: login.php");
                exit();
            }
        }
    } else {
        echo "<p style='color: red;'>Lỗi SQL: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link src="jquery-3.7.1.min.js">
    <style>
    body {
        background-color: #f1f1f1;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 400px;
        margin: 100px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #35424a;
    }

    label {
        display: block;
        margin: 10px 0 5px;
    }

    input[type="text"],
    input[type="password"] {
        width: 95%;
        padding: 10px;
        margin: 5px 0 15px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    input[type="submit"] {
        background-color: #33bbff;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #0099e6;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
    }

    .footer a {
        color: #007BFF;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Đăng Nhập</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div style='color: red;'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']); // Xóa thông báo sau khi hiển thị
        }
        ?>
        <form action="login.php" autocomplete="off" method="POST">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>

            <label for="password">Mật khẩu:</label>
            <input type="text" id="password" name="password" placeholder="Nhập mật khẩu" required>

            <input type="submit" id="login" name="login" value="Đăng Nhập">
        </form>
    </div>
</body>

</html>