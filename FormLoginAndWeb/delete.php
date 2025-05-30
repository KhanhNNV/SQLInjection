<?php
$conn = mysqli_connect('localhost', 'root', '', 'sqlinjection');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$id = $_GET['ID']; // không an toàn
$sql = "DELETE FROM user WHERE ID = $id";
mysqli_query($conn, $sql);

echo "Xóa thành công (nếu có dữ liệu phù hợp).";
?>