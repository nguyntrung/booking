<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';


// Xử lý thêm hoặc cập nhật tuyến xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["id"]) && isset($_POST["GiaTien"])){
        $id = $_POST["id"];
        $GiaTien = $_POST["GiaTien"];
        $stmt = $conn->prepare("update chuyenxe set GiaTien = ? where MaChuyenXe = ?");
        $stmt->bind_param("ii", $GiaTien, $id);

        if ($stmt->execute()) {
            header("Location: lichtrinh_update1.php?id=$id&kq=1");
            exit;
        } else {
            header("Location: lichtrinh_update1.php?id=$id&kq=2");
            exit;
        }
    } else if(isset($_POST["id"]) && isset($_POST["ThoiGianKhoiHanh"]) && isset($_POST["ThoiGianKetThuc"])){
        $id = $_POST["id"];
        $ThoiGianKhoiHanh = $_POST["ThoiGianKhoiHanh"];
        $ThoiGianKetThuc = $_POST["ThoiGianKetThuc"];
        $stmt = $conn->prepare("update chuyenxe set ThoiGianKhoiHanh = ? , ThoiGianKetThuc = ? where MaChuyenXe = ?");
        $stmt->bind_param("ssi", $ThoiGianKhoiHanh, $ThoiGianKetThuc, $id);

        if ($stmt->execute()) {
            header("Location: lichtrinh_update1.php?id=$id&kq=3");
            exit;
        } else {
            header("Location: lichtrinh_update1.php?id=$id&kq=4");
            exit;
        }
    } else if(isset($_POST["id"])  && isset($_POST["Xe"])){
        $id = $_POST["id"];
        $Xe = $_POST["Xe"];
        $stmt = $conn->prepare("update chuyenxe set Xe = ? where MaChuyenXe = ?");
        $stmt->bind_param("ii", $Xe, $id);

        if ($stmt->execute()) {
            header("Location: lichtrinh_update1.php?id=$id&kq=5");
            exit;
        } else {
            header("Location: lichtrinh_update1.php?id=$id&kq=6");
            exit;
        }
    } else if(isset($_POST["id"])  && isset($_POST["TaiXe"])){
        $id = $_POST["id"];
        $TaiXe = $_POST["TaiXe"];
        $stmt = $conn->prepare("update chuyenxe set TaiXe = ? where MaChuyenXe = ?");
        $stmt->bind_param("ii", $TaiXe, $id);

        if ($stmt->execute()) {
            header("Location: lichtrinh_update1.php?id=$id&kq=7");
            exit;
        } else {
            header("Location: lichtrinh_update1.php?id=$id&kq=8");
            exit;
        }
    }


            
        
    
}

?>
