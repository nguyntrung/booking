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
    <link rel="stylesheet" href="../assets/css/aboutus.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>
<div id="aboutus">
<div class="about-section text-center">
        <div class="container">
            <h1>Về chúng tôi</h1>
            <p>Chúng tôi là Nhóm 3, hiện đang thực hiện đồ án xây dựng một nền tảng hiện đại giúp khách hàng dễ dàng đặt vé xe khách. Mục tiêu của chúng tôi là mang đến sự tiện lợi, nhanh chóng và đáng tin cậy cho mọi hành trình.</p>
        </div>
    </div>

    <!-- Team Section -->
    <div class="team-section">
        <div class="container">
            <h2 class="text-center mb-4">Thành viên</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://bedental.vn/wp-content/uploads/2022/12/Anh-Avatar-Doremon-dep-ngau-cute.jpg" alt="Team Member">
                        <h5>Bùi Quốc Bảo</h5>
                        <p>Nhóm tưởng </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEi1epMGHjWVyFkAe5-DzPfZgcfqcKFEWksrr7i__TnLd_sEjRNhbDhFwtalRLkIycad-20Ee1_DDbhCPH34nGy87ZhfzXzYQiWaguft0WaZo8JkbByLmnYApqTyFXzUJmX-zzntb7K9cSqt/s280/11390004_973941572656403_472554857478716923_n.jpg" alt="Team Member">
                        <h5>Nguyễn Thành Trung</h5>
                        <p>Thành viên</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://img.tripi.vn/cdn-cgi/image/width=700,height=700/https://gcs.tripi.vn/public-tripi/tripi-feed/img/474064hdR/anh-xuka-dep_105625163.jpg" alt="Team Member">
                        <h5>Nguyễn Đoàn Anh Thư</h5>
                        <p>Thành viên</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://img.tripi.vn/cdn-cgi/image/width=700,height=700/https://gcs.tripi.vn/public-tripi/tripi-feed/img/474117rPD/nhung-hinh-anh-ve-nobita_105847092.jpg" alt="Team Member">
                        <h5>Phan Chí Tài</h5>
                        <p>Thành viên</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://iphoto.edu.vn/public/upload/2024/09/chai-ko-12.webp" alt="Team Member">
                        <h5>Mai Thanh Trúc</h5>
                        <p>Thành viên</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card p-3">
                        <img src="https://i.pinimg.com/736x/3e/e3/2f/3ee32fe352fb902de778d7727d2edbd9.jpg" alt="Team Member">
                        <h5>Lê Nguyễn Quang Minh</h5>
                        <p>Thành viên</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
