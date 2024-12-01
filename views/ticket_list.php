<!-- ticket_list.php -->

<?php
   include '../database/db.php';
   
   $diem_di = $_GET['diem_di'] ?? null;
   $diem_den = $_GET['diem_den'] ?? null;
   $ngay_di = $_GET['ngay_di'] ?? null;
   $ngay_ve = $_GET['ngay_ve'] ?? null;
   $khu_hoi = isset($_GET['khu_hoi']) && $_GET['khu_hoi'] == '1';
   $gio_di = $_GET['gio_di'] ?? 'all';
   $cho_trong = $_GET['cho_trong'] ?? 0;
   
   function getTickets($diem_di, $diem_den, $ngay, $gio_di, $cho_trong) {
       global $conn;
       $tickets = [];
       
       if ($diem_di && $diem_den && $ngay) {
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
               $stmt->bind_param('iisi', $diem_di, $diem_den, $ngay, $cho_trong);
           } else {
               $stmt->bind_param('iis', $diem_di, $diem_den, $ngay);
           }
           
           $stmt->execute();
           $result = $stmt->get_result();
   
           if ($result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                   if ($gio_di != 'all') {
                       $hour = date('H', strtotime($row['ThoiGianKhoiHanh']));
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
       return $tickets;
   }
   
   $outbound_tickets = getTickets($diem_di, $diem_den, $ngay_di, $gio_di, $cho_trong);
   $return_tickets = $khu_hoi ? getTickets($diem_den, $diem_di, $ngay_ve, $gio_di, $cho_trong) : [];
   ?>

<div class="container-ticket">
   <aside class="filter-sidebar">
      <div class="filter-header">
         <p class="fw-bold m-0">BỘ LỌC TÌM KIẾM</p>
         <a href="#" class="filter-clear">
         <i class="fas fa-trash"></i>
         Bỏ lọc
         </a>
      </div>
      <div class="filter-section">
         <h3>Giờ đi</h3>
         <div class="filter-options">
            <div class="filter-checkbox">
               <input type="checkbox" id="time1" value="0" <?= $gio_di == '0' ? 'checked' : '' ?>>
               <label for="time1">Sáng sớm 00:00 - 06:00</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="time2" value="6" <?= $gio_di == '6' ? 'checked' : '' ?>>
               <label for="time2">Buổi sáng 06:00 - 12:00</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="time3" value="12" <?= $gio_di == '12' ? 'checked' : '' ?>>
               <label for="time3">Buổi chiều 12:00 - 18:00</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="time4" value="18" <?= $gio_di == '18' ? 'checked' : '' ?>>
               <label for="time4">Buổi tối 18:00 - 24:00</label>
            </div>
         </div>
      </div>
      <div class="filter-section">
         <h3>Loại xe</h3>
         <div class="filter-options">
            <div class="filter-checkbox">
               <input type="checkbox" id="type1">
               <label for="type1">Ghế</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="type2">
               <label for="type2">Giường</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="type3">
               <label for="type3">Limousine</label>
            </div>
         </div>
      </div>
      <div class="filter-section">
         <h3>Hạng ghế</h3>
         <div class="filter-options">
            <div class="filter-checkbox">
               <input type="checkbox" id="class1">
               <label for="class1">Hàng đầu</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="class2">
               <label for="class2">Hàng giữa</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="class3">
               <label for="class3">Hàng cuối</label>
            </div>
         </div>
      </div>
      <div class="filter-section">
         <h3>Tầng</h3>
         <div class="filter-options">
            <div class="filter-checkbox">
               <input type="checkbox" id="floor1">
               <label for="floor1">Tầng trên</label>
            </div>
            <div class="filter-checkbox">
               <input type="checkbox" id="floor2">
               <label for="floor2">Tầng dưới</label>
            </div>
         </div>
      </div>
   </aside>
   <main class="main-content">
      <div class="route-header m-0">
        <?php foreach ($outbound_tickets as $ticket): ?>
            <p class="fw-bold"><?= htmlspecialchars($ticket['TenTuyenXe']) ?> (<?= count($outbound_tickets) ?>)</p>
        <?php endforeach; ?>
        <!-- <span class="trip-count">(<?= count($outbound_tickets) ?>)</span> -->
      </div>
      <div class="quick-filters">
         <div class="quick-filter">
            <i class="fas fa-tag"></i>
            Giá rẻ bất ngờ
         </div>
         <div class="quick-filter">
            <i class="far fa-clock"></i>
            Giờ khởi hành
         </div>
         <div class="quick-filter">
            <i class="fas fa-chair"></i>
            Ghế trống
         </div>
      </div>
      <?php if ($khu_hoi): ?>
      <div class="trip-tabs">
         <div class="trip-tab active" data-tab="outbound">
            CHUYẾN ĐI - <?= date('D, d/m', strtotime($ngay_di)) ?>
         </div>
         <div class="trip-tab" data-tab="return">
            CHUYẾN VỀ - <?= date('D, d/m', strtotime($ngay_ve)) ?>
         </div>
      </div>
      <?php endif; ?>
      <div class="ticket-list active" id="outbound-tickets">
         <?php if (!empty($outbound_tickets)): ?>
         <?php foreach ($outbound_tickets as $ticket): ?>
         <div class="ticket-card">
            <div class="ticket-header">
               <div class="time-info">
                  <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKhoiHanh'])) ?></span>
                  <?php
                     $start_time = new DateTime($ticket['ThoiGianKhoiHanh']);
                     $end_time = new DateTime($ticket['ThoiGianKetThuc']);
                     $interval = $start_time->diff($end_time);
                     $duration_str = sprintf(
                         '%d giờ',
                         $interval->h + ($interval->days * 24)
                     );
                     if ($interval->i > 0) {
                         $duration_str .= sprintf(' %d phút', $interval->i);
                     }
                     ?>
                  <span class="duration"><?= $duration_str ?></span>
                  <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKetThuc'])) ?></span>
               </div>
               <div class="bus-info">
                  <span><?= htmlspecialchars($ticket['TenLoaiXe']) ?></span>
                  <span><?= htmlspecialchars($ticket['SoChoTrong']) ?> chỗ trống</span>
               </div>
            </div>
            <div class="station-info">
               <div class="station">
                  <strong><?= htmlspecialchars($ticket['TenBenDi']) ?></strong>
               </div>
               <div class="station">
                  <strong><?= htmlspecialchars($ticket['TenBenDen']) ?></strong>
               </div>
            </div>
            <div class="ticket-footer">
               <div class="price"><?= number_format($ticket['GiaTien'], 0, ',', '.') ?>đ</div>
               <!-- <button class="book-button" data-trip="outbound" data-ticket-id="<?= $ticket['MaChuyenXe'] ?>">
               Chọn chuyến
               </button> -->
               <a href="choose_seat.php?MaChuyenXe=<?= $ticket['MaChuyenXe'] ?>" 
                  class="book-button text-decoration-none" data-trip="outbound">Chọn ghế
               </a>
            </div>
         </div>
         <?php endforeach; ?>
         <?php else: ?>
         <div class="no-results-container">
            <img class="no-results" src="https://futabus.vn/images/empty_list.svg" alt="No results">
            <p class="no-results">Không có kết quả được tìm thấy.</p>
         </div>
         <?php endif; ?>
      </div>
      <?php if ($khu_hoi): ?>
      <div class="ticket-list" id="return-tickets">
         <?php if (!empty($return_tickets)): ?>
         <?php foreach ($return_tickets as $ticket): ?>
         <div class="ticket-card">
            <div class="ticket-header">
               <div class="time-info">
                  <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKhoiHanh'])) ?></span>
                  <?php
                     $start_time = new DateTime($ticket['ThoiGianKhoiHanh']);
                     $end_time = new DateTime($ticket['ThoiGianKetThuc']);
                     $interval = $start_time->diff($end_time);
                     $duration_str = sprintf(
                         '%d giờ',
                         $interval->h + ($interval->days * 24)
                     );
                     if ($interval->i > 0) {
                         $duration_str .= sprintf(' %d phút', $interval->i);
                     }
                     ?>
                  <span class="duration"><?= $duration_str ?></span>
                  <span class="time"><?= date('H:i', strtotime($ticket['ThoiGianKetThuc'])) ?></span>
               </div>
               <div class="bus-info">
                  <span><?= htmlspecialchars($ticket['TenLoaiXe']) ?></span>
                  <span><?= htmlspecialchars($ticket['SoChoTrong']) ?> chỗ trống</span>
               </div>
            </div>
            <div class="station-info">
               <div class="station">
                  <strong><?= htmlspecialchars($ticket['TenBenDi']) ?></strong>
               </div>
               <div class="station">
                  <strong><?= htmlspecialchars($ticket['TenBenDen']) ?></strong>
               </div>
            </div>
            <div class="ticket-footer">
               <div class="price"><?= number_format($ticket['GiaTien'], 0, ',', '.') ?>đ</div>
               <!-- <button class="book-button" data-trip="return" data-ticket-id="<?= $ticket['MaChuyenXe'] ?>">
               Chọn chuyến
               </button> -->
               <a href="choose_seat.php?MaChuyenXe=<?= $ticket['MaChuyenXe'] ?>" 
                  class="book-button text-decoration-none" data-trip="outbound">Chọn ghế
               </a>
            </div>
         </div>
         <?php endforeach; ?>
         <?php else: ?>
         <p class="no-results">⚠️ Không tìm thấy chuyến xe nào.</p>
         <?php endif; ?>
      </div>
      <?php endif; ?>
   </main>
</div>
<script>
   // Handle filter checkboxes
   document.querySelectorAll('.filter-checkbox input').forEach(checkbox => {
       checkbox.addEventListener('change', function() {
           applyFilters();
       });
   });
   
   // Handle trip tabs for round-trip tickets
   document.querySelectorAll('.trip-tab').forEach(tab => {
       tab.addEventListener('click', function() {
           // Remove active class from all tabs and ticket lists
           document.querySelectorAll('.trip-tab').forEach(t => t.classList.remove('active'));
           document.querySelectorAll('.ticket-list').forEach(l => l.classList.remove('active'));
           
           // Add active class to clicked tab and corresponding ticket list
           this.classList.add('active');
           document.getElementById(this.dataset.tab + '-tickets').classList.add('active');
       });
   });
   
   function applyFilters() {
       const currentUrl = new URL(window.location.href);
       
       // Get selected time filters
       const selectedTimes = [...document.querySelectorAll('input[type="checkbox"]:checked')]
           .map(cb => cb.value)
           .filter(Boolean);
       
       if (selectedTimes.length > 0) {
           currentUrl.searchParams.set('gio_di', selectedTimes.join(','));
       } else {
           currentUrl.searchParams.set('gio_di', 'all');
       }
   
       window.location.href = currentUrl.toString();
   }
   
   // Handle ticket booking
   let selectedOutbound = null;
   let selectedReturn = null;
   
   document.querySelectorAll('.book-button').forEach(button => {
       button.addEventListener('click', function() {
           const tripType = this.dataset.trip;
           const ticketId = this.dataset.ticketId;
   
           if (tripType === 'outbound') {
               selectedOutbound = ticketId;
               if (!<?= $khu_hoi ? 'true' : 'false' ?> || selectedReturn) {
                   proceedWithBooking();
               }
           } else {
               selectedReturn = ticketId;
               if (selectedOutbound) {
                   proceedWithBooking();
               }
           }
       });
   });

   function proceedWithBooking() {
      if (<?= $khu_hoi ? 'true' : 'false' ?>) {
         if (!selectedOutbound || !selectedReturn) {
               alert('Vui lòng chọn cả chuyến đi và chuyến về');
               return;
         }
         window.location.href = `choose_seat.php?outbound=${selectedOutbound}&return=${selectedReturn}`;
      } else {
         window.location.href = `choose_seat.php?trip=${selectedOutbound}`;
      }
   }
   
   // function proceedWithBooking() {
   //     if (<?= $khu_hoi ? 'true' : 'false' ?>) {
   //         if (!selectedOutbound || !selectedReturn) {
   //             alert('Vui lòng chọn cả chuyến đi và chuyến về');
   //             return;
   //         }
   //         window.location.href = `booking.php?outbound=${selectedOutbound}&return=${selectedReturn}`;
   //     } else {
   //         window.location.href = `booking.php?outbound=${selectedOutbound}`;
   //     }
   // }
</script>