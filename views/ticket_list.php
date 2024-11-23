<?php
include '../database/db.php';

$diem_di = $_GET['diem_di'] ?? null;
$diem_den = $_GET['diem_den'] ?? null;
$ngay_di = $_GET['ngay_di'] ?? null;
$gio_di = $_GET['gio_di'] ?? 'all';
$cho_trong = $_GET['cho_trong'] ?? 0;

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

    if ($cho_trong > 0) {
        $query .= " AND CHUYENXE.SoChoTrong >= ?";
    }

    $stmt = $conn->prepare($query);
    
    if ($cho_trong > 0) {
        $stmt->bind_param('iisi', $diem_di, $diem_den, $ngay_di, $cho_trong);
    } else {
        $stmt->bind_param('iis', $diem_di, $diem_den, $ngay_di);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Lọc theo giờ nếu có chọn
            if ($gio_di != 'all') {
                $hour = date('H', strtotime($row['ThoiGianKhoiHanh']));
                $selected_hour = intval($gio_di);
                
                // Lọc theo khoảng giờ (ví dụ: 0-6, 6-12, 12-18, 18-24)
                switch($gio_di) {
                    case '0':
                        if ($hour >= 0 && $hour < 6) $tickets[] = $row;
                        break;
                    case '6':
                        if ($hour >= 6 && $hour < 12) $tickets[] = $row;
                        break;
                    case '12':
                        if ($hour >= 12 && $hour < 18) $tickets[] = $row;
                        break;
                    case '18':
                        if ($hour >= 18 && $hour < 24) $tickets[] = $row;
                        break;
                }
            } else {
                $tickets[] = $row;
            }
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
        flex-wrap: wrap;
    }

    .filter-section {
        display: flex;
        gap: 10px;
        align-items: center;
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
    }

    .filter-label {
        font-weight: bold;
        color: #666;
    }

    select.filter-select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
    }

    input.filter-input {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 80px;
    }

    .apply-filters {
        background: #ff4d07;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .apply-filters:hover {
        background: #e64400;
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
        <div class="filter-section">
            <span class="filter-label">Giờ khởi hành:</span>
            <select class="filter-select" id="gioFilter">
                <option value="all" <?= $gio_di == 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="0" <?= $gio_di == '0' ? 'selected' : '' ?>>00:00 - 06:00</option>
                <option value="6" <?= $gio_di == '6' ? 'selected' : '' ?>>06:00 - 12:00</option>
                <option value="12" <?= $gio_di == '12' ? 'selected' : '' ?>>12:00 - 18:00</option>
                <option value="18" <?= $gio_di == '18' ? 'selected' : '' ?>>18:00 - 24:00</option>
            </select>
        </div>
        <div class="filter-section">
            <span class="filter-label">Số ghế trống tối thiểu:</span>
            <input type="number" class="filter-input" id="choTrongFilter" value="<?= $cho_trong ?>" min="0">
        </div>
        <button class="apply-filters" id="applyFilters">Lọc</button>
    </div>

    <h4 class="text-center background-waring">DANH SÁCH VÉ</h4>

    <?php if (!empty($tickets)): ?>
        <?php foreach ($tickets as $ticket): ?>
            <!-- Existing ticket card HTML remains unchanged -->
            <div class="ticket-card">
                <div class="ticket-header">
                    <div class="time-info">
                        <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKhoiHanh'])) ?></span>
                        <?php
                        // Tính thời gian di chuyển
                        $start_time = new DateTime($ticket['ThoiGianKhoiHanh']);
                        $end_time = new DateTime($ticket['ThoiGianKetThuc']);
                        $interval = $start_time->diff($end_time);
                        
                        // Format duration string
                        $duration_str = '';
                        if ($interval->h >= 0) {
                            $duration_str .= $interval->h . ' giờ';
                        }
                        if ($interval->i > 0) {
                            $duration_str .= ($duration_str ? '' : '') . $interval->i . ' phút';
                        }
                        ?>
                        <span class="duration"><?= $duration_str ?></span>
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
        <p class="p-3 text-center bg-warning-subtle rounded">⚠️ Không tìm thấy chuyến xe nào.</p>
    <?php endif; ?>
</div>

<script>
document.getElementById('applyFilters').addEventListener('click', function() {
    const currentUrl = new URL(window.location.href);
    const gioFilter = document.getElementById('gioFilter').value;
    const choTrongFilter = document.getElementById('choTrongFilter').value;

    currentUrl.searchParams.set('gio_di', gioFilter);
    currentUrl.searchParams.set('cho_trong', choTrongFilter);

    window.location.href = currentUrl.toString();
});

document.querySelectorAll('.book-button').forEach(button => {
    button.addEventListener('click', function() {
        alert('Chọn chuyến xe thành công!');
    });
});
</script>
