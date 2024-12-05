<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';


// Xử lý thêm hoặc cập nhật tuyến xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Tuyen = trim($_POST['Tuyen']);
    $ThoiGianKhoiHanh = $_POST['ThoiGianKhoiHanh'];
    $ThoiGianKetThuc = $_POST['ThoiGianKetThuc'];
    $Xe = trim($_POST['Xe']);
    $GiaTien = trim($_POST['GiaTien']);
    $SoChoTrong = trim($_POST['SoChoTrong']);
    $TaiXe = trim($_POST['TaiXe']);
    $enableflag = isset($_POST['enableflag']) ? (int)$_POST['enableflag'] : 0;

    if($TaiXe==-1){
        $stmt = $conn->prepare("INSERT INTO chuyenxe(Tuyen, ThoiGianKhoiHanh, ThoiGianKetThuc, Xe, GiaTien, SoChoTrong, enableflag) VALUES ( ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiii", $Tuyen, $ThoiGianKhoiHanh, $ThoiGianKetThuc, $Xe, $GiaTien, $SoChoTrong, $enableflag);

        if ($stmt->execute()) {
            $successMessage = 'Thêm chuyến xe thành công!';
        } else {
            $errorMessage = 'Có lỗi xảy ra khi thêm chuyến xe.';
        }
    }else {
        $stmt = $conn->prepare("INSERT INTO chuyenxe(Tuyen, ThoiGianKhoiHanh, ThoiGianKetThuc, Xe, GiaTien, SoChoTrong, TaiXe, enableflag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiiii", $Tuyen, $ThoiGianKhoiHanh, $ThoiGianKetThuc, $Xe, $GiaTien, $SoChoTrong, $TaiXe, $enableflag);

        if ($stmt->execute()) {
            $successMessage = 'Thêm chuyến xe thành công!';
        } else {
            $errorMessage = 'Có lỗi xảy ra khi thêm chuyến xe.';
        }
    }

            
        
    
}

?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $maTuyenXe ? 'Chỉnh sửa' : 'Thêm mới'; ?> Chuyến Xe</title>
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
                            <h5 class="card-header"><?php echo $MaChuyenXe ? 'Chỉnh sửa chuyến xe' : 'Thêm chuyến xe mới'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                
                                <a href="./lichtrinh_manager.php" class="btn btn-secondary">Xong</a>
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
