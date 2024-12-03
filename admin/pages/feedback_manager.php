<?php
session_start();

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Phan trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 4; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) AS total FROM phanhoidanhgia";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$totalRows = $row_count['total'];  // Lấy tổng số dòng

$totalPages = ceil($totalRows / $limit);

$loc = isset($_GET['loc']) ? $_GET['loc'] : '0';
if($loc=='1'){
    $locstr='ORDER BY ph.DanhGia';
} else if($loc=='2'){
    $locstr='ORDER BY ph.enableflag';
} else {
    $locstr='ORDER BY ph.MaPhanHoi';
}
$loailoc = isset($_GET['loailoc']) ? $_GET['loailoc'] : 'xx';
$change = isset($_GET["change"])? $_GET["change"] : 1;
if($change==0){
    if($loailoc!='DESC' && $loailoc!='ASC'){
        $loailoc='ASC';
    }
} else {
    if($loailoc=='DESC'){
        $loailoc='ASC';
    } else if($loailoc=='ASC'){
        $loailoc='DESC';
    } else {
        $loailoc='ASC';
    }
}


$stmt = $conn->prepare("
    SELECT 
        ph.MaPhanHoi,
        ph.NoiDung,
        ph.DanhGia,
        hk.TenHK AS TenHanhKhach,
        hk.SDT AS SDT,
        ph.enableflag
    FROM phanhoidanhgia ph
    LEFT JOIN hanhkhach hk ON ph.HanhKhach = hk.MaHK
    $locstr $loailoc 
    LIMIT ? OFFSET ?
");

// Kiểm tra lỗi khi chuẩn bị câu lệnh SQL
if ($stmt === false) {
    die('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
}

$stmt->bind_param("ii", $limit, $offset);

// Thực thi truy vấn
$stmt->execute();

// Kiểm tra lỗi khi thực thi truy vấn
if ($stmt->error) {
    die('Lỗi khi thực thi truy vấn: ' . $stmt->error);
}

// Lấy kết quả truy vấn
$result = $stmt->get_result();
$phanhoiList = $result->fetch_all(MYSQLI_ASSOC);
?>


<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>QL Phản hồi đánh giá</title>
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
                            <h5 class="card-header">Danh sách Phản hồi đánh giá</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                <table class="table table-bordered" style="">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <form method="get" action="" class="d-flex justify-content-between">
                                                        <span>Mã</span>
                                                        <input type="text" class="d-none" name="loc" value="0">
                                                        <input type="text" class="d-none" name="loailoc" value="<?php echo $loailoc ?>">
                                                        <button class="btn badge <?php echo $loc=='0' ? 'bg-primary' : 'bg-secondary'; ?>"><i class="ri-expand-up-down-fill"></i></button>
                                                    </form>
                                                </th>
                                                <th>
                                                    <form method="get" action="" class="d-flex justify-content-between">
                                                        <span>Đánh giá </span>
                                                        <input type="text" class="d-none" name="loc" value="1">
                                                        <input type="text" class="d-none" name="loailoc" value="<?php echo $loailoc ?>">
                                                        <button class="btn badge <?php echo $loc=='1' ? 'bg-primary' : 'bg-secondary'; ?>"><i class="ri-expand-up-down-fill"></i></button>
                                                    </form>
                                                </th>
                                                <th>Nội dung</th>
                                                <th>Tên hành khách</th>
                                                <th>
                                                    <form method="get" action="" class="d-flex justify-content-between">
                                                        <span>Trạng thái </span>
                                                        <input type="text" class="d-none" name="loc" value="2">
                                                        <input type="text" class="d-none" name="loailoc" value="<?php echo $loailoc ?>">
                                                        <button class="btn badge <?php echo $loc=='2' ? 'bg-primary' : 'bg-secondary'; ?>"><i class="ri-expand-up-down-fill"></i></button>
                                                    </form>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($phanhoiList as $phanhoi): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($phanhoi['MaPhanHoi']); ?></td>
                                                <td class="text-warning"><?php 
                                                    $sao = htmlspecialchars($phanhoi['DanhGia']);
                                                    for($i=0; $i<$sao; $i++){
                                                        echo '&starf; ';
                                                    }
                                                ?></td>
                                                <td><?php echo htmlspecialchars($phanhoi['NoiDung']); ?></td>
                                                <td><?php echo htmlspecialchars($phanhoi['TenHanhKhach']); ?> <a href="#" class="text-success" onclick="callHK('<?php echo htmlspecialchars($phanhoi['SDT']); ?>')"><i class="ri-phone-fill"></i></a> </td> 
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                    <?php 
                                                        // Kiểm tra trạng thái enableflag và hiển thị màu sắc tương ứng
                                                        if ($phanhoi['enableflag'] == 1) {
                                                            echo "<a class='badge bg-danger' href='#' onclick=\"confirmDelete('phanhoidanhgia','{$phanhoi['MaPhanHoi']}', 0)\"> Ẩn </a>";
                                                        } else {
                                                            echo "<a class='badge bg-success' href='#' onclick=\"confirmDelete('phanhoidanhgia','{$phanhoi['MaPhanHoi']}', 1)\"> Kích hoạt </a>";
                                                        }
                                                    ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    // Hiển thị phân trang
                                    echo '<nav aria-label="Page navigation" class="p-5 fs-3">';
                                    echo '<ul class="pagination justify-content-center green-pagination">';

                                    // Nút Previous
                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0&page=1">&laquo; First</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0">&laquo; First</a></li>';
                                    }

                                    // Các nút số trang
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0&page=' . $i . '">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0&page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }

                                    // Nút Next
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0&page=' . ($totalPages) . '">Last &raquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><a class="page-link" href="?loc='.$loc.'&loailoc='.$loailoc.'&change=0">Last &raquo;</a></li>';
                                    }

                                    echo '</ul>';
                                    echo '</nav>';
                                    ?>
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

</html>