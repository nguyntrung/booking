<?php
session_start();
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

$loc = isset($_GET['loc']) ? $_GET['loc'] : '0';
$locstr = $loc == '1' ? 'ORDER BY ph.DanhGia' : ($loc == '2' ? 'ORDER BY ph.enableflag' : 'ORDER BY ph.MaPhanHoi');

$loailoc = isset($_GET['loailoc']) ? $_GET['loailoc'] : 'xx';
$loailoc = ($loailoc == 'DESC') ? 'ASC' : ($loailoc == 'ASC' ? 'DESC' : 'ASC');

$stmt = $conn->prepare("SELECT 
    ph.MaPhanHoi,
    ph.NoiDung,
    ph.DanhGia,
    ph.Type,
    hk.TenHK AS TenHanhKhach,
    hk.SDT AS SDT,
    ph.enableflag
FROM phanhoidanhgia ph
LEFT JOIN hanhkhach hk ON ph.HanhKhach = hk.MaHK
$locstr $loailoc");

if ($stmt === false) {
    die('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
}

$stmt->execute();

if ($stmt->error) {
    die('Lỗi khi thực thi truy vấn: ' . $stmt->error);
}

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
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
                            <h5 class="card-header">Danh sách Phản hồi đánh giá</h5>
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã</th>
                                                <th>Đánh giá</th>
                                                <th>Nội dung</th>
                                                <th>Tên hành khách</th>
                                                <th>Trạng thái</th>
                                                <th>Loại</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($phanhoiList as $phanhoi): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($phanhoi['MaPhanHoi']); ?></td>
                                                    <td class="text-warning">
                                                        <?php 
                                                        $sao = htmlspecialchars($phanhoi['DanhGia']);
                                                        for($i=0; $i<$sao; $i++){
                                                            echo '&starf; ';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($phanhoi['NoiDung']); ?></td>
                                                    <td><?php echo htmlspecialchars($phanhoi['TenHanhKhach']); ?> 
                                                        <a href="#" class="text-success" onclick="callHK('<?php echo htmlspecialchars($phanhoi['SDT']); ?>')">
                                                            <i class="ri-phone-fill"></i>
                                                        </a> 
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <?php 
                                                                if ($phanhoi['enableflag'] == 1) {
                                                                    echo "<a class='badge bg-danger' href='#' onclick=\"confirmDelete('phanhoidanhgia','{$phanhoi['MaPhanHoi']}', 0)\">Ẩn</a>";
                                                                } else {
                                                                    echo "<a class='badge bg-success' href='#' onclick=\"confirmDelete('phanhoidanhgia','{$phanhoi['MaPhanHoi']}', 1)\">Kích hoạt</a>";
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
