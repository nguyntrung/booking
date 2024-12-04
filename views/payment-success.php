<?php
include '../database/db.php';

// Retrieve invoice number from GET parameter
$invoiceId = isset($_GET['invoice']) ? $_GET['invoice'] : null;

// Fetch invoice details from database
if ($invoiceId) {
    $stmt = $conn->prepare("
        SELECT 
            hoadon.*, 
            hanhkhach.TenHK,
            ve.TenTuyen, 
            ve.ThoiGianKhoiHanh AS GioKhoiHanh,
            GROUP_CONCAT(ve.MaCho ORDER BY ve.MaCho SEPARATOR ', ') AS MaCho
        FROM hoadon 
        JOIN hanhkhach ON hoadon.HanhKhach = hanhkhach.MaHK 
        JOIN ve ON hoadon.MaHoaDon = ve.HoaDon 
        WHERE hoadon.MaHoaDon = ?
        GROUP BY hoadon.MaHoaDon
    ");
    $stmt->bind_param("s", $invoiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thành Công - FUTA Bus Lines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/payment-success.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>

    <div class="container payment-success-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card success-card">
                    <div class="card-body text-center">
                        <div class="success-icon">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h2 class="card-title mt-3">Thanh Toán Thành Công</h2>
                        
                        <?php if ($booking): ?>
                            <div class="booking-details">
                                <div class="detail-section">
                                    <h4>Thông Tin Đặt Vé</h4>
                                    <div class="row">
                                        <div class="col-6 text-end">Mã Hóa Đơn:</div>
                                        <div class="col-6 text-start fw-bold"><?php echo htmlspecialchars($invoiceId); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 text-end">Hành Khách:</div>
                                        <div class="col-6 text-start"><?php echo htmlspecialchars($booking['TenHK']); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 text-end">Tuyến Xe:</div>
                                        <div class="col-6 text-start"><?php echo htmlspecialchars($booking['TenTuyen']); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 text-end">Ngày Khởi Hành:</div>
                                        <!-- <div class="col-6 text-start"><?php echo htmlspecialchars($booking['GioKhoiHanh']); ?></div> -->
                                        <?php
                                            $gioKhoiHanh = new DateTime($booking['GioKhoiHanh']);
                                            echo htmlspecialchars($gioKhoiHanh->format('H:i d/m/Y')); 
                                        ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 text-end">Số Ghế:</div>
                                        <div class="col-6 text-start"><?php echo htmlspecialchars($booking['MaCho']); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 text-end">Tổng Tiền:</div>
                                        <div class="col-6 text-start fw-bold text-success"><?php echo number_format($booking['TongTien'], 0, ',', '.'); ?>đ</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-danger">Không tìm thấy thông tin hóa đơn.</p>
                        <?php endif; ?>

                        <div class="actions mt-4">
                            <a href="../views/my-bookings.php" class="btn btn-primary me-2">
                                <i class="fas fa-ticket-alt"></i> Xem Vé Của Tôi
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Về Trang Chủ
                            </a>
                        </div>

                        <div class="additional-info mt-4">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Vé xe điện tử đã được gửi đến email của bạn. 
                                Vui lòng kiểm tra và lưu giữ để sử dụng.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="print-section text-center mt-3">
                    <a href="generate_pdf.php?invoice=<?php echo $invoiceId; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-print"></i> In Hóa Đơn
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php @include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Optional: Client-side validation or additional interactions
        document.addEventListener('DOMContentLoaded', function() {
            // You can add additional JavaScript here if needed
        });
    </script>
</body>
</html>