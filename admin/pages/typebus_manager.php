<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNguoiDung'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Lấy danh sách câu hỏi tự luận từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT MaLoaiXe, TenLoaiXe, SucChua, enableflag
                         FROM loaixe  
                         ORDER BY MaLoaiXe ASC");
$stmt->execute();
$result = $stmt->get_result();
$loaiXeList = $result->fetch_all(MYSQLI_ASSOC); // Trả về mảng kết hợp
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý loại xe</title>
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
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
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
                            <h5 class="card-header">Danh sách các loại xe</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã loại xe</th>
                                                <th>Tên loại</th>
                                                <th>Sức chứa</th>
                                                <th>Trạng thái</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($loaiXeList as $loaiXe): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($loaiXe['MaLoaiXe']); ?></td>
                                                <td><?php echo htmlspecialchars($loaiXe['TenLoaiXe']); ?></td>
                                                <td><?php echo htmlspecialchars($loaiXe['SucChua']); ?></td>
                                                <td>
                                                <div class="d-flex justify-content-center">
                                                    <?php 
                                                        // Kiểm tra trạng thái enableflag và hiển thị màu sắc tương ứng
                                                        if ($loaiXe['enableflag'] == 1) {
                                                            echo "<a class='badge bg-danger' href='#' onclick=\"confirmDelete('loaixe','{$loaiXe['MaLoaiXe']}', 0)\"> Ẩn </a>";
                                                        } else {
                                                            echo "<a class='badge bg-success' href='#' onclick=\"confirmDelete('loaixe','{$loaiXe['MaLoaiXe']}', 1)\"> Kích hoạt </a>";
                                                        }
                                                    ?>
                                                </div>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown">
                                                            <i class="ri-more-2-line"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item"
                                                                href="add_update_typebus.php?id=<?php echo $loaiXe['MaLoaiXe']; ?>"><i
                                                                    class="ri-pencil-line me-1"></i> Chỉnh sửa</a>
                                                            <a class="dropdown-item" href="#"
                                                                onclick="confirmDelete('<?php echo $loaiXe['MaLoaiXe']; ?>')"><i
                                                                    class="ri-delete-bin-6-line me-1"></i> Xóa</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <a href="add_update_typebus.php" class="btn btn-success mt-2">Thêm loại xe</a>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Modal Xác Nhận Xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác Nhận Xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa câu hỏi này?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                    </div>
                </div>
            </div>
        </div>

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

        function callHK(SDT){
            Swal.fire({
            title: "Gọi cho hành khách?",
            text: "Nói chuyện chuẩn mực, thái độ nghiêm túc,  đảm bảo tính uy tín của nhà xe.",
            icon: 'info',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            cancelButtonText: 'Hủy',
            confirmButtonText: '<a href="tel:'+SDT+'" class="text-white">Gọi: '+SDT+'</a>'
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
        </script>
</body>

</html>