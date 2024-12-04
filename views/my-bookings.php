<?php
session_start();
include '../database/db.php';

if (!isset($_SESSION['MaHK'])) {
    header("Location: ../views/login.php");
    exit();
}

$userId = $_SESSION['MaHK'];

$bookingsQuery = "
    SELECT 
        hoadon.MaHoaDon, 
        ve.TenTuyen, 
        ve.ThoiGianKhoiHanh, 
        ve.TrangThai,
        GROUP_CONCAT(ve.MaCho ORDER BY ve.MaCho SEPARATOR ', ') AS MaCho,
        hoadon.TongTien
    FROM hoadon 
    JOIN ve ON hoadon.MaHoaDon = ve.HoaDon
    WHERE hoadon.HanhKhach = ?
    GROUP BY hoadon.MaHoaDon
    ORDER BY ve.ThoiGianKhoiHanh DESC
";

$stmt = $conn->prepare($bookingsQuery);
$stmt->bind_param("s", $userId);
$stmt->execute();
$bookingsResult = $stmt->get_result();
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
    <link rel="stylesheet" href="../assets/css/my-bookings.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>

    <div class="container my-bookings-container">
        <div class="row">
            <div class="col-12">
                <h2 class="my-4 text-center">
                    <i class="fas fa-ticket-alt me-2"></i>Vé Của Tôi
                </h2>

                <?php if ($bookingsResult->num_rows > 0): ?>
                    <div class="bookings-list">
                        <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                            <div class="card booking-card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="card-title">
                                                <?php echo htmlspecialchars($booking['TenTuyen']); ?>
                                            </h5>
                                            <div class="booking-details">
                                                <p>
                                                    <strong>Mã Hóa Đơn:</strong> 
                                                    <?php echo htmlspecialchars($booking['MaHoaDon']); ?>
                                                </p>
                                                <p>
                                                    <strong>Ngày Khởi Hành:</strong> 
                                                    <?php 
                                                    $departureTime = new DateTime($booking['ThoiGianKhoiHanh']);
                                                    echo $departureTime->format('H:i d/m/Y'); 
                                                    ?>
                                                </p>
                                                <p>
                                                    <strong>Số Ghế:</strong> 
                                                    <?php echo htmlspecialchars($booking['MaCho']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="booking-price mb-2">
                                                <strong><?php echo number_format($booking['TongTien'], 0, ',', '.'); ?>đ</strong>
                                            </div>
                                            <div class="booking-actions">
                                                <a href="../payment/generate_pdf.php?invoice=<?php echo $booking['MaHoaDon']; ?>" 
                                                   class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-print"></i> In Vé
                                                </a>
                                                <?php if ($booking['TrangThai'] == 'upcoming'): ?>
                                                    <button class="btn btn-sm btn-danger cancel-booking" 
                                                            data-invoice-id="<?php echo $booking['MaHoaDon']; ?>">
                                                        <i class="fas fa-times-circle"></i> Hủy Vé
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Bạn chưa có vé đặt nào.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php @include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Add booking cancellation functionality
            const cancelButtons = document.querySelectorAll('.cancel-booking');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceId = this.getAttribute('data-invoice-id');
                    if (confirm('Bạn có chắc chắn muốn hủy vé này không?')) {
                        // AJAX call to cancel booking
                        fetch('../trips/cancel_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'invoice=' + invoiceId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Hủy vé thành công');
                                location.reload();
                            } else {
                                alert('Không thể hủy vé: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Đã xảy ra lỗi khi hủy vé');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>