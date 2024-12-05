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
$Tuyen = isset($_GET["Tuyen"])?$_GET["Tuyen"]:'';
$ThoiGianKhoiHanh = isset($_GET["ThoiGianKhoiHanh"])?$_GET["ThoiGianKhoiHanh"]:'';
$ThoiGianKetThuc = isset($_GET["ThoiGianKetThuc"])?$_GET["ThoiGianKetThuc"]:'';
$LoaiXe = isset($_GET["LoaiXe"])?$_GET["LoaiXe"]:'';

// Lấy danh sách bến xe từ cơ sở dữ liệu (nếu cần)
$sqltuyen = "SELECT MaTuyenXe, TenTuyenXe FROM tuyenxe WHERE enableflag=0";
$resultTuyen = $conn->query($sqltuyen);
$tuyenxeList = [];
if ($resultTuyen && $resultTuyen->num_rows > 0) {
    while ($row = $resultTuyen->fetch_assoc()) {
        $tuyenxeList[] = $row;
    }
}

$sqlloaixe = "SELECT * FROM loaixe WHERE enableflag=0";
$resultLoaiXe = $conn->query($sqlloaixe);
$loaixeList = [];
if ($resultLoaiXe && $resultLoaiXe->num_rows > 0) {
    while ($row = $resultLoaiXe->fetch_assoc()) {
        $loaixeList[] = $row;
    }
}

?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $maTuyenXe ? 'Chỉnh sửa' : 'Thêm mới'; ?>Thêm chuyến xe</title>
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
                            <h5 class="card-header">Thêm chuyến xe mới 1/3</h5>
                            <div class="card-body">
                                
                                <form action="./lichtrinh_add2.php" method="GET">
                                    <div class="mb-3">
                                        <label for="Tuyen" class="form-label">Tuyến</label>
                                        <select class="form-control" id="Tuyen" name="Tuyen" required  >
                                            <?php foreach ($tuyenxeList as $tuyenxe): ?>
                                                <option value="<?php echo $tuyenxe['MaTuyenXe']; ?>" <?php echo $tuyenxe['MaTuyenXe']==$Tuyen?'selected':'' ?>>
                                                    <?php echo $tuyenxe['TenTuyenXe']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3" style="display: flex;">
                                        <div class=" m-1" style="flex: 1;">
                                            <label for="ThoiGianKhoiHanh" class="form-label">Thời gian khởi hành</label>
                                            <input type="datetime-local" class="form-control" id="ThoiGianKhoiHanh" name="ThoiGianKhoiHanh" 
                                                value="<?php echo htmlspecialchars($ThoiGianKhoiHanh); ?>" required>
                                        </div>
                                        
                                        <div class="m-1" style="flex:1;">
                                            <label for="ThoiGianKetThuc" class="form-label">Thời gian kết thúc</label>
                                            <input type="datetime-local" class="form-control" id="ThoiGianKetThuc" name="ThoiGianKetThuc" 
                                                value="<?php echo htmlspecialchars($ThoiGianKetThuc); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="LoaiXe" class="form-label">Loại xe</label>
                                        <select class="form-control" id="LoaiXe" name="LoaiXe" required  >
                                            <?php foreach ($loaixeList as $lx): ?>
                                                <option value="<?php echo $lx['MaLoaiXe']; ?>" <?php echo $lx['MaLoaiXe']==$LoaiXe?'selected':'' ?>>
                                                    <?php echo $lx['TenLoaiXe']." - ".$lx['SucChua']." chỗ"; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Tiếp theo</button>
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
