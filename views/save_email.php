<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';

// Kiểm tra xem có email được gửi từ yêu cầu POST không
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Kiểm tra xem email đã tồn tại trong bảng hanhkhach chưa
    $sql = "SELECT * FROM hanhkhach WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Nếu email chưa tồn tại, thêm vào bảng hanhkhach
        $sql_insert = "INSERT INTO hanhkhach (email, enableflag) VALUES (?, 0)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $email);

        if ($stmt_insert->execute()) {
            echo "Email đã được lưu vào cơ sở dữ liệu!";
        } else {
            echo "Lỗi khi lưu email: " . $conn->error;
        }
    } else {
        echo "Email đã tồn tại trong cơ sở dữ liệu.";
    }

    // Đóng kết nối
    $stmt->close();
    $stmt_insert->close();
    $conn->close();
} else {
    echo "Email không được gửi!";
}
?>
