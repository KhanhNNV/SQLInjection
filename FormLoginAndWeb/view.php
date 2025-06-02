<?php
// Tắt toàn bộ lỗi hiển thị ra màn hình
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(0);

// Tắt cơ chế tự động ném exception của MySQLi (bỏ stack trace)
// mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli("localhost", "root", "", "sqlinjection");
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}
// ✅ Gợi ý: Kiểm tra session để chỉ user đã đăng nhập mới xem được dữ liệu
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


/*
================== CÁCH 1: KHÔNG AN TOÀN - DỄ BỊ TẤN CÔNG BẰNG SQL INJECTION ====================
*/

// Không kiểm tra session, không lọc đầu vào => DỄ bị SQL Injection
$id = $_GET['id'] ?? '';

// Câu truy vấn trực tiếp – DỄ bị tấn công SQLi nếu truyền vào: id=1 OR 1=1
$sql = "SELECT * FROM user WHERE ID = $id";

// Thực thi câu truy vấn nguy hiểm
if (mysqli_multi_query($conn, $sql)) {
    $result = mysqli_store_result($conn);
} else {
    echo "<p style='color: red;'>Lỗi SQL: " . mysqli_error($conn) . "</p>";
}



/*
================== CÁCH 2: AN TOÀN - CHỐNG SQL INJECTION ====================
Sử dụng prepared statement để tránh chèn mã SQL độc hại vào câu truy vấn
Giúp bảo vệ dữ liệu khi người dùng cố gắng truyền vào chuỗi độc hại
=============================================================================
*/

// $id = $_GET['id'] ?? '';
// $id = (int)$id; // ép kiểu an toàn (tùy chọn thêm)

// $stmt = $conn->prepare("SELECT * FROM user WHERE ID = ?");
// $stmt->bind_param("i", $id);  // 'i' = integer
// $stmt->execute();
// $result = $stmt->get_result();

//HTML
echo "<h2>Kết quả truy vấn</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Tên đăng nhập</th><th>Mật khẩu</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['ID'] . "</td>";
        echo "<td>" . $row['TenUser'] . "</td>";
        echo "<td>" . $row['Pass'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Không tìm thấy kết quả.";
}


$conn->close();
?>
