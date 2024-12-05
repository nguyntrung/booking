<?php
// Kết nối cơ sở dữ liệu
include '../../database/db.php';

$error_message = "";

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Kiểm tra nếu username là admin và password là 123456
    if ($sdt === "admin" && $password === "123456") {
        session_start();
        
        // Gán session cho admin
        $_SESSION['nhanvien'] = "admin";
        $_SESSION['ma_loai'] = 0;             // Loại ảo cho admin
        $_SESSION['TenNV'] = "Admin";        // Tên cố định là Admin
        $_SESSION['TenLoai'] = "Admin";      // Loại cố định là Admin
        
        header('Location: index.php');       // Chuyển hướng sau khi đăng nhập thành công
        exit;
    }

    // Truy vấn kiểm tra thông tin người dùng trong database
    $query = "SELECT * FROM nhanvien WHERE SDT = '$sdt'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $nhanvien = mysqli_fetch_assoc($result);
        
        // Kiểm tra mật khẩu
        if ($password === $nhanvien['MatKhau']) {
            session_start();
            
            // Lưu thông tin người dùng vào session
            $_SESSION['nhanvien'] = $nhanvien;               
            $_SESSION['ma_loai'] = $nhanvien['LoaiNV']; 
            $_SESSION['TenNV'] = $nhanvien['TenNV'];
            $_SESSION['MaNV'] = $nhanvien['MaNV'];
            
            $maLoai = $nhanvien['LoaiNV'];
            $queryLoai = "SELECT TenLoai FROM loainhanvien WHERE MaLoai = '$maLoai'";
            $resultLoai = mysqli_query($conn, $queryLoai);
            
            if ($resultLoai && mysqli_num_rows($resultLoai) > 0) {
                $loaiNV = mysqli_fetch_assoc($resultLoai);
                $_SESSION['TenLoai'] = $loaiNV['TenLoai']; // Lưu TenLoai vào session
            } else {
                $_SESSION['TenLoai'] = "Không xác định"; // Nếu không tìm thấy
            }

            header('Location: index.php'); // Chuyển hướng sau khi đăng nhập thành công
            exit;
        } else {
            $error_message = "Sai mật khẩu.";
        }
    } else {
        $error_message = "Số điện thoại không tồn tại.";
    }
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAC THAN Bus Lines - Chất lượng là danh dự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    
    <div class="container login">
        <div class="header m-0">
            <div class="brand">
                <h1>LAC THAN BUS</h1>
                <p>Cùng bạn trên mọi nẻo đường</p>
            </div>
            <div class="nav-tabs">
                <a href="#" class="nav-tab active">ĐĂNG NHẬP ADMIN</a>
            </div>
        </div>

        <div class="content">
            <div class="illustration">
                <img src="https://cdn.futabus.vn/futa-busline-cms-dev/TVC_00aa29ba5b/TVC_00aa29ba5b.svg" alt="Bus illustration">
            </div>

            <div class="form-container">
                <!-- Đăng nhập -->
                <form id="loginForm" method="POST" action="">
                    <!-- Số điện thoại -->
                    <div class="input-group">
                        <input type="text" name="sdt" id="sdt" placeholder="Nhập số điện thoại" required>
                        <div id="sdtFeedback" class="invalid-feedback">
                            Số điện thoại không hợp lệ
                        </div>
                    </div>
                    <!-- Mật khẩu -->
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="passwordFeedback" class="invalid-feedback">
                            Vui lòng nhập mật khẩu
                        </div>
                    </div>
                    <!-- Thông báo lỗi -->
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>
                    <!-- Nút đăng nhập -->
                    <button type="submit" name="login" class="submit-btn">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>

    <?php @include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
