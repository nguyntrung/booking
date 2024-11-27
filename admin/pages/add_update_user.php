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
$maNV = '';
$tenNV = '';
$namSinh = '';
$sdt = '';
$matKhau = '';
$diaChi = '';
$bangCap = '';
$loaiNV = '';
$enableFlag = 0;
$errorMessage = '';
$successMessage = '';

// Lấy danh sách loại nhân viên từ bảng loainhanvien
$sqlLoaiNV = "SELECT MaLoai, TenLoai FROM loainhanvien";
$resultLoaiNV = $conn->query($sqlLoaiNV);
$loaiNhanVien = [];
if ($resultLoaiNV && $resultLoaiNV->num_rows > 0) {
    while ($row = $resultLoaiNV->fetch_assoc()) {
        $loaiNhanVien[] = $row;
    }
}

// Kiểm tra xem có mã nhân viên để chỉnh sửa không
if (isset($_GET['id'])) {
    $maNV = $_GET['id'];

    // Lấy thông tin nhân viên từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT MaNV, TenNV, NamSinh, SDT, MatKhau, DiaChi, BangCap, LoaiNV, enableflag FROM nhanvien WHERE MaNV = ?");
    $stmt->bind_param("i", $maNV);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $nhanVien = $result->fetch_assoc();
        $tenNV = $nhanVien['TenNV'];
        $namSinh = $nhanVien['NamSinh'];
        $sdt = $nhanVien['SDT'];
        $matKhau = $nhanVien['MatKhau'];
        $diaChi = $nhanVien['DiaChi'];
        $bangCap = $nhanVien['BangCap'];
        $loaiNV = $nhanVien['LoaiNV'];
        $enableFlag = $nhanVien['enableflag'];
    } else {
        $errorMessage = 'Không tìm thấy nhân viên!';
    }
}

// Xử lý thêm hoặc cập nhật nhân viên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenNV = trim($_POST['tenNV']);
    $namSinh = trim($_POST['namSinh']);
    $sdt = trim($_POST['sdt']);
    $matKhau = trim($_POST['matKhau']);
    $diaChi = trim($_POST['diaChi']);
    $bangCap = trim($_POST['bangCap']);
    $loaiNV = $_POST['loaiNV'];
    $enableFlag = $_POST['enableFlag'];

    if (empty($tenNV)) {
        $errorMessage = 'Vui lòng nhập tên nhân viên.';
    } else {
        if ($maNV) {
            // Cập nhật nhân viên
            $stmt = $conn->prepare("UPDATE nhanvien SET TenNV = ?, NamSinh = ?, SDT = ?, MatKhau = ?, DiaChi = ?, BangCap = ?, LoaiNV = ?, enableflag = ? WHERE MaNV = ?");
            $stmt->bind_param("ssssssiii", $tenNV, $namSinh, $sdt, $matKhau, $diaChi, $bangCap, $loaiNV, $enableFlag, $maNV);

            if ($stmt->execute()) {
                $successMessage = 'Cập nhật nhân viên thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật nhân viên.';
            }
        } else {
            // Thêm nhân viên mới
            $stmt = $conn->prepare("INSERT INTO nhanvien (TenNV, NamSinh, SDT, MatKhau, DiaChi, BangCap, LoaiNV, enableflag) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssii", $tenNV, $namSinh, $sdt, $matKhau, $diaChi, $bangCap, $loaiNV, $enableFlag);

            if ($stmt->execute()) {
                $successMessage = 'Thêm nhân viên thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm nhân viên.';
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
    <title><?php echo $maNV ? 'Chỉnh sửa' : 'Thêm mới'; ?> Nhân viên</title>
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
                            <h5 class="card-header"><?php echo $maNV ? 'Chỉnh sửa nhân viên' : 'Thêm nhân viên mới'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="tenNV" class="form-label">Tên nhân viên</label>
                                        <input type="text" class="form-control" id="tenNV" name="tenNV" value="<?php echo htmlspecialchars($tenNV); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namSinh" class="form-label">Năm sinh</label>
                                        <input type="text" class="form-control" id="namSinh" name="namSinh" value="<?php echo htmlspecialchars($namSinh); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sdt" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" id="sdt" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="matKhau" class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" id="matKhau" name="matKhau" value="<?php echo htmlspecialchars($matKhau); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="diaChi" class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" id="diaChi" name="diaChi" value="<?php echo htmlspecialchars($diaChi); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bangCap" class="form-label">Bằng cấp</label>
                                        <input type="text" class="form-control" id="bangCap" name="bangCap" value="<?php echo htmlspecialchars($bangCap); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loaiNV" class="form-label">Loại nhân viên</label>
                                        <select class="form-control" id="loaiNV" name="loaiNV" required>
                                            <?php foreach ($loaiNhanVien as $loai): ?>
                                                <option value="<?php echo $loai['MaLoai']; ?>" <?php echo $loaiNV == $loai['MaLoai'] ? 'selected' : ''; ?>>
                                                    <?php echo $loai['TenLoai']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="enableFlag" class="form-label">Trạng thái</label>
                                        <select class="form-control" id="enableFlag" name="enableFlag" required>
                                            <option value="0" <?php echo $enableFlag == 0 ? 'selected' : ''; ?>>Kích hoạt</option>
                                            <option value="1" <?php echo $enableFlag == 1 ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $maNV ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="ú_manager.php" class="btn btn-secondary">Quay lại</a>
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
