<?php
session_start();

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Khởi tạo biến cho các trường
$maHK = '';
$tenHK = '';
$sdt = '';
$email = '';
$matKhau = '';
$namSinh = '';
$gioiTinh = '';
$cccd = '';
$errorMessage = '';
$successMessage = '';

// Kiểm tra xem có mã hành khách để chỉnh sửa không
if (isset($_GET['id'])) {
    $maHK = $_GET['id'];

    // Lấy thông tin hành khách từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT MaHK, TenHK, SDT, Email, MatKhau, NamSinh, GioiTinh, CCCD FROM hanhkhach WHERE MaHK = ?");
    $stmt->bind_param("i", $maHK);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $hanhkhach = $result->fetch_assoc();
        $tenHK = $hanhkhach['TenHK'];
        $sdt = $hanhkhach['SDT'];
        $email = $hanhkhach['Email'];
        $matKhau = $hanhkhach['MatKhau'];
        $namSinh = $hanhkhach['NamSinh'];
        $gioiTinh = $hanhkhach['GioiTinh'];
        $cccd = $hanhkhach['CCCD'];
    } else {
        $errorMessage = 'Không tìm thấy hành khách!';
    }
}

// Xử lý thêm hoặc cập nhật hành khách
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $tenHK = trim($_POST['tenHK']);
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);
    $matKhau = trim($_POST['matKhau']);
    $namSinh = trim($_POST['namSinh']);
    $gioiTinh = trim($_POST['gioiTinh']);
    $cccd = trim($_POST['cccd']);

    // Kiểm tra các trường
    if (empty($tenHK) || empty($sdt) || empty($email) || empty($matKhau)) {
        $errorMessage = 'Vui lòng nhập đủ thông tin cho hành khách.';
    } else {
        if ($maHK) {
            // Cập nhật hành khách
            $stmt = $conn->prepare("UPDATE hanhkhach SET TenHK = ?, SDT = ?, Email = ?, MatKhau = ?, NamSinh = ?, GioiTinh = ?, CCCD = ? WHERE MaHK = ?");
            $stmt->bind_param("ssssisssi", $tenHK, $sdt, $email, $matKhau, $namSinh, $gioiTinh, $cccd, $maHK);

            if ($stmt->execute()) {
                $successMessage = 'Cập nhật thông tin hành khách thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật hành khách.';
            }
        } else {
            // Thêm hành khách mới
            $stmt = $conn->prepare("INSERT INTO hanhkhach (TenHK, SDT, Email, MatKhau, NamSinh, GioiTinh, CCCD) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiss", $tenHK, $sdt, $email, $matKhau, $namSinh, $gioiTinh, $cccd);

            if ($stmt->execute()) {
                $successMessage = 'Thêm hành khách thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm hành khách.';
            }
        }
    }
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý hành khách</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'sidebar.php'; ?>
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <h5 class="card-header"><?php echo $maHK ? 'Chỉnh sửa hành khách' : 'Thêm hành khách'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="tenHK" class="form-label">Tên hành khách</label>
                                        <input type="text" class="form-control" id="tenHK" name="tenHK"
                                            value="<?php echo htmlspecialchars($tenHK); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sdt" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" id="sdt" name="sdt"
                                            value="<?php echo htmlspecialchars($sdt); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="matKhau" class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" id="matKhau" name="matKhau"
                                            value="<?php echo htmlspecialchars($matKhau); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namSinh" class="form-label">Năm sinh</label>
                                        <input type="number" class="form-control" id="namSinh" name="namSinh"
                                            value="<?php echo htmlspecialchars($namSinh); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="gioiTinh" class="form-label">Giới tính</label>
                                        <select class="form-control" id="gioiTinh" name="gioiTinh">
                                            <option value="Nam" <?php echo ($gioiTinh == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                            <option value="Nữ" <?php echo ($gioiTinh == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                            <option value="Khác" <?php echo ($gioiTinh == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cccd" class="form-label">CCCD</label>
                                        <input type="text" class="form-control" id="cccd" name="cccd"
                                            value="<?php echo htmlspecialchars($cccd); ?>">
                                    </div>
                                    <button type="submit"
                                        class="btn btn-primary"><?php echo $maHK ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="hanhkhach_manager.php" class="btn btn-secondary">Quay lại</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>
