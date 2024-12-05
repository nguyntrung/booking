<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

$errorMessage = '';
$successMessage = '';

// Khởi tạo biến cho các trường
if (isset($_GET['id'])) {
    $MaChuyenXe = $_GET['id'];

    // Lấy thông tin bến xe từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT c.MaChuyenXe, c.Tuyen, t.TenTuyenXe, c.ThoiGianKhoiHanh, c.ThoiGianKetThuc, c.GiaTien, c.SoChoTrong, 
        c.Xe, x.BienSoXe, x.LoaiXe, c.TaiXe, nv.TenNV, nv.SDT, l.TenLoaiXe, l.SucChua, c.enableflag, t.MaTuyenXe
    FROM chuyenxe c
    LEFT JOIN tuyenxe t ON c.Tuyen = t.MaTuyenXe
    LEFT JOIN nhanvien nv ON c.TaiXe = nv.MaNV
    LEFT JOIN xe x ON c.Xe = x.MaXe
    LEFT JOIN loaixe l ON x.LoaiXe = l.MaLoaiXe
    WHERE MaChuyenXe = ?");
    $stmt->bind_param("i", $MaChuyenXe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $txe = $result->fetch_assoc();
        $MaChuyenXe = $txe['MaChuyenXe'];
        $Tuyen = $txe['Tuyen'];
        $TenTuyenXe = $txe['TenTuyenXe'];
        $ThoiGianKhoiHanh = $txe['ThoiGianKhoiHanh'];
        $ThoiGianKetThuc = $txe['ThoiGianKetThuc'];
        $Xe = $txe['Xe'];
        $LoaiXe = $txe['LoaiXe'];
        $TenLoaiXe = $txe['TenLoaiXe'];
        $SucChua = $txe['SucChua'];
        $BienSoXe = $txe['BienSoXe'];
        $GiaTien = $txe['GiaTien'];
        $SoChoTrong = $txe['SoChoTrong'];
        $TaiXe = $txe['TaiXe'];
        $TenNV = $txe['TenNV'];
        $enableflag = $txe['enableflag'];
    } else {
        header("Location: ./lichtrinh_manager.php");
        exit;
    }
} else {
    header("Location: ./lichtrinh_manager.php");
    exit;
}
if(isset($_GET["kq"])){
    $kq=$_GET["kq"];
}else $kq=-1;

$sqlxe = "SELECT MaXe, BienSoXe 
    FROM xe 
    WHERE  enableflag=0 and LoaiXe =?
    ";
$stmt = $conn->prepare($sqlxe);
$stmt->bind_param('i', $LoaiXe);
$stmt->execute();
$resultXe = $stmt->get_result();
$xeList = [];
while ($row = $resultXe->fetch_assoc()) {
    $xeList[] = $row;
}

$sqlNV = "SELECT MaNV, TenNV 
    FROM nhanvien 
    WHERE  enableflag=0 and LoaiNV =1
    ";
$stmt = $conn->prepare($sqlNV);
$stmt->execute();
$resultNV = $stmt->get_result();
$NVList = [];
while ($row = $resultNV->fetch_assoc()) {
    $NVList[] = $row;
}

?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $maTuyenXe ? 'Chỉnh sửa' : 'Thêm mới'; ?>Sửa chuyến xe</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <h5 class="card-header">Sửa chuyến xe</h5>
                            <?php
                                $messages = [
                                    1 => ['type' => 'success', 'text' => 'Cập nhật giá tiền thành công.'],
                                    2 => ['type' => 'danger', 'text' => 'Cập nhật giá tiền thất bại.'],
                                    3 => ['type' => 'success', 'text' => 'Cập nhật ngày giờ thành công.'],
                                    4 => ['type' => 'danger', 'text' => 'Cập nhật ngày giờ thất bại.'],
                                    5 => ['type' => 'success', 'text' => 'Cập nhật xe thành công.'],
                                    6 => ['type' => 'danger', 'text' => 'Cập nhật xe thất bại.'],
                                    7 => ['type' => 'success', 'text' => 'Cập nhật tài xế thành công.'],
                                    8 => ['type' => 'danger', 'text' => 'Cập nhật tài xế thất bại.'],
                                ];
                                if (isset($messages[$kq])): ?>
                                    <div class="alert alert-<?php echo $messages[$kq]['type']; ?>">
                                        <?php echo $messages[$kq]['text']; ?>
                                    </div>
                                <?php endif; 
                            ?>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p>Tuyến: <?php echo $TenTuyenXe; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <p>Số chỗ trống: <?php echo $SoChoTrong; ?></p>
                                    </div>
                                    <form action="./lichtrinh_update2.php" method="post">
                                        <input class="d-none" type="number" name="id" value="<?php echo $MaChuyenXe; ?>">
                                        <div class="mb-3">
                                            <div class="mb-3" style="display: flex;">
                                                <div style="margin-right:20px; display: flex; flex-direction: column; justify-content: center;">
                                                <p class="m-0">Thòi gian:</p>
                                                </div>
                                                <div class=" m-1" style="flex: 1;">
                                                    <input type="datetime-local" class="form-control" id="ThoiGianKhoiHanh" name="ThoiGianKhoiHanh" 
                                                        value="<?php echo htmlspecialchars($ThoiGianKhoiHanh); ?>" required>
                                                </div>
                                                <div style="display: flex; flex-direction: column; justify-content: center;"> <i class="ri-arrow-right-line"></i> </div>
                                                <div class="m-1" style="flex:1;">
                                                    <input type="datetime-local" class="form-control" id="ThoiGianKetThuc" name="ThoiGianKetThuc" 
                                                        value="<?php echo htmlspecialchars($ThoiGianKetThuc); ?>" required>
                                                </div>
                                                <div style="margin-left:20px; display: flex; flex-direction: column; justify-content: center;"><button type="submit" class="btn btn-success text-white"><i class="ri-save-fill"></i></button></div>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <form action="./lichtrinh_update2.php" method="post">
                                        <input class="d-none" type="number" name="id" value="<?php echo $MaChuyenXe; ?>">
                                        <div class="mb-3">
                                            <div class="mb-3" style="display: flex;">
                                                <div style="margin-right:20px; display: flex; flex-direction: column; justify-content: center;">
                                                    <p class="m-0">Giá tiền: </p>
                                                </div>
                                                <input class="form-control" style="margin: 0; flex:1;" type="number" name="GiaTien" value="<?php echo $GiaTien; ?>">
                                                <div style=" margin-left:20px; display: flex; flex-direction: column; justify-content: center;"><button type="submit" class="btn btn-success text-white"><i class="ri-save-fill"></i></button></div>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="./lichtrinh_update2.php" method="post">
                                        <input class="d-none" type="number" name="id" value="<?php echo $MaChuyenXe; ?>">
                                        <div class="mb-3">
                                            <div class="mb-3" style="display: flex;">
                                                <div style="margin-right:20px; display: flex; flex-direction: column; justify-content: center;">
                                                    <p class="m-0"><?php echo $TenLoaiXe." ".$SucChua." chỗ: "; ?></p>
                                                </div>
                                                <select class="form-control" id="Xe" name="Xe" required style="flex:1">
                                                    <option value="-1">--</option>
                                                    <?php foreach ($xeList as $xeitem): ?>
                                                        <option value="<?php echo $xeitem['MaXe']; ?>" <?php echo $Xe == $xeitem['MaXe'] ? 'selected' : ''; ?>>
                                                            <?php echo $xeitem['BienSoXe']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div style=" margin-left:20px; display: flex; flex-direction: column; justify-content: center;"><button type="submit" class="btn btn-success text-white"><i class="ri-save-fill"></i></button></div>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="./lichtrinh_update2.php" method="post">
                                        <input class="d-none" type="number" name="id" value="<?php echo $MaChuyenXe; ?>">
                                        <div class="mb-3">
                                            <div class="mb-3" style="display: flex;">
                                                <div style="margin-right:20px; display: flex; flex-direction: column; justify-content: center;">
                                                    <p class="m-0">Tài xế:</p>
                                                </div>
                                                <select class="form-control" id="TaiXe" name="TaiXe" required style="flex:1">
                                                    <option value="-1">--</option>
                                                    <?php foreach ($NVList as $nvitem): ?>
                                                        <option value="<?php echo $nvitem['MaNV']; ?>" <?php echo $TaiXe == $nvitem['MaNV'] ? 'selected' : ''; ?>>
                                                            <?php echo $nvitem['TenNV']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div style=" margin-left:20px; display: flex; flex-direction: column; justify-content: center;"><button type="submit" class="btn btn-success text-white"><i class="ri-save-fill"></i></button></div>
                                            </div>
                                        </div>
                                    </form>
                                    <a href="./lichtrinh_manager.php" class="btn btn-primary">Xong</a>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
