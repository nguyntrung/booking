<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch trình các tuyến xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>

    <div class="container py-5">
        <div class="col-md-8" style="margin: 0 auto;">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">TÌM KIẾM LỊCH TRÌNH</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="schedule.php">
                        <div class="mb-3">
                            <label for="start-point" class="form-label">Nhập điểm đi</label>
                            <input type="text" class="form-control" id="start-point" name="BenDi" placeholder="Nhập điểm đi">
                        </div>
                        <div class="mb-3">
                            <label for="end-point" class="form-label">Nhập điểm đến</label>
                            <input type="text" class="form-control" id="end-point" name="BenDen" placeholder="Nhập điểm đến">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Tìm tuyến xe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Lấy thông tin từ form

    $BenDi = trim($_POST['BenDi'] ?? '');

    $BenDen = trim($_POST['BenDen'] ?? '');



    if (!empty($BenDi) && !empty($BenDen)) {

        // Truy vấn SQL để lấy dữ liệu các tuyến xe

        $sql = "SELECT 
                tuyenxe.TenTuyenXe, 
                loaixe.TenLoaiXe, 
                tuyenxe.KhoangCach, 
                TIMEDIFF(chuyenxe.ThoiGianKetThuc, chuyenxe.ThoiGianKhoiHanh) AS ThoiGianHanhTrinh, 
                chuyenxe.GiaTien
            FROM tuyenxe
            JOIN benxe AS benxe_di ON tuyenxe.BenDi = benxe_di.MaBenXe
            JOIN benxe AS benxe_den ON tuyenxe.BenDen = benxe_den.MaBenXe
            JOIN chuyenxe ON tuyenxe.MaTuyenXe = chuyenxe.Tuyen
            JOIN loaixe ON chuyenxe.Xe = loaixe.MaLoaiXe
            WHERE benxe_di.TenBenXe LIKE ? 
            AND benxe_den.TenBenXe LIKE ?
        ";

        $stmt = $conn->prepare($sql);

        // Thêm ký tự `%` để tìm kiếm gần đúng

        $BenDi = '%' . $BenDi . '%';

        $BenDen = '%' . $BenDen . '%';

        
        $stmt->bind_param("ss", $BenDi, $BenDen);

        $stmt->execute();

        $result = $stmt->get_result();



        if ($result->num_rows > 0) {

            echo "<div class='table-responsive mt-4'>";

            echo "<table class='table table-bordered'>";

            echo "<thead class='table-primary text-center'>";

            echo "<tr>

                    <th>Tuyến xe</th>

                    <th>Loại xe</th>

                    <th>Quãng đường (km)</th>

                    <th>Thời gian hành trình</th>

                    <th>Giá vé (VNĐ)</th>

                  </tr>";

            echo "</thead><tbody>";



            while ($row = $result->fetch_assoc()) {

                echo "<tr>";

                echo "<td>" . $row['TenTuyenXe'] . "</td>";

                echo "<td>" . $row['TenLoaiXe'] . "</td>";

                echo "<td class='text-center'>" . $row['KhoangCach'] . "</td>";

                echo "<td class='text-center'>" . $row['ThoiGianHanhTrinh'] . "</td>";

                echo "<td class='text-end'>" . number_format($row['GiaTien'], 0, ',', '.') . "</td>";

                echo "</tr>";

            }
            echo "</tbody></table></div>";

        } else {

            echo "<div class='alert alert-warning text-center mt-4'>Không tìm thấy lịch trình nào phù hợp.</div>";

        }

        $stmt->close();

    } else {"<div class='alert alert-danger text-center mt-4'>Vui lòng nhập đầy đủ thông tin điểm đi và điểm đến!</div>";

    }

}

?>
    </div>

    <?php @include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
