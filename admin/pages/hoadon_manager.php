<?php
session_start();

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Phan trang 1 / 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 8; // Số hóa đơn mỗi trang
$offset = ($page - 1) * $limit;
$sql_count = "SELECT COUNT(*) AS total FROM hoadon";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$totalRows = $row_count['total'];  // Lấy tổng số dòng
$totalPages = ceil($totalRows / $limit);
// Phan trang 1 / 1

$stmt = $conn->prepare("
    SELECT 
        h.MaHoaDon, 
        h.NgayLap, 
        h.SoLuongVe, 
        h.TongTien, 
        h.NhanVien, 
        h.HanhKhach, 
        h.enableflag, 
        h.TrangThaiThanhToan,
        hk.TenHK
    FROM hoadon h
    JOIN hanhkhach hk ON h.HanhKhach = hk.MaHK
    ORDER BY h.MaHoaDon ASC 
    LIMIT ? OFFSET ?
");

$stmt->bind_param("ii", $limit, $offset); 

// Thực thi truy vấn
$stmt->execute();

// Kiểm tra lỗi khi thực thi truy vấn
if ($stmt->error) {
    die('Lỗi khi thực thi truy vấn: ' . $stmt->error);
}

// Lấy kết quả truy vấn
$result = $stmt->get_result();
$hoadonList = $result->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý hóa đơn</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    <style>
        .badge.bg-success {
            background-color: #28a745;
            color: white;
        }
        .badge.bg-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
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
                            <h5 class="card-header">Danh sách hóa đơn</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã Hóa Đơn</th>
                                                <th>Ngày lập</th>
                                                <th>Số lượng vé</th>
                                                <th>Tổng tiền</th>
                                                <th>Hành khách</th>
                                                <th>Trạng thái thanh toán</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($hoadonList as $hoadon): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($hoadon['MaHoaDon']); ?></td>
                                                <td><?php echo htmlspecialchars($hoadon['NgayLap']); ?></td>
                                                <td><?php echo htmlspecialchars($hoadon['SoLuongVe']); ?></td>
                                                <td><?php echo htmlspecialchars($hoadon['TongTien']); ?></td>
                                                <td><?php echo htmlspecialchars($hoadon['TenHK']); ?></td>
                                                <td>
                                                    <select class="form-control" onchange="updatePaymentStatus(<?php echo $hoadon['MaHoaDon']; ?>, this.value)">
                                                        <option value="Pending" <?php echo ($hoadon['TrangThaiThanhToan'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Success" <?php echo ($hoadon['TrangThaiThanhToan'] == 'Success') ? 'selected' : ''; ?>>Success</option>
                                                        <option value="Failed" <?php echo ($hoadon['TrangThaiThanhToan'] == 'Failed') ? 'selected' : ''; ?>>Failed</option>
                                                    </select>
                                                </td>

                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                    <?php 
                                                        // Kiểm tra trạng thái enableflag và hiển thị màu sắc tương ứng
                                                        if ($hoadon['enableflag'] == 1) {
                                                            echo "<a class='badge bg-danger' href='#' onclick=\"confirmDelete('hoadon','{$hoadon['MaHoaDon']}', 0)\"> Ẩn </a>";
                                                        } else {
                                                            echo "<a class='badge bg-success' href='#' onclick=\"confirmDelete('hoadon','{$hoadon['MaHoaDon']}', 1)\"> Kích hoạt </a>";
                                                        }
                                                    ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- Phan trang 3 / 3 -->
                                    <?php
                                    echo '<nav aria-label="Page navigation" class="p-5 fs-3">';
                                    echo '<ul class="pagination justify-content-center green-pagination">';

                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1">&laquo; First</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">&laquo; First</a></li>';
                                    }

                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }

                                    if ($page < $totalPages) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($totalPages) . '">Last &raquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">Last &raquo;</a></li>';
                                    }

                                    echo '</ul>';
                                    echo '</nav>';
                                    ?>
                                    <!-- Phan trang 3 / 3 -->
                                </div>
                            </div>
                        </div>
                        <?php include 'footer.php'; ?>
                    </div>
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
    text: "Bạn có chắc chắn muốn thay đổi trạng thái của đánh giá này không?",
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
                    'Trạng thái đã được thay đổi.',
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
function updatePaymentStatus(hoadonId, newStatus) {
    // Gửi yêu cầu AJAX để cập nhật trạng thái thanh toán
    $.ajax({
        url: 'process-update-payment-status.php',
        type: 'POST',
        data: {
            MaHoaDon: hoadonId,
            TrangThaiThanhToan: newStatus
        },
        success: function(response) {
            // Hiển thị thông báo khi thành công
            if(response === 'success') {
                Swal.fire(
                    'Cập nhật thành công!',
                    'Trạng thái thanh toán đã được cập nhật.',
                    'success'
                );
            } else {
                // Nếu có lỗi xảy ra
                Swal.fire(
                    'Có lỗi xảy ra!',
                    'Không thể cập nhật trạng thái thanh toán.',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Lỗi kết nối!',
                'Không thể kết nối đến server.',
                'error'
            );
        }
    });
}

</script>

</html>
