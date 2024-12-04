<?php
// update_enableflag.php

// Kết nối đến cơ sở dữ liệu
include '../../database/db.php';


// Kiểm tra nếu có dữ liệu được gửi đến
if (isset($_POST['type']) &&isset($_POST['id']) && isset($_POST['enableflag'])) {
    $table = $conn->real_escape_string($_POST['type']);
    switch ($table) {
        case 'nhanvien':
            $column ='MaNV';
            break;
        case 'hanhkhach':
            $column = 'MaHK';
            break;
        case 'tuyenxe':
            $column = 'MaTuyenXe';
            break;
        case 'loaixe':
            $column = 'MaLoaiXe';
            break;
        case 'xe':
            $column = 'MaXe';
            break;
        case 'phanhoidanhgia':
            $column = 'MaPhanHoi';
            break;
        default:
            die('Error!');
    }
    $maNV = $_POST['id'];
    $enableflag = $_POST['enableflag'];
    $sql = "UPDATE {$table} SET enableflag = ? WHERE {$column} = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $enableflag, $maNV);
    if ($stmt->execute()) {
        echo "Cập nhật thành công";
    } else {
        echo "Lỗi cập nhật";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Dữ liệu không hợp lệ";
}
?>
