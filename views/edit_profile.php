<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';

// Kiểm tra và lấy MaHK từ URL
if (!isset($_GET['MaHK'])) {
    echo "Mã hành khách không hợp lệ.";
    exit;
}
$maHK = intval($_GET['MaHK']);

// Truy vấn thông tin hành khách
$stmt = $conn->prepare("SELECT MaHK, TenHK, SDT, Email, NamSinh, GioiTinh, CCCD FROM hanhkhach WHERE MaHK = ?");
$stmt->bind_param("i", $maHK);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $hanhKhach = $result->fetch_assoc();
} else {
    echo "Không tìm thấy hành khách.";
    exit;
}

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenHK = $_POST['TenHK'];
    $sdt = $_POST['SDT'];
    $email = $_POST['Email'];
    $namSinh = $_POST['NamSinh'];
    $gioiTinh = $_POST['GioiTinh'];
    $cccd = $_POST['CCCD'];

    // Kiểm tra dữ liệu hợp lệ
    if (strlen($sdt) != 10 || !ctype_digit($sdt)) {
        $error = "Số điện thoại phải có 10 chữ số.";
    } elseif (strlen($cccd) != 12 || !ctype_digit($cccd)) {
        $error = "CCCD phải có 12 chữ số.";
    } else {
        // Cập nhật thông tin
        $stmt = $conn->prepare("
            UPDATE hanhkhach 
            SET TenHK = ?, SDT = ?, Email = ?, NamSinh = ?, GioiTinh = ?, CCCD = ?
            WHERE MaHK = ?
        ");
        $stmt->bind_param("ssssssi", $tenHK, $sdt, $email, $namSinh, $gioiTinh, $cccd, $maHK);

        if ($stmt->execute()) {
            // Chuyển hướng với tham số success
            header("Location: profile.php?success=1");
            exit;
        } else {
            $error = "Lỗi khi cập nhật: " . $stmt->error;
        }
    }
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
        <h2 class="text-center">Chỉnh sửa thông tin</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="#" method="POST">
            <div class="mb-3">
                <label for="TenHK" class="form-label">Tên hành khách:</label>
                <input type="text" class="form-control" name="TenHK" value="<?php echo htmlspecialchars($hanhKhach['TenHK']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="SDT" class="form-label">Số điện thoại:</label>
                <input type="text" class="form-control" name="SDT" value="<?php echo htmlspecialchars($hanhKhach['SDT']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="Email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="Email" value="<?php echo htmlspecialchars($hanhKhach['Email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="NamSinh" class="form-label">Năm sinh:</label>
                <input type="text" class="form-control" name="NamSinh" value="<?php echo htmlspecialchars($hanhKhach['NamSinh']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="GioiTinh" class="form-label">Giới tính:</label>
                <select class="form-select" name="GioiTinh">
                    <option value="Nam" <?php echo ($hanhKhach['GioiTinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo ($hanhKhach['GioiTinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo ($hanhKhach['GioiTinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="CCCD" class="form-label">CCCD:</label>
                <input type="text" class="form-control" name="CCCD" value="<?php echo htmlspecialchars($hanhKhach['CCCD']); ?>" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
   </body>
</html>
