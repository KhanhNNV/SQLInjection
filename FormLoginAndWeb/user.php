<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "sqlinjection");
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM user");

echo "<h2>Danh sách người dùng</h2>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Tên đăng nhập</th><th>Mật khẩu</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TenUser']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Pass']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Không có người dùng nào trong cơ sở dữ liệu.";
}
?>
