<?php
include '../database/db.php';
session_start(); 

if (!isset($_SESSION['userEmail'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

$userEmail = $_SESSION['userEmail'];

if (isset($_POST['rating']) && isset($_POST['feedback'])) {
    $rating = (int) $_POST['rating'];
    $feedback = $_POST['feedback']; 

    $stmt = $conn->prepare("SELECT MaHK FROM hanhkhach WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($MaHK);
    $stmt->fetch();
    $stmt->close();

    if (!$MaHK) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy hành khách với email này.']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO phanhoidanhgia (NoiDung, DanhGia, HanhKhach, enableflag) VALUES (?, ?, ?, ?)");
    $enableFlag = 1; 
    $stmt->bind_param("siii", $feedback, $rating, $MaHK, $enableFlag);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cảm ơn bạn đã gửi phản hồi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi gửi phản hồi.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
}

$conn->close(); 
?>
