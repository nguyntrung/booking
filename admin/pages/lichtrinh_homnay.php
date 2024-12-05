<?php
session_start();
include '../../database/db.php';

// Phan trang 1 / 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 8; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;
$sql_count = "SELECT COUNT(*) AS total FROM chuyenxe";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$totalRows = $row_count['total'];  // Lấy tổng số dòng
$totalPages = ceil($totalRows / $limit);
// Phan trang 1 / 1

// Lấy danh sách tuyến xe từ cơ sở dữ liệu, kết hợp với bảng benxe
$stmt = $conn->prepare("
    SELECT c.MaChuyenXe, t.TenTuyenXe, c.ThoiGianKhoiHanh, c.ThoiGianKetThuc, 
        c.GiaTien, c.SoChoTrong, x.BienSoXe, nv.TenNV, nv.SDT, 
        l.SucChua, c.enableflag, t.MaTuyenXe
    FROM chuyenxe c
    LEFT JOIN tuyenxe t ON c.Tuyen = t.MaTuyenXe
    LEFT JOIN nhanvien nv ON c.TaiXe = nv.MaNV
    LEFT JOIN xe x ON c.Xe = x.MaXe
    LEFT JOIN loaixe l ON x.LoaiXe = l.MaLoaiXe
    WHERE c.ThoiGianKhoiHanh BETWEEN CONCAT(CURDATE(), ' 00:00:00') 
        AND CONCAT(DATE_ADD(CURDATE(), INTERVAL 1 DAY), ' 02:00:00')
    ORDER BY c.MaChuyenXe DESC
    LIMIT ? OFFSET ?;


"); 
// Phan trang 2 / 2 CHÚ Ý Thêm LIMIT và OFFSET vào truy vấn
$stmt->bind_param("ii", $limit, $offset); 
// Phan trang 2 / 2
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
                    <a href="./lichtrinh_manager.php" class="btn btn-success mb-3">Xem tất cả</a>
                        <div class="card">
                            <h5 class="card-header">Danh sách chuyến xe hôm nay</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
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
                                                <td><a href="./lichtrinh_chitiet.php?id=<?php echo $chuyenxe['MaChuyenXe'] ?>"><i class="ri-eye-fill"></i></a></td>
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
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item"
                                                                href="./lichtrinh_update1.php?id=<?php echo $chuyenxe['MaChuyenXe']; ?>">
                                                                <i class="ri-pencil-line me-1"></i> Chỉnh sửa</a>
                                                            <a class="dropdown-item" href="#" onclick="confirmDelete('chuyenxe','<?php echo $chuyenxe['MaChuyenXe']; ?>', 1)">
                                                                <i class="ri-delete-bin-6-line me-1"></i> Xóa
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
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
                                        echo '<li class="page-item"><a class="page-link" href="?page=1">&laquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
                                    }

                                    // Các nút số trang
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }

                                    // Nút Next
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($totalPages) . '">&raquo;</a></li>';
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
<script>
        function confirmDelete(table, id, isEnable) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn có chắc chắn muốn xoá chuyến xe này không?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gọi hàm để cập nhật trạng thái
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    // Gửi dữ liệu đến server
                    xhr.send(`type=${table}&id=${id}&enableflag=${isEnable}`);

                    // Xử lý phản hồi từ server
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            Swal.fire(
                                'Thành công!',
                                'Đã thay đổi trạng thái chuyến xe thành công.',
                                'success'
                            );
                            $('.card-body').load(' .card-body>.table-responsive');
                        } else {
                            Swal.fire(
                                'Lỗi!',
                                'Đã có lỗi xảy ra. Vui lòng thử lại.',
                                'error'
                            );
                        }
                    };
                }
            });
        }
    </script>
</html>
