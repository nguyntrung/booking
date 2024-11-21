<?php
include '../database/db.php';

$diem_di = $_GET['diem_di'] ?? null;
$diem_den = $_GET['diem_den'] ?? null;
$ngay_di = $_GET['ngay_di'] ?? null;

$tickets = [];

if ($diem_di && $diem_den && $ngay_di) {
    $query = "
        SELECT 
            CHUYENXE.*, TUYENXE.TenTuyenXe, XE.BienSoXe, LOAIXE.TenLoaiXe, LOAIXE.SucChua,
            BenDi.TenBenXe AS TenBenDi, BenDen.TenBenXe AS TenBenDen
        FROM CHUYENXE
        INNER JOIN TUYENXE ON CHUYENXE.Tuyen = TUYENXE.MaTuyenXe
        INNER JOIN XE ON CHUYENXE.Xe = XE.MaXe
        INNER JOIN LOAIXE ON XE.LoaiXe = LOAIXE.MaLoaiXe
        INNER JOIN BENXE AS BenDi ON TUYENXE.BenDi = BenDi.MaBenXe
        INNER JOIN BENXE AS BenDen ON TUYENXE.BenDen = BenDen.MaBenXe
        WHERE TUYENXE.BenDi = ? AND TUYENXE.BenDen = ? AND DATE(CHUYENXE.ThoiGianKhoiHanh) = ?
    ";


    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $diem_di, $diem_den, $ngay_di);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
    }
}
?>

<style>
    .ticket-list {
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .ticket-filters {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 20px;
        color: #666;
        cursor: pointer;
    }

    .filter-badge i {
        margin-right: 5px;
    }

    .ticket-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .time-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .time {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .duration {
        color: #666;
        font-size: 14px;
        padding: 0 15px;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
    }

    .location {
        margin: 10px 0;
        color: #333;
    }

    .ticket-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .bus-type {
        display: flex;
        gap: 15px;
        color: #666;
    }

    .price {
        font-size: 20px;
        font-weight: bold;
        color: #ff4d07;
    }

    .book-button {
        background: #ff4d07;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .book-button:hover {
        background: #e64400;
    }

    .warning {
        color: #ff4d07;
        font-size: 14px;
        margin-top: 10px;
    }

    .route-info {
        color: #666;
        font-size: 14px;
        font-style: italic;
    }
</style>

<div class="ticket-list">
    <div class="ticket-filters">
        <div class="filter-badge">
            <i class="fas fa-tag"></i>
            Giá rẻ bất ngờ
        </div>
        <div class="filter-badge">
            <i class="fas fa-clock"></i>
            Giờ khởi hành
        </div>
        <div class="filter-badge">
            <i class="fas fa-chair"></i>
            Ghế trống
        </div>
    </div>

    <?php if (!empty($tickets)): ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket-card">
                <div class="ticket-header">
                    <div class="time-info">
                        <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKhoiHanh'])) ?></span>
                        <span class="duration">12 giờ</span>
                        <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKetThuc'])) ?></span>
                    </div>
                    <div class="bus-type">
                        <span><?= htmlspecialchars($ticket['TenLoaiXe']) ?></span>
                        <span><?= htmlspecialchars($ticket['SoChoTrong']) ?> chỗ trống</span>
                    </div>
                </div>
                <div class="location">
                    <strong><?= htmlspecialchars($ticket['TenBenDi']) ?></strong>
                </div>
                <div class="location">
                    <strong><?= htmlspecialchars($ticket['TenBenDen']) ?></strong>
                </div>
                <div class="ticket-details">
                    <div class="price"><?= number_format($ticket['GiaTien'], 0, ',', '.') ?>đ</div>
                    <button class="book-button">Chọn chuyến</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy chuyến xe nào.</p>
    <?php endif; ?>
</div>

<script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
<script>
    document.querySelectorAll('.filter-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });

    document.querySelectorAll('.book-button').forEach(button => {
        button.addEventListener('click', function() {
            alert('Chọn chuyến xe thành công!');
        });
    });
</script>
