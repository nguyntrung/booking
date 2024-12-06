<?php
session_start();
include '../database/db.php';

// if (!isset($_SESSION['MaHK'])) {
//     header("Location: ../views/login.php");
//     exit();
// }

// $userId = $_SESSION['MaHK'];
if(isset($_GET["kq"])){
    $kq=$_GET["kq"];
    if($kq==1){
        $mess="Đổi vé thành công";
    } else {
        $mess="Đổi vé thất bại";
    }
}
$userId = 60;

$bookingsQuery = "
    select v.MaVeXe, t.TenTuyenXe, c.ThoiGianKhoiHanh, v.MaCho , c.GiaTien
    from ve v, hoadon hd, chuyenxe c, tuyenxe t 
    where v.ChuyenXe=c.MaChuyenXe and c.Tuyen=t.MaTuyenXe 
    and v.HoaDon=hd.MaHoaDon and hd.HanhKhach=? and v.enableflag=0
    ORDER BY c.ThoiGianKhoiHanh DESC
";

$stmt = $conn->prepare($bookingsQuery);
$stmt->bind_param("i", $userId);
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
                <h2 class="my-4 text-center title">
                    <i class="fas fa-ticket-alt me-2"></i>Vé Của Tôi
                </h2>
                <?php
                if(isset($mess)){

                ?>
                    <div id="successMessage" class="alert <?php 
                    if(isset($kq)){
                       echo $kq==1?'alert-success':'alert-danger'; 
                    }
                     
                     ?>">
                        <?php echo $mess; ?>
                    </div>
                    <?php } ?>

                <?php if ($bookingsResult->num_rows > 0): ?>
                    <div class="bookings-list">
                        <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                            <?php
                                $timezone = new DateTimeZone('Asia/Ho_Chi_Minh');
                                $currentTime = new DateTime('now', $timezone);
                                $departureTime = new DateTime($booking['ThoiGianKhoiHanh'], $timezone); 
                                if ($departureTime < $currentTime) {
                                    $hethan=1;
                                }else {
                                    $hethan=0;
                                }                            
                                ?>
                            <div class="card booking-card mb-3 card_ve <?php echo $hethan==1?'hethan':'khonghethan'; ?>">
                            <?php echo $hethan==1?'<p class="kyhieuhethan"><i class="fa-solid fa-circle-xmark"></i></p>':''; ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="card-title">
                                                <?php echo htmlspecialchars($booking['TenTuyenXe']); ?>
                                            </h5>
                                            <div class="booking-details">
                                                <p>
                                                    <strong>Mã Vé:</strong> 
                                                    <?php echo htmlspecialchars($booking['MaVeXe']); ?>
                                                </p>
                                                <p>
                                                    <strong>Ngày Khởi Hành:</strong> 
                                                    <?php  echo $departureTime->format('H:i d/m/Y');  ?>
                                                </p>
                                                <p>
                                                    <strong>Số Ghế:</strong> 
                                                    <?php echo htmlspecialchars($booking['MaCho']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="booking-price mb-2">
                                                <strong><?php echo number_format($booking['GiaTien'], 0, ',', '.'); ?>đ</strong>
                                            </div>
                                            <div class="booking-actions">
                                                <?php echo $hethan==1?'':'<a href="./doive.php?id='.$booking["MaVeXe"] .'" 
                                                   class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fa-solid fa-rotate-right"></i> Đổi vé
                                                </a>'; ?>
                                                
                                                
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
    <script>
    // Tìm phần tử thông báo
    const successMessage = document.getElementById('successMessage');
    
    // Nếu thông báo tồn tại, đặt thời gian để ẩn sau 5 giây
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none'; // Ẩn thông báo
        }, 3000); // 5000ms = 5 giây
    }
</script>

</body>
</html>