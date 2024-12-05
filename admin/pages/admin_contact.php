<?php
session_start();
include '../../database/db.php';

// Chuẩn bị câu truy vấn và thực thi
// Chuẩn bị câu truy vấn và thực thi
$sql = "SELECT * FROM lienhe";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Lấy kết quả và kiểm tra số lượng hàng trả về
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $lienhes = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $lienhes = [];
}


?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Quản lý liên hệ</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"rel="stylesheet" />
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
                            <h5 class="card-header">Danh sách liên hệ</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã liên hệ</th>
                                                <th>Họ và tên</th>
                                                <th>Email</th>
                                                <th>Tiêu đề</th>
                                                <th>Nội dung</th>
                                                <th>Ngày giờ</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($lienhes) > 0): ?>
                                                <?php foreach ($lienhes as $contact): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($contact['MaLienHe']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['HoTen']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['Email']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['TieuDe']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['NoiDung']); ?></td>
                                                        <td><?php echo date("d-m-Y H:i:s", strtotime($contact['NgayTao'])); ?></td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                            <?php 
                                                               
                                                                if ($contact['enableflag'] == 1) {
                                                                    echo "<a class='badge bg-danger' href='#' onclick=\"confirmDelete('lienhe','{$contact['MaLienHe']}', 0)\"> Ẩn </a>";
                                                                } else {
                                                                    echo "<a class='badge bg-success' href='#' onclick=\"confirmDelete('lienhe','{$contact['MaLienHe']}', 1)\"> Kích hoạt </a>";
                                                                }
                                                            ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Không có liên hệ nào</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
    </div>
    
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
</script>

</html>

