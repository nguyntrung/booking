<?php 

include '../database/db.php';


?>

<div id="feedbackHK">
    <h1 class="text-center pb-3 fw-bold" style="color: #00843D;">FEEDBACK</h1>
    <div class="feedback-container">
        <?php
        // Truy vấn dữ liệu từ CSDL
        $sql = "SELECT ph.MaPhanHoi, ph.NoiDung, ph.DanhGia, hk.TenHK 
                FROM phanhoidanhgia ph
                INNER JOIN hanhkhach hk ON ph.HanhKhach = hk.MaHK
                WHERE ph.enableflag = 0";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $stars = str_repeat('&#9733;', $row['DanhGia']); // Tạo số sao tương ứng với điểm đánh giá
        ?>
            <div class="feedback-card">
                <div class="feedback-title"><?php echo htmlspecialchars($row['TenHK']); ?></div>
                <div class="feedback-text">"<?php echo htmlspecialchars($row['NoiDung']); ?>"</div>
                <div class="feedback-stars"><?php echo $stars; ?></div>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <p>Không có phản hồi nào</p>
        <?php endif; ?>
    </div>
</div>

