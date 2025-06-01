<?php
session_start();

// Kết nối database
$conn = mysqli_connect('localhost', 'root', '', 'sqlinjection');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Kiểm tra phiên đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// CSS đơn giản cho giao diện
echo '<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
        color: #333;
    }
    p {
        background-color: #e7f3fe;
        border: 1px solid #8ac7ff;
        padding: 15px;
        border-radius: 6px;
        max-width: 600px;
        margin-bottom: 15px;
        font-size: 18px;
    }
    a {
        display: inline-block;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        padding: 10px 20px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }
    a:hover {
        background-color: #0056b3;
    }
</style>';

// Nếu có tham số id truyền vào
if (isset($_GET['id'])) {
    $id = $_GET['id'];  // ❌ Không kiểm tra, không xử lý dữ liệu => DỄ BỊ SQL Injection

    // ❌ Câu truy vấn SQL dễ bị tấn công khi truyền tham số qua URL
    // VD: ?id=0 OR 1=1 sẽ thành câu SQL:
    // DELETE FROM user WHERE ID = 0 OR 1=1
    // => xóa toàn bộ bảng user do '1=1' luôn đúng
    $sql = "DELETE FROM user WHERE ID = $id";

    if (mysqli_query($conn, $sql)) {
        if ($id == $_SESSION['user_id']) {
            session_destroy();
            echo "<p>Tài khoản của bạn đã bị xóa. Bạn đã đăng xuất.</p>";
        } else {
            echo "<p>Tài khoản khác đã bị xóa.</p>";
        }
        echo '<a href="login.php">Quay lại</a>';
    } else {
        echo "<p style='color:red;'>Lỗi khi xóa tài khoản: " . mysqli_error($conn) . "</p>";
    }

} else {
    echo "<p>Không có dữ liệu để xóa.</p>";
}

// === Giải pháp an toàn chống SQL Injection: ===
// Thay vì chèn trực tiếp biến $id vào câu SQL,
// nên dùng Prepared Statement với bind_param kiểu số nguyên:
//
// $stmt = $conn->prepare("DELETE FROM user WHERE ID = ?");
// $stmt->bind_param("i", $id);
// $stmt->execute();
//
// Nếu thành công thì:
// if ($stmt->affected_rows > 0) {
//     // xử lý sau xóa thành công
// }
//
// Chuẩn này giúp:
// - $id được xử lý an toàn, không thể chèn thêm câu lệnh SQL
// - Bảo vệ ứng dụng khỏi các cuộc tấn công SQL Injection
//

mysqli_close($conn);
?>
