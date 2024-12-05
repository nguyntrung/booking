<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';

session_start(); 
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../views/login.php");
    exit();
}

// Truy vấn thông tin hành khách
$stmt = $conn->prepare("SELECT MaHK, TenHK, SDT, Email, NamSinh, GioiTinh, CCCD FROM hanhkhach WHERE email = ?");
$stmt->bind_param("s", $_SESSION['userEmail']);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra dữ liệu
if ($result->num_rows > 0) {
    $hanhKhach = $result->fetch_assoc();
} else {
    echo "Không tìm thấy hành khách.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Thông tin tài khoản</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
      <style>
         body {
            background-color: #f8f9fa;
         }
         .containers {
            max-width: 1000px;
            margin: auto;
         }

         .sidebar {
            background-color: #ffffff;
            height: 356px;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }
         .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            margin-bottom: 10px;
            border-radius: 5px;
         }
         .sidebar a.active, .sidebar a:hover {
            background-color: #f05454;
            color: #fff;
         }
         .sidebar a i {
            margin-right: 10px;
         }
         .profile-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }
         .table {
            border: white;
         }
         .btn-update {
            background-color: #f05454;
            color: #fff;
            border: none;
            padding: 8px 30px;
            border-radius: 25px;
         }
         .btn-update:hover {
            background-color: #d94242;
            color: #fff;
         }
      </style>
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <div class="containers mt-5">
         <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
               <a href="profile.php" class="active"><i class="fas fa-user"></i> Thông tin tài khoản</a>
               <a href="my-bookings.php"><i class="fas fa-history"></i> Lịch sử mua vé</a>
               <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
            <!-- Profile Info -->
            <div class="col-md-9 mb-3">
               <div class="profile-container">
                  <h5 class="text-center">THÔNG TIN TÀI KHOẢN</h5>
                  <table class="table">
                     <tr><th>Họ và tên:</th><td><?php echo htmlspecialchars($hanhKhach['TenHK']); ?></td></tr>
                     <tr><th>Số điện thoại:</th><td><?php echo htmlspecialchars($hanhKhach['SDT']); ?></td></tr>
                     <tr><th>Email:</th><td><?php echo htmlspecialchars($hanhKhach['Email']); ?></td></tr>
                     <tr><th>Năm sinh:</th><td><?php echo htmlspecialchars($hanhKhach['NamSinh']); ?></td></tr>
                     <tr><th>Giới tính:</th><td><?php echo htmlspecialchars($hanhKhach['GioiTinh']); ?></td></tr>
                     <tr><th>Căn cước:</th><td><?php echo htmlspecialchars($hanhKhach['CCCD']); ?></td></tr>
                  </table>
                  <div class="text-center">
                     <a href="edit_profile.php?MaHK=<?php echo $hanhKhach['MaHK']; ?>" class="btn btn-update">Chỉnh sửa</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </body>
</html>
