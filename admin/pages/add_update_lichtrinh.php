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
$MaChuyenXe = '';
$Tuyen = '';
$ThoiGianKhoiHanh = '';
$ThoiGianKetThuc = '';
$Xe = '';
$GiaTien = '';
$SoChoTrong = '';
$TaiXe = '';
$enableflag = 0;
$errorMessage = '';
$successMessage = '';

// Lấy danh sách bến xe từ cơ sở dữ liệu (nếu cần)
$sqltuyen = "SELECT MaTuyenXe, TenTuyenXe FROM tuyenxe";
$sqlxe = "SELECT MaXe, BienSoXe FROM xe";
$sqlloaixe = "SELECT MaLoaiXe, SucChua FROM loaixe";
$sqlnhanvien = "SELECT MaNV, TenNV FROM nhanvien WHERE LoaiNV = 1";
$resultTuyen = $conn->query($sqltuyen);
$tuyenxeList = [];
if ($resultTuyen && $resultTuyen->num_rows > 0) {
    while ($row = $resultTuyen->fetch_assoc()) {
        $tuyenxeList[] = $row;
    }
}
$resultXe = $conn->query($sqlxe);
$xeList = [];
if ($resultXe && $resultXe->num_rows > 0) {
    while ($row = $resultXe->fetch_assoc()) {
        $xeList[] = $row;
    }
}
$resultLoaiXe = $conn->query($sqlloaixe);
$loaixeList = [];
if ($resultLoaiXe && $resultLoaiXe->num_rows > 0) {
    while ($row = $resultLoaiXe->fetch_assoc()) {
        $loaixeList[] = $row;
    }
}
$resultNV = $conn->query($sqlnhanvien);
$nhanvienList = [];
if ($resultNV && $resultNV->num_rows > 0) {
    while ($row = $resultNV->fetch_assoc()) {
        $nhanvienList[] = $row;
    }
}

// Kiểm tra xem có mã tuyến xe để chỉnh sửa không
if (isset($_GET['id'])) {
    $machuyenxe = $_GET['id'];

    // Lấy thông tin tuyến xe từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT * FROM chuyenxe WHERE MaChuyenXe = ?");
    $stmt->bind_param("i", $machuyenxe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $chuyenxe = $result->fetch_assoc();
        $MaChuyenXe = $chuyenxe['MaChuyenXe'];
        $Tuyen = $chuyenxe['Tuyen'];
        $ThoiGianKhoiHanh = $chuyenxe['ThoiGianKhoiHanh'];
        $ThoiGianKetThuc = $chuyenxe['ThoiGianKetThuc'];
        $Xe = $chuyenxe['Xe'];
        $GiaTien = $chuyenxe['GiaTien'];
        $SoChoTrong = $chuyenxe['SoChoTrong'];
        $TaiXe = $chuyenxe['TaiXe'];
        $enableflag = $chuyenxe['enableflag'];
    } else {
        $errorMessage = 'Không tìm thấy chuyến xe!';
    }
}

// Xử lý thêm hoặc cập nhật tuyến xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Tuyen = trim($_POST['Tuyen']);
    $ThoiGianKhoiHanh = str_replace('T', ' ', $_POST['ThoiGianKhoiHanh']);
    $ThoiGianKetThuc = str_replace('T', ' ', $_POST['ThoiGianKetThuc']);
    $Xe = trim($_POST['Xe']);
    $GiaTien = trim($_POST['GiaTien']);
    $SoChoTrong = isset($_POST['SoChoTrong']) ? (int)trim($_POST['SoChoTrong']) : 0;
    $TaiXe = trim($_POST['TaiXe']);
    $enableflag = isset($_POST['enableflag']) ? (int)$_POST['enableflag'] : 0;

    if (empty($GiaTien)) {
        $errorMessage = 'Vui lòng nhập giá chuyến.';
    } else {
        if ($MaChuyenXe) {
            // Cập nhật tuyến xe
            $stmt = $conn->prepare("UPDATE chuyenxe SET Tuyen = ?, ThoiGianKhoiHanh = ?, ThoiGianKetThuc = ?, Xe = ?, GiaTien = ?, SoChoTrong = ?, TaiXe = ?, enableflag = ? WHERE MaChuyenXe = ?");
            $stmt->bind_param("ssssiiisi", $Tuyen, $ThoiGianKhoiHanh, $ThoiGianKetThuc, $Xe, $GiaTien, $SoChoTrong, $TaiXe, $enableflag, $MaChuyenXe);

            if ($stmt->execute()) {
                $successMessage = 'Cập nhật chuyến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật chuyến xe.';
            }
        } else {
            // Thêm tuyến xe mới
            foreach ($loaixeList as $lx) {
                if ($lx["MaLoaiXe"] == $Xe) {
                    $SoChoTrong = (int)$lx["SucChua"];
                } else {
                    $SoChoTrong = 16;
                }
            }
            $stmt = $conn->prepare("INSERT INTO chuyenxe(Tuyen, ThoiGianKhoiHanh, ThoiGianKetThuc, Xe, GiaTien, SoChoTrong, TaiXe, enableflag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiiis", $Tuyen, $ThoiGianKhoiHanh, $ThoiGianKetThuc, $Xe, $GiaTien, $SoChoTrong, $TaiXe, $enableflag);

            if ($stmt->execute()) {
                $successMessage = 'Thêm chuyến xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm tuychuyếnến xe.';
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
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="Tuyen" class="form-label">Tuyến</label>
                                        <select class="form-control" id="Tuyen" name="Tuyen" required <?php echo $MaChuyenXe ? 'disabled' : ''; ?> >
                                            <?php foreach ($tuyenxeList as $tuyenxe): ?>
                                                <option value="<?php echo $tuyenxe['MaTuyenXe']; ?>" <?php echo $Tuyen == $tuyenxe['MaTuyenXe'] ? 'selected' : ''; ?>>
                                                    <?php echo $tuyenxe['TenTuyenXe']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ThoiGianKhoiHanh" class="form-label">Thời gian khởi hành</label>
                                        <input type="datetime-local" class="form-control" id="ThoiGianKhoiHanh" name="ThoiGianKhoiHanh" 
                                            value="<?php echo htmlspecialchars($ThoiGianKhoiHanh); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ThoiGianKetThuc" class="form-label">Thời gian kết thúc</label>
                                        <input type="datetime-local" class="form-control" id="ThoiGianKetThuc" name="ThoiGianKetThuc" 
                                            value="<?php echo htmlspecialchars($ThoiGianKetThuc); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="Xe" class="form-label">Xe</label>
                                        <select class="form-control" id="Xe" name="Xe" required>
                                            <?php foreach ($xeList as $xeitem): ?>
                                                <option value="<?php echo $xeitem['MaXe']; ?>" <?php echo $Xe == $xeitem['MaXe'] ? 'selected' : ''; ?>>
                                                    <?php echo $xeitem['BienSoXe']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="GiaTien" class="form-label">Giá tiền</label>
                                        <input type="number" class="form-control" id="GiaTien" name="GiaTien" value="<?php echo htmlspecialchars($GiaTien); ?>" required>
                                    </div>
                                    <div class="mb-3 <?php echo $MaChuyenXe ? '' : 'd-none'; ?> ">
                                        <label for="SoChoTrong" class="form-label">Số chỗ trống</label>
                                        <input type="text" class="form-control" id="SoChoTrong" name="SoChoTrong" value="<?php echo htmlspecialchars($SoChoTrong); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="TaiXe" class="form-label">Tài Tế</label>
                                        <select class="form-control" id="TaiXe" name="TaiXe" required>
                                            <?php foreach ($nhanvienList as $nv): ?>
                                                <option value="<?php echo $nv['MaNV']; ?>" <?php echo $TaiXe == $nv['MaNV'] ? 'selected' : ''; ?>>
                                                    <?php echo $nv['TenNV']; ?>
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
                                    <button type="submit" class="btn btn-primary"><?php echo $MaChuyenXe ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="./lichtrinh_manager.php" class="btn btn-secondary">Quay lại</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
        </div>
    </div>
    <script>
    // Thiết lập thời gian tối thiểu là hiện tại
    const now = new Date();
    const localISOTime = now.toISOString().slice(0, 16); // Định dạng `YYYY-MM-DDTHH:mm`
    const khoiHanhInput = document.getElementById('ThoiGianKhoiHanh');
    const ketThucInput = document.getElementById('ThoiGianKetThuc');

    khoiHanhInput.setAttribute('min', localISOTime);
    ketThucInput.setAttribute('min', localISOTime);

    // Cập nhật min cho thời gian kết thúc khi thay đổi thời gian khởi hành
    khoiHanhInput.addEventListener('change', function () {
        const khoiHanh = this.value;
        ketThucInput.setAttribute('min', khoiHanh);

        // Kiểm tra và xóa giá trị không hợp lệ
        if (ketThucInput.value && ketThucInput.value <= khoiHanh) {
            ketThucInput.value = '';
        }
    });

    // Kiểm tra tính hợp lệ khi chọn thời gian kết thúc
    ketThucInput.addEventListener('change', function () {
        const ketThuc = this.value;
        const khoiHanh = khoiHanhInput.value;

        if (khoiHanh && ketThuc <= khoiHanh) {
            alert('Thời gian kết thúc phải lớn hơn thời gian khởi hành!');
            this.value = '';
        }
    });
</script>

</body>

</html>
