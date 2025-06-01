<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "sqlinjection");
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

// ❌ Không kiểm tra session, không lọc đầu vào => DỄ bị SQL Injection
$id = $_GET['id'] ?? '';

// ❌ Câu truy vấn trực tiếp – DỄ bị tấn công SQLi nếu truyền vào: id=1 OR 1=1
$sql = "SELECT * FROM user WHERE ID = $id";

// ✅ Cách PHÒNG CHỐNG SQLi (sửa lỗi):
/*
$stmt = $conn->prepare("SELECT * FROM user WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
*/

// ❌ Thực thi câu truy vấn nguy hiểm
$result = $conn->query($sql);

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
