<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../database/db.php';


// Xử lý thêm hoặc cập nhật tuyến xe
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if(isset($_GET["id"]) && isset($_GET["macho"])){
        $id = $_GET["id"];
        $macho = $_GET["macho"];
        $stmt = $conn->prepare("update ve set MaCho = ? where MaVeXe = ?");
        $stmt->bind_param("si", $macho, $id);

        if ($stmt->execute()) {
            header("Location: my-bookings.php?kq=1");
            exit;
        } else {
            header("Location: my-bookings.php?kq=2");
            exit;
        }
    } else {
        header("Location: my-bookings.php?kq=3");
            exit;
    }
            
        
    
}else {
    header("Location: my-bookings.php?kq=4");
        exit;
}

?>
