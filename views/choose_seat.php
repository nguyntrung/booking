<!-- choose_seat.php -->

<?php
   include '../database/db.php';

   // Nhận MaChuyenXe từ URL
   $MaChuyenXe = isset($_GET['MaChuyenXe']) ? intval($_GET['MaChuyenXe']) : die("Chưa chọn chuyến xe");
   
   // Truy vấn để lấy LoaiXe từ MaChuyenXe thông qua bảng ChuyenXe và Xe
   $query = "
       SELECT xe.LoaiXe 
       FROM chuyenxe 
       JOIN xe ON chuyenxe.Xe = xe.MaXe -- Kết nối bảng ChuyenXe với Xe dựa trên MaXe
       WHERE chuyenxe.MaChuyenXe = ?";  // Lọc theo MaChuyenXe được truyền vào
   
   $stmt = $conn->prepare($query); 
   $stmt->bind_param('i', $MaChuyenXe); // Liên kết tham số an toàn
   $stmt->execute(); // Thực thi truy vấn
   $result = $stmt->get_result(); // Lấy kết quả
   
   // Kiểm tra kết quả và lấy LoaiXe
   if ($row = $result->fetch_assoc()) {
       $LoaiXe = $row['LoaiXe']; // Gán giá trị LoaiXe từ kết quả truy vấn
   } else {
       die("Không tìm thấy chuyến xe"); // Lỗi nếu không tìm thấy kết quả
   }
   $stmt->close();

   // Truy vấn để lấy GiaTien từ bảng chuyenxe
   $query = "SELECT GiaTien FROM chuyenxe WHERE MaChuyenXe = ?";
   $stmt = $conn->prepare($query);
   $stmt->bind_param('i', $MaChuyenXe);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($row = $result->fetch_assoc()) {
         $GiaTien = $row['GiaTien']; // Lấy giá vé từ bảng chuyenxe
   } else {
         die("Không tìm thấy chuyến xe hoặc giá vé");
   }
   $stmt->close();
   
   // Cấu hình số tầng và số ghế dựa vào LoaiXe
   if ($LoaiXe == 1) {
       $floors = 2; // 2 tầng
       $rowsPerFloor = 6; // 6 hàng mỗi tầng
       $seatsPerRow = 3; // 3 dãy mỗi hàng
   } elseif ($LoaiXe == 2) {
       $floors = 1; // 1 tầng
       $rowsPerFloor = 10; // 10 hàng
       $seatsPerRow = 4; // 4 dãy mỗi hàng
   } else {
       die("Dữ liệu không hợp lệ");
   }   

   // Truy vấn để lấy TenTuyen và ThoiGianKhoiHanh từ MaChuyenXe
   $query = "SELECT * FROM tuyenxe 
   JOIN chuyenxe ON chuyenxe.Tuyen = tuyenxe.MaTuyenXe 
   WHERE chuyenxe.MaChuyenXe = ?";
   $stmt = $conn->prepare($query);
   $stmt->bind_param('i', $MaChuyenXe);
   $stmt->execute();
   $result = $stmt->get_result();

   // Kiểm tra kết quả
   if ($row = $result->fetch_assoc()) {
   $TenTuyen = $row['TenTuyenXe'];
   $ThoiGianKhoiHanh = $row['ThoiGianKhoiHanh'];
   } else {
   die("Không tìm thấy chuyến xe");
   }
   $stmt->close();

   // Lấy danh sách ghế đã bán và chuyến xe tương ứng từ cơ sở dữ liệu
   $soldSeats = [];
   $query = "SELECT MaCho, MaChuyenXe FROM ve 
            JOIN chuyenxe ON ve.ChuyenXe = chuyenxe.MaChuyenXe 
            JOIN xe ON chuyenxe.Xe = xe.MaXe 
            WHERE xe.LoaiXe = ? AND ve.enableflag = 0";
   $stmt = $conn->prepare($query);
   $stmt->bind_param('i', $LoaiXe);
   $stmt->execute();
   $result = $stmt->get_result();

   // Lưu thông tin mã ghế và mã chuyến xe
   while ($row = $result->fetch_assoc()) {
      $soldSeats[] = [
         'MaCho' => $row['MaCho'],        // Mã ghế
         'MaChuyenXe' => $row['MaChuyenXe'] // Mã chuyến xe
      ];
   }
   $stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Chọn vị trí ghế</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
      <link rel="stylesheet" href="../assets/css/choose_seat.css">
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <div class="containers">
         <div class="left-section">
            <div class="seat">
               <div class="seat-selection">
                  <h5 class="text-center">Chọn ghế</h5>
                  <div class="legend">
                     <div class="legend-item">
                        <div class="legend-color" style="background: #9e9e9e"></div>
                        <span>Đã bán</span>
                     </div>
                     <div class="legend-item">
                        <div class="legend-color" style="border: 2px solid #2196F3"></div>
                        <span>Còn trống</span>
                     </div>
                     <div class="legend-item">
                        <div class="legend-color" style="background: #ff3344"></div>
                        <span>Đang chọn</span>
                     </div>
                  </div>
                  <div class="floor-container">
                     <?php for ($floor = 0; $floor < $floors; $floor++): ?>
                        <div class="floor">
                              <div class="floor-label text-center">Tầng <?php echo chr(65 + $floor); ?></div>
                              <div id="floor<?php echo chr(65 + $floor); ?>"></div>
                        </div>
                     <?php endfor; ?>
                  </div>
               </div>
            </div>
            <!-- Customer Information Form -->
            <form id="customerForm" class="mt-3">
               <div class="customer-info">
                 <div class="info-clause-container">
                   <div class="info">
                     <p class="fw-bold">Thông tin khách hàng</p>
                     <div class="form-group">
                       <label style="font-size: small">Họ và tên <span class="required text-danger">*</span></label>
                       <input class="form-control" type="text" id="fullName" name="fullName" required>
                     </div>
                     <div class="form-group">
                       <label style="font-size: small">Số điện thoại <span class="required text-danger">*</span></label>
                       <input class="form-control" type="tel" id="phone" name="phone" required>
                     </div>
                     <div class="form-group">
                       <label style="font-size: small">Email <span class="required text-danger">*</span></label>
                       <input class="form-control" type="email" id="email" name="email" required>
                     </div>
                   </div>
                   <div class="clause">
                     <div class="terms-section pt-0">
                       <h3 class="text-center pt-0">ĐIỀU KHOẢN & LƯU Ý</h3>
                       <div class="terms-content" style="font-size: small">
                         (*) Quý khách vui lòng có mặt tại bến xuất phát của xe trước ít nhất 30 phút giờ xe khởi hành, mang theo thông báo đã thanh toán vé thành công có chứa mã vé được gửi từ hệ thống LACTHAN BUS. Vui lòng liên hệ Trung tâm tổng đài <span style="color: #ff3344">1900 6067</span> để được hỗ trợ.
                         <br><br>
                         (*) Nếu quý khách có nhu cầu trung chuyển, vui lòng liên hệ Tổng đài trung chuyển <span style="color: #ff3344">1900 6918</span> trước khi đặt vé. Chúng tôi không đón/trung chuyển tại những điểm xe trung chuyển không thể tới được.
                       </div>
                     </div>
                   </div>
                 </div>                    
                 <div class="form-check">
                   <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
                   <label class="form-check-label" for="termsCheckbox" style="font-size: small;">
                     <strong><a href="#" style="color: #ff3344">Chấp nhận điều khoản</a></strong> đặt vé & chính sách bảo mật thông tin của LACTHAN Bus
                   </label>
                 </div>
               </div>
            </form>

            <div class="button-container">
               <button class="btn btn-cancel rounded-pill">Hủy</button>
               <button id="paymentBtn" class="btn btn-pay rounded-pill">Thanh toán</button>
            </div>
         </div>
         <div class="right-section">
            <div class="info-card">
               <h3>Thông tin lượt đi</h3>
               <div>
                  <?php
                     function trimRoute($route) {
                        $route = preg_replace('/^Tuyến \d+: /', '', $route);
                        return $route;
                     }
                  ?>
                  <p><strong>Tuyến xe:</strong> <?php echo htmlspecialchars(trimRoute($TenTuyen)); ?></p>
                  <p><strong>Thời gian xuất bến:</strong> <?php echo date("H:i d/m/Y", strtotime($ThoiGianKhoiHanh)); ?></p>
                  <p><strong>Số lượng ghế:</strong> <span id="seatCount" class="fw-bold" style="color: #00684f">0</span> Ghế</p>
                  <p><strong>Số ghế:</strong> <span id="selectedSeats" style="color: #00684f; text-align: end">-</span></p>
               </div>
            </div>
            <div class="info-card">
               <h3>Chi tiết giá</h3>
               <div class="price-details">
                  <div>
                     <span>Giá vé lượt đi</span>
                     <span id="ticketPrice" style="color: #ff3344">0đ</span>
                  </div>
                  <div>
                     <span>Phí thanh toán</span>
                     <span>0đ</span>
                  </div>
                  <div class="total">
                     <span>Tổng tiền</span>
                     <span id="totalPrice" style="color: #ff3344">0đ</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php @include '../includes/footer.php'; ?>
      <script>
         const PRICE_PER_SEAT = <?php echo $GiaTien; ?>;; // Giá mỗi ghế
         const soldSeats = <?php echo json_encode($soldSeats); ?>; // Danh sách ghế đã bán từ PHP
         const CURRENT_TRIP_ID = <?php echo $MaChuyenXe; ?>;
         const TRIP_ROUTE = "<?php echo htmlspecialchars(trimRoute($TenTuyen)); ?>";
         const TRIP_DEPARTURE_TIME = "<?php echo date("H:i d/m/Y", strtotime($ThoiGianKhoiHanh)); ?>";
         let selectedSeats = []; // Ghế đang chọn

         // Các thông số về loại xe (nhận từ PHP)
         const floors = <?php echo $floors; ?>; // Số tầng
         const rowsPerFloor = <?php echo $rowsPerFloor; ?>; // Số hàng mỗi tầng
         const seatsPerRow = <?php echo $seatsPerRow; ?>; // Số ghế mỗi hàng

         document.getElementById('paymentBtn').addEventListener('click', function() {
            const customerForm = document.getElementById('customerForm');
            const termsCheckbox = document.getElementById('termsCheckbox');

            // Kiểm tra xem đã chọn ghế chưa
            if (selectedSeats.length === 0) {
               alert('Vui lòng chọn ít nhất một ghế');
               return;
            }

            // Kiểm tra form và checkbox
            if (customerForm.checkValidity() && termsCheckbox.checked) {
               // Thu thập thông tin
               const paymentData = {
                  fullName: document.getElementById('fullName').value,
                  phone: document.getElementById('phone').value,
                  email: document.getElementById('email').value,
                  seats: selectedSeats,
                  tripId: CURRENT_TRIP_ID,
                  route: TRIP_ROUTE,
                  departureTime: TRIP_DEPARTURE_TIME,
                  totalPrice: selectedSeats.length * PRICE_PER_SEAT
               };

               // Chuyển hướng đến trang thanh toán với thông tin
               const queryParams = new URLSearchParams(paymentData).toString();
               window.location.href = `payment.php?${queryParams}`;
            } else {
               customerForm.reportValidity();
            }
         });

         /**
          * Tạo sơ đồ chỗ ngồi cho từng tầng
          * @param {string} floorId - ID phần tử HTML của tầng
          * @param {string} prefix - Tiền tố ghế (ví dụ: 'A', 'B')
          */
         function createSeatLayout(floorId, prefix) {
            const floor = document.getElementById(floorId);
            
            const rowClass = (floors === 1 && seatsPerRow === 4) ? 'row-four-columns' : 'row';

            // Lặp qua từng hàng
            for (let row = 0; row < rowsPerFloor; row++) {
               const rowDiv = document.createElement('div');
               rowDiv.className = 'row';

               // Lặp qua từng ghế trong hàng
               for (let seat = 1; seat <= seatsPerRow; seat++) {
                     const seatNumber = `${prefix}${String(row * seatsPerRow + seat).padStart(2, '0')}`;
                     const seatDiv = document.createElement('div');
                     const isSold = soldSeats.some(s => s.MaCho === seatNumber && s.MaChuyenXe === CURRENT_TRIP_ID);

                     // Kiểm tra ghế đã bán
                     seatDiv.className = `seat ${isSold ? 'sold' : 'available'}`;
                     seatDiv.textContent = seatNumber;

                     // Thêm sự kiện chọn ghế nếu ghế chưa bán
                     if (!isSold) {
                        seatDiv.addEventListener('click', () => toggleSeat(seatDiv, seatNumber));
                     }

                     rowDiv.appendChild(seatDiv);
               }

               floor.appendChild(rowDiv);
            }
         }

         /**
          * Xử lý chọn/bỏ chọn ghế
          * @param {HTMLElement} seatDiv - Phần tử HTML của ghế
          * @param {string} seatNumber - Số ghế
          */
         function toggleSeat(seatDiv, seatNumber) {
            const seatIndex = selectedSeats.indexOf(seatNumber);

            if (seatIndex === -1) {
               // Chọn ghế
               selectedSeats.push(seatNumber);
               seatDiv.classList.add('selected');
            } else {
               // Bỏ chọn ghế
               selectedSeats.splice(seatIndex, 1);
               seatDiv.classList.remove('selected');
            }

            selectedSeats.sort(); // Sắp xếp danh sách ghế
            updatePriceInfo(); // Cập nhật thông tin giá vé
         }

         /**
          * Cập nhật thông tin giá vé
          */
         function updatePriceInfo() {
            const totalPrice = selectedSeats.length * PRICE_PER_SEAT;
            document.getElementById('seatCount').textContent = selectedSeats.length;
            document.getElementById('selectedSeats').textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : '-';
            document.getElementById('ticketPrice').textContent = `${totalPrice.toLocaleString()}đ`;
            document.getElementById('totalPrice').textContent = `${totalPrice.toLocaleString()}đ`;
         }

         /**
          * Khởi tạo sơ đồ chỗ ngồi
          */
         function initializeSeatLayout() {
            for (let floor = 0; floor < floors; floor++) {
               const floorId = `floor${String.fromCharCode(65 + floor)}`; // 'A', 'B', ...
               createSeatLayout(floorId, String.fromCharCode(65 + floor)); // 'A', 'B', ...
            }
         }

         // Khởi tạo sơ đồ chỗ ngồi khi trang được tải
         initializeSeatLayout();
         updatePriceInfo();
      </script>
   </body>
</html>