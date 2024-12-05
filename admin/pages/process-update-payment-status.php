<?php
// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Kiểm tra nếu có thông tin gửi đến từ AJAX
if (isset($_POST['MaHoaDon']) && isset($_POST['TrangThaiThanhToan'])) {
    $maHoaDon = $_POST['MaHoaDon'];
    $trangThaiThanhToan = $_POST['TrangThaiThanhToan'];

    // Cập nhật trạng thái thanh toán trong cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE hoadon SET TrangThaiThanhToan = ? WHERE MaHoaDon = ?");
    $stmt->bind_param("si", $trangThaiThanhToan, $maHoaDon);

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
} else {
    echo 'error';
}

$conn->close();
?>
