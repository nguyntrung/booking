<?php
session_start();
include '../../database/db.php';

// Khởi tạo biến
$maXe = '';
$loaiXe = '';
$bienSoXe = '';
$viTriXe = '';
$errorMessage = '';
$successMessage = '';

// Kiểm tra mã xe từ GET
if (isset($_GET['id'])) {
    $maXe = $_GET['id'];
    $stmt = $conn->prepare("SELECT x.MaXe, x.BienSoXe, x.ViTriXe, x.LoaiXe, l.TenLoaiXe, b.TenBenXe 
                            FROM xe x 
                            JOIN loaixe l ON x.LoaiXe = l.MaLoaiXe 
                            JOIN benxe b ON x.ViTriXe = b.MaBenXe 
                            WHERE x.MaXe = ?");
    $stmt->bind_param("i", $maXe);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $xe = $result->fetch_assoc();
        $bienSoXe = $xe['BienSoXe'];
        $viTriXe = $xe['ViTriXe'];
        $loaiXe = $xe['LoaiXe'];
    } else {
        $errorMessage = 'Không tìm thấy xe!';
    }
}

// Xử lý POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bienSoXe = $_POST['bienSoXe'] ?? '';
    $viTriXe = $_POST['viTriXe'] ?? '';
    $loaiXe = $_POST['loaiXe'] ?? '';

    if (empty($bienSoXe) || empty($viTriXe) || empty($loaiXe)) {
        $errorMessage = 'Vui lòng điền tất cả các trường.';
    } else {
        if ($maXe) {
            // Cập nhật
            $stmt = $conn->prepare("UPDATE xe SET BienSoXe = ?, ViTriXe = ?, LoaiXe = ? WHERE MaXe = ?");
            $stmt->bind_param("sssi", $bienSoXe, $viTriXe, $loaiXe, $maXe);
            if ($stmt->execute()) {
                $successMessage = 'Cập nhật xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật xe!';
            }
        } else {
            // Thêm mới
            $stmt = $conn->prepare("INSERT INTO xe (BienSoXe, ViTriXe, LoaiXe) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $bienSoXe, $viTriXe, $loaiXe);
            if ($stmt->execute()) {
                $successMessage = 'Thêm xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm xe!';
            }
        }
    }
}

// Lấy danh sách bến xe
$stmt = $conn->prepare("SELECT MaBenXe, TenBenXe FROM benxe");
$stmt->execute();
$result = $stmt->get_result();
$benXeList = $result->fetch_all(MYSQLI_ASSOC);

// Lấy danh sách loại xe
$stmt = $conn->prepare("SELECT MaLoaiXe, TenLoaiXe FROM loaixe");
$stmt->execute();
$result = $stmt->get_result();
$loaiXeList = $result->fetch_all(MYSQLI_ASSOC);
?>


<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý xe</title>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script src="../ckeditor/ckeditor5.js"></script>
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
                            <h5 class="card-header"><?php echo $maXe ? 'Chỉnh sửa xe' : 'Thêm xe'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="bienSoXe" class="form-label">Biển Số Xe</label>
                                        <input type="text" class="form-control" id="bienSoXe" name="bienSoXe"
                                            value="<?php echo htmlspecialchars($bienSoXe); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viTriXe" class="form-label">Vị Trí Xe</label>
                                        <select class="form-select" id="viTriXe" name="viTriXe" required>
                                            <?php foreach ($benXeList as $benxe): ?>
                                                <option value="<?php echo htmlspecialchars($benxe['MaBenXe']); ?>"
    <?php echo ($viTriXe == $benxe['MaBenXe']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($benxe['TenBenXe']); ?>
</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loaiXe" class="form-label">Loại Xe</label>
                                        <select class="form-select" id="loaiXe" name="loaiXe" required>
                                            <?php foreach ($loaiXeList as $loaixeL): ?>
                                                <option value="<?php echo htmlspecialchars($loaixeL['MaLoaiXe']); ?>"
    <?php echo ($loaiXe == $loaixeL['MaLoaiXe']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($loaixeL['TenLoaiXe']); ?>
</option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="btn btn-primary"><?php echo $maXe ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="bus_manager.php" class="btn btn-secondary">Quay lại</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include 'other.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#noiDungLyThuyet'))
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    document.querySelector('#noiDungLyThuyet').value =
                        data; // Cập nhật giá trị cho textarea
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const editorData = document.querySelector('#noiDungLyThuyet').value; // Lấy dữ liệu từ CKEditor
        // Đảm bảo dữ liệu từ CKEditor được thiết lập vào textarea
        if (editorData.trim() === '') {
            alert('Nội dung lý thuyết không được để trống.');
            return false; // Ngăn chặn gửi biểu mẫu
        }
    });
    </script>
</body>

</html>