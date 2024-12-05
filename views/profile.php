<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';

// if (!isset($_SESSION['MaHK'])) {
//     header("Location: ../views/login.php");
//     exit();
// }

// Truy vấn thông tin hành khách
$stmt = $conn->prepare("SELECT MaHK, TenHK, SDT, Email, NamSinh, GioiTinh, CCCD FROM hanhkhach WHERE MaHK = ?");
$maHK = 1; // Lấy mã hành khách (có thể thay đổi thành tham số GET nếu cần)
$stmt->bind_param("i", $maHK);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra dữ liệu
if ($result->num_rows > 0) {
    $hanhKhach = $result->fetch_assoc();
} else {
    echo "Không tìm thấy hành khách.";
    exit;
}
if (isset($_GET['success'])) {
    echo "<script>alert('Chỉnh sửa thành công!');</script>";
}
?>
<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>FUTA Bus Lines - Chất lượng là danh dự</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <!-- Main Contact -->
       <!-- Main Contact -->
       <div class="container mt-5">
        <h2 class="text-center">Thông tin hành khách</h2>
        <table class="table table-bordered mt-4">
            <tr><th>Tên</th><td><?php echo htmlspecialchars($hanhKhach['TenHK']); ?></td></tr>
            <tr><th>Số điện thoại</th><td><?php echo htmlspecialchars($hanhKhach['SDT']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($hanhKhach['Email']); ?></td></tr>
            <tr><th>Năm sinh</th><td><?php echo htmlspecialchars($hanhKhach['NamSinh']); ?></td></tr>
            <tr><th>Giới tính</th><td><?php echo htmlspecialchars($hanhKhach['GioiTinh']); ?></td></tr>
            <tr><th>CCCD</th><td><?php echo htmlspecialchars($hanhKhach['CCCD']); ?></td></tr>
        </table>
        <div class="text-center mt-3">
            <a href="edit_profile.php?MaHK=<?php echo $hanhKhach['MaHK']; ?>" class="btn btn-primary">Chỉnh sửa</a>
        </div>
    </div>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
   </body>
</html>
