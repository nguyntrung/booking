<?php
session_start();

// Kết nối cơ sở dữ liệu
include '../../database/db.php';


function classifyFeedbackAndSaveToDB($noiDung, $maPhanHoi, $conn) {
    // URL API
    $url = 'https://wttx7hth-5000.asse.devtunnels.ms/classify_feedback';
    $payload = json_encode(['NoiDung' => $noiDung]);
    error_log("Feedback Content: " . $noiDung);
    // Ghi log payload
    error_log("========== START API CALL ==========");
    error_log("Payload: " . $payload);

    // Cấu hình cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Bật chế độ debug cho cURL

    // Gọi API
    $response = curl_exec($ch);
    $error = curl_errno($ch) ? curl_error($ch) : null;
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Kiểm tra lỗi cURL
    if ($error) {
        error_log("Error: " . $error);
        curl_close($ch);
        return false; // Kết thúc nếu gặp lỗi
    }

    // Kiểm tra mã trạng thái HTTP
    if ($http_status != 200) {
        error_log("API Error: HTTP Status Code " . $http_status);
        curl_close($ch);
        return false; // API không trả về thành công
    }

    // Ghi log phản hồi API
    error_log("Response: " . $response);

    // Đóng cURL
    curl_close($ch);

    // Giải mã phản hồi từ API
    $result = json_decode($response, true);

    // Kiểm tra lỗi giải mã JSON
    if ($result === null) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        return false;
    }

    // Lấy giá trị Sentiment từ phản hồi
    $sentiment = $result['Type'] ?? null;

    // Kiểm tra nếu không có giá trị Sentiment
    if (!$sentiment) {
        error_log("Error: No 'Type' in API response.");
        return false;
    }

    // Ghi log kết quả Sentiment
    error_log("Sentiment: " . $sentiment);
    error_log("========== END API CALL ==========");

    // Nếu có giá trị Sentiment, lưu vào cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE danhgiaphanhoi SET Type = ? WHERE MaPhanHoi = ?");

    // Kiểm tra lỗi SQL khi chuẩn bị câu lệnh
    if ($stmt === false) {
        error_log("SQL Error: " . $conn->error);
        return false;
    }

    // Gắn giá trị vào câu lệnh SQL
    $stmt->bind_param("si", $sentiment, $maPhanHoi); // 's' cho string, 'i' cho integer

    // Kiểm tra kết nối cơ sở dữ liệu
    if (!$conn->ping()) {
        error_log("Connection to DB failed: " . $conn->error);
        return false;
    }

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        error_log("Updated MaPhanHoi $maPhanHoi with Type: $sentiment");
        error_log("Affected rows: " . $stmt->affected_rows); // Kiểm tra số lượng bản ghi bị thay đổi
        $stmt->close();
        return true;
    } else {
        error_log("SQL Execute Error: " . $stmt->error); // Thêm lỗi khi thực thi câu lệnh
        $stmt->close();
        return false;
    }
}

// Phan trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)) {
    die("Lỗi: Tham số 'page' không hợp lệ.");
}
$limit = 8; // Số sản phẩm mỗi trang
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
        ph.Type,
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


$sql_positive = "SELECT COUNT(*) AS positive_count FROM phanhoidanhgia WHERE Type = 'Tích cực'";
$sql_negative = "SELECT COUNT(*) AS negative_count FROM phanhoidanhgia WHERE Type = 'Tiêu cực'";

$result_positive = $conn->query($sql_positive);
$result_negative = $conn->query($sql_negative);

$positive_count = $result_positive->fetch_assoc()['positive_count'] ?? 0;
$negative_count = $result_negative->fetch_assoc()['negative_count'] ?? 0;

$total_count = $positive_count + $negative_count;

// Tính tỷ lệ phần trăm
$positive_percentage = $total_count > 0 ? round(($positive_count / $total_count) * 100, 2) : 0;
$negative_percentage = $total_count > 0 ? round(($negative_count / $total_count) * 100, 2) : 0;
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
                                                <th>Loại</th>
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
                                                <td>
                                                        <?php 
                                                        $type = $phanhoi['Type'] ?? 'Chưa phân loại';
                                                        echo htmlspecialchars($type); 

                                                        if ($type === "Tích cực") {
                                                            echo  ' 😊'; 
                                                        } elseif ($type === "Tiêu cực") {
                                                            echo ' 😢'; 
                                                        }
                                                        ?>
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
                        <div class="container mt-5" style="width: 500px !important; height: 500px !important;">
                            <h5 class="text-center">Tỉ lệ phản hồi</h5>
                            <canvas id="feedbackPieChart" ></canvas>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
const ctx = document.getElementById('feedbackPieChart').getContext('2d');
const feedbackPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Tích cực', 'Tiêu cực'],
        datasets: [{
            data: [
                <?php echo $positive_count; ?>,
                <?php echo $negative_count; ?>
            ],
            backgroundColor: ['#28a745', '#dc3545'],
            borderColor: ['#ffffff', '#ffffff'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        size: 10
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.raw;
                        let total = <?php echo $total_count; ?>;
                        let percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

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