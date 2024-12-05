<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LacThan Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>

    <div class="container py-5">
        <div class="col-md-8" style="margin: 0 auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-center">KẾT QUẢ TRA CỨU</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Kiểm tra nếu dữ liệu POST được gửi
                    if ($_SERVER["REQUEST_METHOD"] === "POST") {
                        $SDT = trim($_POST['SDT'] ?? '');
                        $MaVeXe = trim($_POST['MaVeXe'] ?? '');

                        // Kiểm tra nhập thiếu dữ liệu
                        if (empty($SDT) || empty($MaVeXe)) {
                            echo "<div class='alert alert-danger text-center'>Vui lòng nhập đầy đủ thông tin!</div>";
                            echo "<div class='text-center'><a href='lookup.php' class='btn btn-primary'>Quay lại</a></div>";
                        } else {
                            // Xử lý truy vấn
                            $sql = "SELECT ve.MaVeXe, hanhkhach.SDT, ve.MaCho, ve.TenTuyen, ve.ThoiGianKhoiHanh, ve.ThoiGianKetThuc, hoadon.MaHoaDon
                                    FROM ve
                                    JOIN hoadon ON ve.HoaDon = hoadon.MaHoaDon
                                    JOIN hanhkhach ON hoadon.HanhKhach = hanhkhach.MaHK
                                    WHERE hanhkhach.SDT = ? AND ve.MaVeXe = ?;";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $SDT, $MaVeXe);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Kiểm tra kết quả
                            if ($result->num_rows > 0) {
                                $ticket = $result->fetch_assoc();
                                echo "<table class='table table-bordered'>";
                                echo "<tr><th>Mã vé</th><td>" . $ticket['MaVeXe'] . "</td></tr>";
                                echo "<tr><th>Tên tuyến</th><td>" . $ticket['TenTuyen'] . "</td></tr>";
                                echo "<tr><th>Thời gian khởi hành</th><td>" . $ticket['ThoiGianKhoiHanh'] . "</td></tr>";
                                echo "<tr><th>Thời gian kết thúc</th><td>" . $ticket['ThoiGianKetThuc'] . "</td></tr>";
                                echo "<tr><th>Mã chỗ</th><td>" . $ticket['MaCho'] . "</td></tr>";
                                echo "</table>";
                                echo "<div class='text-center'><a href='lookup.php' class='btn btn-success'>Tra cứu lại</a></div>";
                            } else {
                                echo "<div class='alert alert-danger text-center'>Thông tin vé của bạn không được tìm thấy.</div>";
                                echo "<div class='text-center'><a href='lookup.php' class='btn btn-primary'>Quay lại</a></div>";
                            }

                            $stmt->close();
                        }
                    } else {
                        echo "<div class='alert alert-danger text-center'>Yêu cầu không hợp lệ!</div>";
                        echo "<div class='text-center'><a href='lookup.php' class='btn btn-primary'>Quay lại</a></div>";
                    }

                    // Đóng kết nối cơ sở dữ liệu
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php @include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
