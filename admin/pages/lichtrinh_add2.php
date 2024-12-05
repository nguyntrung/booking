<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

if(!isset($_GET["Tuyen"]) || !isset($_GET["ThoiGianKhoiHanh"]) ||!isset($_GET["ThoiGianKetThuc"]) ||!isset($_GET["LoaiXe"]) ){
    header("Location: ./lichtrinh_add1.php");
    exit;
}

// Khởi tạo biến cho các trường
$Tuyen = $_GET["Tuyen"];
$ThoiGianKhoiHanh = $_GET["ThoiGianKhoiHanh"];
$ThoiGianKetThuc = $_GET["ThoiGianKetThuc"];
$LoaiXe = $_GET["LoaiXe"];
$SoChoTrong = '';
$Xe = '';
$GiaTien = '';
$TaiXe = '';

$sql = "SELECT SucChua FROM loaixe WHERE MaLoaiXe = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $LoaiXe); 
$stmt->execute();
$result = $stmt->get_result();

// Lấy dữ liệu
if ($row = $result->fetch_assoc()) {
    $SoChoTrong = $row['SucChua'];
}

// Lấy danh sách bến xe từ cơ sở dữ liệu (nếu cần)
$sqlxe = "SELECT MaXe, BienSoXe 
    FROM xe x
    WHERE x.LoaiXe=? and enableflag=0
    AND MaXe NOT IN (
        SELECT DISTINCT Xe
        FROM chuyenxe
        WHERE (ThoiGianKhoiHanh BETWEEN ? AND ?)
        OR (ThoiGianKetThuc BETWEEN ? AND ?)
    )";
$stmt = $conn->prepare($sqlxe);
$stmt->bind_param('issss', $LoaiXe, $ThoiGianKhoiHanh, $ThoiGianKetThuc, $ThoiGianKhoiHanh, $ThoiGianKetThuc);
$stmt->execute();
$resultXe = $stmt->get_result();
$xeList = [];
while ($row = $resultXe->fetch_assoc()) {
    $xeList[] = $row;
}

$sqlloaixe = "SELECT MaLoaiXe, SucChua FROM loaixe";
$resultLoaiXe = $conn->query($sqlloaixe);
$loaixeList = [];
if ($resultLoaiXe && $resultLoaiXe->num_rows > 0) {
    while ($row = $resultLoaiXe->fetch_assoc()) {
        $loaixeList[] = $row;
    }
}


// Tham số chuyến xe mới
$ThoiGianKhoiHanhMoi = $ThoiGianKhoiHanh;
$ThoiGianKetThucMoi = $ThoiGianKetThuc;
$TuyenMoi = $Tuyen;

// Lấy điểm bắt đầu và kết thúc của tuyến mới
$sqlTuyenMoi = "SELECT BenDi, BenDen FROM tuyenxe WHERE MaTuyenXe = ?";
$stmtTuyenMoi = $conn->prepare($sqlTuyenMoi);
$stmtTuyenMoi->bind_param('i', $TuyenMoi);
$stmtTuyenMoi->execute();
$resultTuyenMoi = $stmtTuyenMoi->get_result();
$tuyenMoiData = $resultTuyenMoi->fetch_assoc();
$BenDiMoi = $tuyenMoiData['BenDi'];
$BenDenMoi = $tuyenMoiData['BenDen'];

// Lấy danh sách tài xế (LoaiNV = 1) không trùng lịch
$sql = "SELECT MaNV, TenNV FROM nhanvien WHERE LoaiNV = 1 and enableflag=0 AND MaNV NOT IN (
            SELECT DISTINCT TaiXe
            FROM chuyenxe
            WHERE (ThoiGianKhoiHanh BETWEEN ? AND ?)
               OR (ThoiGianKetThuc BETWEEN ? AND ?)
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $ThoiGianKhoiHanhMoi, $ThoiGianKetThucMoi, $ThoiGianKhoiHanhMoi, $ThoiGianKetThucMoi);
$stmt->execute();
$result = $stmt->get_result();

$availableDrivers = [];
while ($row = $result->fetch_assoc()) {
    $MaNV = $row['MaNV'];

    // Lấy lộ trình cuối cùng của tài xế
    $sqlLastRoute = "SELECT cx.Tuyen, cx.ThoiGianKetThuc, tx.BenDen 
                     FROM chuyenxe cx
                     INNER JOIN tuyenxe tx ON cx.Tuyen = tx.MaTuyenXe
                     WHERE cx.TaiXe = ? 
                     ORDER BY cx.ThoiGianKetThuc DESC LIMIT 1";
    $stmtLastRoute = $conn->prepare($sqlLastRoute);
    $stmtLastRoute->bind_param('s', $MaNV);
    $stmtLastRoute->execute();
    $resultLastRoute = $stmtLastRoute->get_result();
    
    if ($rowLastRoute = $resultLastRoute->fetch_assoc()) {
        $BenDenCuoi = $rowLastRoute['BenDen'];
        $ThoiGianKetThucCuoi = $rowLastRoute['ThoiGianKetThuc'];

        // Kiểm tra điều kiện về thời gian và điểm
        $timeDiff = (strtotime($ThoiGianKhoiHanhMoi) - strtotime($ThoiGianKetThucCuoi)) / 86400;
        if ($timeDiff >= 2 || $BenDenCuoi === $BenDiMoi) {
            $availableDrivers[] = $row;
        }
    } else {
        // Tài xế chưa có lộ trình nào -> hợp lệ
        $availableDrivers[] = $row;
    }
}
$nhanvienList = $availableDrivers;

?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $maTuyenXe ? 'Chỉnh sửa' : 'Thêm mới'; ?> Thêm chuyến xe</title>
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
                            <h5 class="card-header">Thêm chuyến xe mới 2/3</h5>
                            <div class="card-body">
                                <form action="./lichtrinh_add3.php" method="POST">
                                    <div class="mb-3 d-none">
                                        <label for="Tuyen" class="form-label">Tuyến</label>
                                        <input type="text" class="form-control" value="<?php echo $Tuyen; ?>" id="Tuyen" name="Tuyen" >
                                    </div>
                                    <div class="mb-3 d-none">
                                        <label for="ThoiGianKhoiHanh" class="form-label">Thời gian khởi hành</label>
                                        <input type="text" class="form-control" value="<?php echo $ThoiGianKhoiHanh; ?>" id="ThoiGianKhoiHanh" name="ThoiGianKhoiHanh" >
                                    </div>
                                    <div class="mb-3  d-none">
                                        <label for="ThoiGianKetThuc" class="form-label">Thời gian kết thúc</label>
                                        <input type="text" class="form-control" value="<?php echo $ThoiGianKetThuc; ?>" id="ThoiGianKetThuc" name="ThoiGianKetThuc" >
                                    </div>
                                    <div class="mb-3  d-none">
                                        <label for="LoaiXe" class="form-label">Loại xe</label>
                                        <input type="text" class="form-control" value="<?php echo $LoaiXe; ?>" id="LoaiXe" name="LoaiXe" >
                                    </div>
                                    <div class="mb-3  d-none">
                                        <label for="SoChoTrong" class="form-label">Số chỗ trống</label>
                                        <input type="text" class="form-control" value="<?php echo $SoChoTrong; ?>" id="SoChoTrong" name="SoChoTrong" >
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
                                    <div class="mb-3">
                                        <label for="TaiXe" class="form-label">Tài Tế</label>
                                        <select class="form-control" id="TaiXe" name="TaiXe" required>
                                        <option value="-1">--</option>
                                        <?php foreach ($nhanvienList as $nv): ?>
                                                <option value="<?php echo $nv['MaNV']; ?>">
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
                                    <button type="submit" class="btn btn-primary">Tiếp tục</button>
                                    <a href="./lichtrinh_add1.php?Tuyen=<?php echo $Tuyen;?>&LoaiXe=<?php echo $LoaiXe;?>&ThoiGianKhoiHanh=<?php echo $ThoiGianKhoiHanh;?>&ThoiGianKetThuc=<?php echo $ThoiGianKetThuc;?>" class="btn btn-secondary">Quay lại</a>
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
<script>
    function fetchAvailableDrivers() {
        const startTime = document.getElementById('ThoiGianKhoiHanh').value;
        const endTime = document.getElementById('ThoiGianKetThuc').value;

        if (startTime && endTime) {
            fetch('lichtrinh_getTaiXe.php?startTime=' + startTime + '&endTime=' + endTime)
                .then(response => response.json())
                .then(data => {
                    const driverSelect = document.getElementById('TaiXe');
                    driverSelect.innerHTML = ''; // Xóa các lựa chọn cũ

                    data.forEach(driver => {
                        const option = document.createElement('option');
                        option.value = driver.MaNV;
                        option.textContent = driver.TenNV;
                        driverSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching drivers:', error));
        }
    }

    document.getElementById('ThoiGianKhoiHanh').addEventListener('change', fetchAvailableDrivers);
    document.getElementById('ThoiGianKetThuc').addEventListener('change', fetchAvailableDrivers);
</script>


</body>

</html>
