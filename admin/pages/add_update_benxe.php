<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNguoiDung'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Khởi tạo biến cho các trường
$maBenXe = '';
$tenBenXe = '';
$diaChi = '';
$errorMessage = '';
$successMessage = '';

// Kiểm tra xem có mã bến để chỉnh sửa không
if (isset($_GET['id'])) {
    $maBenXe = $_GET['id'];

    // Lấy thông tin bến xe từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT MaBenXe, TenBenXe, DiaChi FROM benxe WHERE MaBenXe = ?");
    $stmt->bind_param("i", $maBenXe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $benXe = $result->fetch_assoc();
        $tenBenXe = $benXe['TenBenXe'];
        $diaChi = $benXe['DiaChi'];
    } else {
        $errorMessage = 'Không tìm thấy bến xe!';
    }
}

// Xử lý thêm hoặc cập nhật bến xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenBenXe = trim($_POST['tenBenXe']);
    $diaChi = trim($_POST['diaChi']);

    if (empty($tenBenXe)) {
        $errorMessage = 'Vui lòng nhập tên bến xe.';
    } else {
        if ($maBenXe) {
            // Cập nhật bến xe
            $stmt = $conn->prepare("UPDATE benxe SET TenBenXe = ?, DiaChi = ? WHERE MaBenXe = ?");
            $stmt->bind_param("ssi", $tenBenXe, $diaChi, $maBenXe);

            if ($stmt->execute()) {
                $successMessage = 'Cập nhật bến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật bến xe.';
            }
        } else {
            // Thêm bến xe mới
            $stmt = $conn->prepare("INSERT INTO benxe (TenBenXe, DiaChi) VALUES (?, ?)");
            $stmt->bind_param("ss", $tenBenXe, $diaChi);

            if ($stmt->execute()) {
                $successMessage = 'Thêm bến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm bến xe.';
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
    <title>Quản lý bến xe</title>
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
                            <h5 class="card-header"><?php echo $maBenXe ? 'Chỉnh sửa bến xe' : 'Thêm bến xe'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="tenBenXe" class="form-label">Tên bến xe</label>
                                        <input type="text" class="form-control" id="tenBenXe" name="tenBenXe"
                                            value="<?php echo htmlspecialchars($tenBenXe); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="diaChi" class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" id="diaChi" name="diaChi"
                                            value="<?php echo htmlspecialchars($diaChi); ?>" required>
                                    </div>
                                    <button type="submit"
                                        class="btn btn-primary"><?php echo $maBenXe ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="benxe_manager.php" class="btn btn-secondary">Quay lại</a>
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