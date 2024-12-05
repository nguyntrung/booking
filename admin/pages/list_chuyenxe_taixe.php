<?php
session_start();
include '../../database/db.php';

// Kiểm tra nếu MaNV có trong session
if (!isset($_SESSION['MaNV'])) {
    header('Location: login.php'); // Chuyển hướng về trang đăng nhập
    exit();
}

$MaNV = $_SESSION['MaNV']; // Lấy MaNV từ session

// Phan trang 1 / 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 8; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

// Lấy tổng số chuyến xe của tài xế từ cơ sở dữ liệu
$sql_count = "SELECT COUNT(*) AS total FROM chuyenxe WHERE TaiXe = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $MaNV); // Truyền MaNV vào
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$totalRows = $row_count['total'];  // Lấy tổng số dòng
$totalPages = ceil($totalRows / $limit);
// Phan trang 1 / 1

// Lấy danh sách chuyến xe của tài xế từ cơ sở dữ liệu, kết hợp với bảng benxe
$stmt = $conn->prepare("
    SELECT c.MaChuyenXe, t.TenTuyenXe, c.ThoiGianKhoiHanh, c.ThoiGianKetThuc, c.GiaTien, c.SoChoTrong, 
        x.BienSoXe, nv.TenNV, nv.SDT, l.SucChua, c.enableflag, t.MaTuyenXe
    FROM chuyenxe c
    LEFT JOIN tuyenxe t ON c.Tuyen = t.MaTuyenXe
    LEFT JOIN nhanvien nv ON c.TaiXe = nv.MaNV
    LEFT JOIN xe x ON c.Xe = x.MaXe
    LEFT JOIN loaixe l ON x.LoaiXe = l.MaLoaiXe
    WHERE c.TaiXe = ?  -- Lọc theo TaiXe
    ORDER BY c.MaChuyenXe DESC
    LIMIT ? OFFSET ?
"); 
$stmt->bind_param("iii", $MaNV, $limit, $offset); // Truyền MaNV, limit, offset
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
                        <div class="card">
                            <h5 class="card-header">Danh sách chuyến xe</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã chuyến xe</th>
                                                <th>Tên tuyến xe</th>
                                                <th>Giờ khởi hành</th>
                                                <th>Giờ kết thúc</th>
                                                <th>Giá tiền</th>
                                                <th>Số chỗ trống</th>
                                                <th>Biển số xe</th>
                                                <th>Tên tài xế</th>
                                                <th>SDT tài xế</th>
                                                <th>Sức chứa</th>
                                                <th>Trạng thái</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($chuyenxeList as $chuyenxe): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($chuyenxe['MaChuyenXe']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['TenTuyenXe']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['ThoiGianKhoiHanh']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['ThoiGianKetThuc']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['GiaTien']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['SoChoTrong']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['BienSoXe']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['TenNV']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['SDT']); ?></td>
                                                <td><?php echo htmlspecialchars($chuyenxe['SucChua']); ?></td>
                                                <td>
                                                    <?php 
                                                        if ($chuyenxe['enableflag'] == 1) {
                                                            echo '<span class="badge bg-danger">Vô hiệu hóa</span>';
                                                        } else {
                                                            echo '<span class="badge bg-success">Kích hoạt</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown">
                                                            <i class="ri-more-2-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                 </div>
                            </div>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=1" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link"
                                        href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo ($page == $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $totalPages; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <?php include 'other.php'; ?>
    </div>
</body>
</html>
