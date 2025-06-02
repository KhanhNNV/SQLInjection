<?php
$conn = new mysqli("127.0.0.1", "root", "", "sqlinjection");
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

/* 
===========================================
KHÔNG AN TOÀN: Thêm tài khoản qua URL GET
Dễ bị tấn công SQL Injection nếu triển khai thực tế.
Chỉ để học!
Ví dụ: add.php?username=test&password=123
===========================================
*/
if (isset($_GET['username'], $_GET['password'])) {
    $username = $_GET['username'];
    $password = $_GET['password'];

    $sql = "INSERT INTO user (TenUser, Pass) VALUES ('$username', '$password')";
    if ($conn->query($sql)) {
        echo "Đã thêm tài khoản `$username` qua URL.<br>";
    } else {
        echo "Lỗi thêm tài khoản: " . $conn->error . "<br>";
    }
}

/*
===========================================
PHIÊN BẢN AN TOÀN: Chặn thêm bằng URL GET
Chỉ cho phép thêm qua biểu mẫu POST
===========================================
*/
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'], $_GET['password'])) {
//     echo "Không được phép thêm tài khoản qua URL.";
//     exit();
// }








//FORM THÊM TÀI KHOẢN
// ============================================
// 2. ✅ Thêm bằng POST (form an toàn)
// ============================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        echo "Vui lòng nhập đầy đủ thông tin.<br>";
    } else {
        // Kiểm tra tên đăng nhập đã tồn tại chưa
        $stmt_check = $conn->prepare("SELECT ID FROM user WHERE TenUser = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            echo "Tên đăng nhập đã tồn tại.<br>";
        } else {
            $stmt = $conn->prepare("INSERT INTO user (TenUser, Pass) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                echo "Thêm tài khoản thành công!<br>";
            } else {
                echo "Lỗi thêm tài khoản: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}
?>

<!-- Form thêm tài khoản -->
<h2>Thêm tài khoản mới</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Tên đăng nhập" required><br><br>
    <input type="text" name="password" placeholder="Mật khẩu" required><br><br>
    <input type="submit" value="Thêm">
</form>

<!-- Hiển thị danh sách tài khoản -->
<h3>Danh sách tài khoản</h3>
<?php
$result = $conn->query("SELECT * FROM user");
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
    echo "Không có tài khoản nào.";
}

$conn->close();
?>
