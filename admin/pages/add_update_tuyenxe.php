<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Khởi tạo biến cho các trường
$maTuyenXe = '';
$tenTuyenXe = '';
$khoangCach = '';
$benDi = '';
$benDen = '';
$enableflag = 0;
$errorMessage = '';
$successMessage = '';

// Lấy danh sách bến xe từ cơ sở dữ liệu (nếu cần)
$sqlBenXe = "SELECT MaBenXe, TenBenXe FROM benxe";
$resultBenXe = $conn->query($sqlBenXe);
$benXeList = [];
if ($resultBenXe && $resultBenXe->num_rows > 0) {
    while ($row = $resultBenXe->fetch_assoc()) {
        $benXeList[] = $row;
    }
}

// Kiểm tra xem có mã tuyến xe để chỉnh sửa không
if (isset($_GET['id'])) {
    $maTuyenXe = $_GET['id'];

    // Lấy thông tin tuyến xe từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT MaTuyenXe, TenTuyenXe, KhoangCach, BenDi, BenDen, enableflag FROM tuyenxe WHERE MaTuyenXe = ?");
    $stmt->bind_param("i", $maTuyenXe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $tuyenXe = $result->fetch_assoc();
        $tenTuyenXe = $tuyenXe['TenTuyenXe'];
        $khoangCach = $tuyenXe['KhoangCach'];
        $benDi = $tuyenXe['BenDi'];
        $benDen = $tuyenXe['BenDen'];
        $enableflag = $tuyenXe['enableflag'];
    } else {
        $errorMessage = 'Không tìm thấy tuyến xe!';
    }
}

// Xử lý thêm hoặc cập nhật tuyến xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenTuyenXe = trim($_POST['tenTuyenXe']);
    $khoangCach = trim($_POST['khoangCach']);
    $benDi = $_POST['benDi'];
    $benDen = $_POST['benDen'];

    // Kiểm tra xem có tồn tại chỉ mục 'enableflag' trong $_POST không
    $enableflag = $_POST['enableflag']; 
    if (empty($tenTuyenXe)) {
        $errorMessage = 'Vui lòng nhập tên tuyến xe.';
    } else {
        if ($maTuyenXe) {
            // Cập nhật tuyến xe
            $stmt = $conn->prepare("UPDATE tuyenxe SET TenTuyenXe = ?, KhoangCach = ?, BenDi = ?, BenDen = ?, enableflag = ? WHERE MaTuyenXe = ?");
            $stmt->bind_param("ssssii", $tenTuyenXe, $khoangCach, $benDi, $benDen, $enableflag, $maTuyenXe);

            if ($stmt->execute()) {
                $successMessage = 'Cập nhật tuyến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật tuyến xe.';
            }
        } else {
            // Thêm tuyến xe mới
            $stmt = $conn->prepare("INSERT INTO tuyenxe (TenTuyenXe, KhoangCach, BenDi, BenDen, enableflag) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $tenTuyenXe, $khoangCach, $benDi, $benDen, $enableflag);

            if ($stmt->execute()) {
                $successMessage = 'Thêm tuyến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm tuyến xe.';
            }
        }
    }
}

?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $maTuyenXe ? 'Chỉnh sửa' : 'Thêm mới'; ?> Tuyến Xe</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
                            <h5 class="card-header"><?php echo $maTuyenXe ? 'Chỉnh sửa tuyến xe' : 'Thêm tuyến xe mới'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="tenTuyenXe" class="form-label">Tên tuyến xe</label>
                                        <input type="text" class="form-control" id="tenTuyenXe" name="tenTuyenXe" value="<?php echo htmlspecialchars($tenTuyenXe); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="khoangCach" class="form-label">Khoảng cách</label>
                                        <input type="text" class="form-control" id="khoangCach" name="khoangCach" value="<?php echo htmlspecialchars($khoangCach); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="benDi" class="form-label">Bến đi</label>
                                        <select class="form-control" id="benDi" name="benDi" required>
                                            <?php foreach ($benXeList as $ben): ?>
                                                <option value="<?php echo $ben['MaBenXe']; ?>" <?php echo $benDi == $ben['MaBenXe'] ? 'selected' : ''; ?>>
                                                    <?php echo $ben['TenBenXe']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="benDen" class="form-label">Bến đến</label>
                                        <select class="form-control" id="benDen" name="benDen" required>
                                            <?php foreach ($benXeList as $ben): ?>
                                                <option value="<?php echo $ben['MaBenXe']; ?>" <?php echo $benDen == $ben['MaBenXe'] ? 'selected' : ''; ?>>
                                                    <?php echo $ben['TenBenXe']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="trangThai" class="form-label">Trạng thái</label>
                                        <select class="form-control" id="enableflag" name="enableflag" required>
                                            <option value="0" <?php echo $enableflag == 0 ? 'selected' : ''; ?>>Kích hoạt</option>
                                            <option value="1" <?php echo $enableflag == 1 ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $maTuyenXe ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="tuyenxe_manager.php" class="btn btn-secondary">Quay lại</a>
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
