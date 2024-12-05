<!-- choose_seat.php -->

<?php
   include '../database/db.php';

   // Nhận MaChuyenXe từ URL
   $MaVeXe = isset($_GET['id']) ? intval($_GET['id']) : die("Chưa chọn ve");

   $query = "
       SELECT ChuyenXe, MaCho  FROM ve WHERE MaVeXe = ?"; 
   $stmt = $conn->prepare($query); 
   $stmt->bind_param('i', $MaVeXe); 
   $stmt->execute(); 
   $result = $stmt->get_result(); 
   
   if ($row = $result->fetch_assoc()) {
        $MaChuyenXe = $row['ChuyenXe']; 
        $ChoCuaToi = $row['MaCho']; 
    } else {
        die("Không tìm thấy ve xe"); 
    }
    $stmt->close();
   
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
                  <p>Ghế cũ: <?php echo $ChoCuaToi; ?></p>
                  <p id="selectedSeatText">Ghế mới: </p>
               </div>
            </div>
            <div class="info-card">
                <div class="button-container">
                    <a href="./my-bookings.php" class="btn btn-cancel rounded-pill">Hủy</a>
                    <a href="#" id="confirmButton" class="btn btn-pay rounded-pill">Xác nhận đổi</a>
                
                </div>
                <p id="errorMessage" style="color: red; display: none;">Vui lòng chọn một ghế trước khi xác nhận!</p>
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
let selectedSeats = []; // Mảng chứa ghế đang chọn

// Các thông số về loại xe (nhận từ PHP)
const floors = <?php echo $floors; ?>; // Số tầng
const rowsPerFloor = <?php echo $rowsPerFloor; ?>; // Số hàng mỗi tầng
const seatsPerRow = <?php echo $seatsPerRow; ?>; // Số ghế mỗi hàng

let selectedSeat = null;
var maVeXe = "<?php echo $MaVeXe; ?>"; 

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
    // Nếu ghế đã được chọn, bỏ chọn
    if (selectedSeat === seatNumber) {
        seatDiv.classList.remove('selected');
        selectedSeat = null; // Đặt lại ghế đã chọn
        // Cập nhật lại nội dung ghế đã chọn
        document.getElementById('selectedSeatText').textContent = 'Ghế mới:';
    } else {
        // Bỏ chọn ghế trước đó nếu có
        const previouslySelectedSeat = document.querySelector('.seat.selected');
        if (previouslySelectedSeat) {
            previouslySelectedSeat.classList.remove('selected');
        }

        // Chọn ghế mới
        seatDiv.classList.add('selected');
        selectedSeat = seatNumber; // Cập nhật ghế đã chọn
        // Cập nhật nội dung ghế mới đã chọn
        document.getElementById('selectedSeatText').textContent = 'Ghế mới: ' + seatNumber;
    }
}

// Hàm xử lý nhấn nút xác nhận
document.getElementById('confirmButton').addEventListener('click', function(event) {
    if (!selectedSeat) {
        // Hiển thị thông báo lỗi nếu chưa chọn ghế
        document.getElementById('errorMessage').style.display = 'block';
    } else {
        // Ẩn thông báo lỗi nếu có ghế đã chọn
        document.getElementById('errorMessage').style.display = 'none';
        
        // Cập nhật URL và chuyển hướng
        const link = document.getElementById('confirmButton');
        link.href = './doive_xuli.php?id='+maVeXe+'&macho=' + selectedSeat; // Thêm mã ghế vào URL
    }
});

/**
 * Cập nhật thông tin giá dựa trên số ghế đã chọn
 */
function updatePriceInfo() {
   const totalPrice = selectedSeats.length * PRICE_PER_SEAT;
   // Cập nhật giá tiền hiển thị (tùy thuộc vào phần tử HTML bạn muốn cập nhật)
   document.getElementById("totalPrice").textContent = `Tổng giá: ${totalPrice} VNĐ`;
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