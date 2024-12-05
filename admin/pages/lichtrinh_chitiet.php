<?php
session_start();
include '../../database/db.php';

if(isset($_GET["id"])){
    $id = $_GET["id"];
}else {
    header("Location: lichtrinh_homnay.php");
    exit;
}

// Phan trang 1 / 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 8; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;
$sql_count = "SELECT COUNT(*) AS total FROM ve where ChuyenXe=? and enableflag=0";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$totalRows = $row_count['total'];  // Lấy tổng số dòng
$totalPages = ceil($totalRows / $limit);
// Phan trang 1 / 1

// Lấy danh sách tuyến xe từ cơ sở dữ liệu, kết hợp với bảng benxe
$stmt = $conn->prepare("
    SELECT v.MaVeXe, v.MaCho, hd.MaHoaDon, hd.TrangThaiThanhToan, c.GiaTien, hk.TenHK, hk.SDT, hk.NamSinh, hk.GioiTinh, hk.CCCD
FROM ve v
INNER JOIN hoadon hd ON v.HoaDon = hd.MaHoaDon
INNER JOIN hanhkhach hk ON hd.HanhKhach = hk.MaHK
INNER JOIN chuyenxe c ON v.ChuyenXe = c.MaChuyenXe
WHERE v.ChuyenXe = ? AND v.enableflag = 0 AND hd.enableflag = 0
LIMIT ? OFFSET ?;

"); 
// Phan trang 2 / 2 CHÚ Ý Thêm LIMIT và OFFSET vào truy vấn
$stmt->bind_param("iii", $id, $limit, $offset); 
// Phan trang 2 / 2

if (!$stmt_count || !$stmt) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$chuyenxeList = $result->fetch_all(MYSQLI_ASSOC); // Trả về mảng kết hợp
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
<meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý lịch trình</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Page CSS -->
    <!-- Helpers -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    <style>
        .badge.bg-success {
            background-color: #28a745; /* Màu xanh lá cây */
            color: white;
        }

        .badge.bg-danger {
            background-color: #dc3545; /* Màu đỏ */
            color: white;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'sidebar.php'; ?>
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                    <a href="./lichtrinh_homnay.php" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card">
                            <h5 class="card-header">Danh sách vé của chuyến <?php echo $id; ?></h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã vé </th>
                                                <th>Tên hành khách </th>
                                                <th>Mã chỗ </th>
                                                <th>Mã hóa đơn </th>
                                                <th>Giá tiền </th>
                                                <th>Trạng thái thanh toán </th>
                                                <th>SDT </th>
                                                <th>Năm sinh </th>
                                                <th>Giới tính </th>
                                                <th>CCCD </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($chuyenxeList as $chuyenxe): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($chuyenxe['MaVeXe']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['TenHK']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['MaCho']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['MaHoaDon']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['GiaTien']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['TrangThaiThanhToan']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['SDT']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['NamSinh']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['GioiTinh']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['CCCD']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    
                                    
                                    </div>
                            </div>
                        </div>
                        <!-- Phan trang 3 / 3 -->
                        <?php
                                    // Hiển thị phân trang
                                    echo '<nav aria-label="Page navigation" class="p-5 fs-3">';
                                    echo '<ul class="pagination justify-content-center green-pagination">';

                                    // Nút Previous
                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?id='.$id.'&page=1">&laquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
                                    }

                                    // Các nút số trang
                                    if ($totalPages > 0) {
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><a class="page-link" href="?id='.$id.'&page=' . $i . '">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="?id='.$id.'&page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }}

                                    // Nút Next
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item"><a class="page-link" href="?id='.$id.'&page=' . ($totalPages) . '">&raquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
                                    }

                                    echo '</ul>';
                                    echo '</nav>';
                                    ?>
                                    <!-- Phan trang 3 / 3 -->
                        
                    </div>
                    <!-- Content wrapper -->
                </div>

            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <?php include 'other.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
