<?php
session_start();
$conn = new mysqli("127.0.0.1", "root", "", "sqlinjection");
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// // 1. Cập nhật bằng URL GET (KHÔNG AN TOÀN - mục đích học SQLi)
if (isset($_GET['id'], $_GET['username'], $_GET['password'])) {
    $id = (int)$_GET['id']; // Ép kiểu số nguyên để giảm rủi ro
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Câu lệnh dễ bị SQL Injection, chỉ để học hỏi
    $sql = "UPDATE user SET TenUser = '$username', Pass = '$password' WHERE ID = $id";

    if ($conn->query($sql)) {
        echo "Đã cập nhật tài khoản ID $id qua URL.<br>";
    } else {
        echo "Lỗi cập nhật: " . $conn->error . "<br>";
    }
}

// Câp nhật an toàn
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['username'], $_GET['password'])) {
//     echo "Không được phép sửa tài khoản qua URL.";
//     exit();
// }






//FORM UPDATE
// 2. Cập nhật an toàn qua POST form với prepared statements
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo "Vui lòng nhập đầy đủ thông tin.<br>";
    } else {
        // Kiểm tra tên đăng nhập đã tồn tại với user khác chưa
        $stmt_check = $conn->prepare("SELECT ID FROM user WHERE TenUser = ? AND ID != ?");
        $stmt_check->bind_param("si", $username, $id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            echo "Tên đăng nhập đã tồn tại!<br>";
        } else {
            // Cập nhật user an toàn
            $stmt = $conn->prepare("UPDATE user SET TenUser = ?, Pass = ? WHERE ID = ?");
            $stmt->bind_param("ssi", $username, $password, $id);
            if ($stmt->execute()) {
                echo "Cập nhật thành công tài khoản ID $id!<br>";
            } else {
                echo "Lỗi cập nhật: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}

// 3. Hiển thị form sửa nếu có tham số `edit` trong URL
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM user WHERE ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        ?>
        <h2>Sửa tài khoản ID <?= $edit_id ?></h2>
        <form method="POST" action="update.php">
            <input type="hidden" name="edit_id" value="<?= $row['ID'] ?>">
            <label>Tên đăng nhập:</label><br>
            <input type="text" name="username" value="<?= htmlspecialchars($row['TenUser']) ?>" required><br><br>
            <label>Mật khẩu:</label><br>
            <input type="text" name="password" value="<?= htmlspecialchars($row['Pass']) ?>" required><br><br>
            <input type="submit" value="Cập nhật">
        </form>
        <br>
        <?php
    } else {
        echo "Không tìm thấy tài khoản để sửa.<br>";
    }
    $stmt->close();
}

// 4. Hiển thị danh sách tài khoản để chọn sửa
echo "<h3>Danh sách tài khoản</h3>";
$result = $conn->query("SELECT * FROM user ORDER BY ID ASC");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Tên đăng nhập</th><th>Mật khẩu</th><th>Hành động</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TenUser']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Pass']) . "</td>";
        echo "<td><a href='update.php?edit=" . urlencode($row['ID']) . "'>Sửa</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Không có tài khoản nào.";
}

$conn->close();
?>
