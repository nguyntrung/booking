<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNV'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Lấy danh sách nhân viên từ cơ sở dữ liệu, kết hợp với bảng loainhanvien
$stmt = $conn->prepare("
    SELECT 
        nv.MaNV, 
        nv.TenNV, 
        nv.NamSinh, 
        nv.SDT, 
        nv.MatKhau, 
        nv.DiaChi, 
        nv.BangCap, 
        ln.TenLoai,
        nv.enableflag
    FROM nhanvien nv
    LEFT JOIN loainhanvien ln ON nv.LoaiNV = ln.MaLoai
    ORDER BY nv.MaNV ASC
");
$stmt->execute();
$result = $stmt->get_result();
$nhanVienList = $result->fetch_all(MYSQLI_ASSOC); // Trả về mảng kết hợp
?>
<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý nhân viên</title>
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
            <!-- Layout container -->
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <h5 class="card-header">Danh sách nhân viên</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã nhân viên</th>
                                                <th>Tên nhân viên</th>
                                                <th>Năm sinh</th>
                                                <th>Số điện thoại</th>
                                                <th>Bằng cấp</th>
                                                <th>Địa chỉ</th>
                                                <th>Chức vụ</th>
                                                <th>Trạng thái</th> <!-- Cột trạng thái -->
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($nhanVienList as $nhanVien): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($nhanVien['MaNV']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['TenNV']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['NamSinh']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['SDT']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['BangCap']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['DiaChi']); ?></td>
                                                <td><?php echo htmlspecialchars($nhanVien['TenLoai']); ?></td>
                                                <td>
                                                    <?php 
                                                        // Kiểm tra trạng thái enableflag và hiển thị màu sắc tương ứng
                                                        if ($nhanVien['enableflag'] == 1) {
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
                                                                href="add_update_user.php?id=<?php echo $nhanVien['MaNV']; ?>"><i
                                                                    class="ri-pencil-line me-1"></i> Chỉnh sửa</a>
                                                            <a class="dropdown-item" href="#" onclick="confirmDelete('nhanvien','<?php echo $nhanVien['MaNV']; ?>', <?php echo $nhanVien['enableflag']; ?>)">
                                                                <i class="ri-delete-bin-6-line me-1"></i> Xóa
                                                            </a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <a href="add_update_user.php" class="btn btn-success mt-2">Thêm nhân viên</a>
                                </div>
                            </div>
                        </div>
                        <?php include 'footer.php'; ?>
                    </div>
                    <!-- Content wrapper -->
                </div>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <?php include 'other.php'; ?>
</body>
<script>
    function confirmDelete(table, id, isEnable) {

        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của nhân viên này không?')) {
            // Gọi hàm để cập nhật trạng thái
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Gửi dữ liệu đến server
            xhr.send(`type=${table}&id=${id}&enableflag=${1-isEnable}`);

            // Xử lý phản hồi từ server
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("Trạng thái nhân viên đã được thay đổi thành công!");
                    $('.card-body').load(' .card-body>.table-responsive');
                } else {
                    alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
                }
            };
            }
    }
</script>

</html>
