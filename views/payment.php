<!--payment.php-->
<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>LacThan Bus</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
      <link rel="stylesheet" href="../assets/css/payment.css">
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <div class="main-container">
         <div class="payment-methods">
            <h2 class="payment-title">Chọn phương thức thanh toán</h2>
            <div class="payment-option" data-method="futapay">
               <input type="radio" name="payment" class="payment-radio">
               <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTp1v7T287-ikP1m7dEUbs2n1SbbLEqkMd1ZA&s" alt="VNPay" class="payment-logo">
               <div class="payment-info">
                  <div class="payment-name">VNPay</div>
               </div>
            </div>
            <div class="payment-option" data-method="zalopay">
               <input type="radio" name="payment" class="payment-radio">
               <img src="https://play-lh.googleusercontent.com/woYAzPCG1I8Z8HXCsdH3diL7oly0N8uth_1g6k7R_9Gu7lbxrsYeriEXLecRG2E9rP0" alt="ZaloPay" class="payment-logo">
               <div class="payment-info">
                  <div class="payment-name">ZaloPay</div>
                  <!-- <div class="payment-promo">Nhập mã GIAMSAU - giảm 50% tối đa 40k cho bạn mới, nhập ZLPFUTA10 giảm 10k cho đơn từ 280k</div> -->
               </div>
            </div>
            <div class="qr-section">
               <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTB7Ziu00uFIRyBfM66pcWDH9inlQKt9hSA_g&s" alt="QR Code" class="qr-code">
               <div class="payment-instructions">
                  <h3>Hướng dẫn thanh toán bằng VNPay</h3>
                  <div class="instruction-step">
                     <div class="step-number">1</div>
                     <div>Mở ứng dụng VNPay hoặc Ngân hàng (Mobile Banking) trên điện thoại</div>
                  </div>
                  <div class="instruction-step">
                     <div class="step-number">2</div>
                     <div>Dùng biểu tượng để quét mã QR</div>
                  </div>
                  <div class="instruction-step">
                     <div class="step-number">3</div>
                     <div>Quét mã ở trang này và thanh toán</div>
                  </div>
               </div>
               <div>
                  <button class="btn btn-primary rounded-pill" id="btnPay">Thanh Toán</button>
               </div>
            </div>
         </div>
         <div class="order-info">
            <?php
               include '../database/db.php';
               // Xử lý thông tin từ URL
               // Retrieve form data
               $fullName = $_GET['fullName'];
               $phone = $_GET['phone'];
               $email = $_GET['email'];
               $seats = explode(',', $_GET['seats']);
               $route = $_GET['route'];
               $departureTime = $_GET['departureTime'];
               $totalPrice = intval($_GET['totalPrice']);
               $tripId = isset($_GET['tripId']) ? intval($_GET['tripId']) : 0;
               
               $query = "SELECT b1.TenBenXe AS TenBenDi, b2.TenBenXe AS TenBenDen 
                  FROM chuyenxe c 
                  JOIN tuyenxe t ON c.Tuyen = t.MaTuyenXe 
                  JOIN benxe b1 ON t.BenDi = b1.MaBenXe 
                  JOIN benxe b2 ON t.BenDen = b2.MaBenXe WHERE c.MaChuyenXe = ?";
   
               if ($stmt = $conn->prepare($query)) {
                     $stmt->bind_param("i", $tripId);
                     $stmt->execute();
                     $stmt->bind_result($tenBenDi, $tenBenDen);
                     $stmt->fetch();
                     $stmt->close();
               } else {
                     $tenBenDi = $tenBenDen = "Không có thông tin";
               }
               
               $conn->commit();

               // Start transaction
               $conn->begin_transaction();
            ?>
            <div class="total-amount"><?php echo number_format($totalPrice, 0, ',', '.'); ?>đ</div>
            <div class="timer">Thời gian giữ chỗ còn lại 14:51</div>
            <div class="section-title">
               Thông tin hành khách 
            </div>
            <div class="info-row">
               <div class="info-label">Họ và tên</div>
               <div class="info-value"><?php echo htmlspecialchars($fullName); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Số điện thoại</div>
               <div class="info-value"><?php echo htmlspecialchars($phone); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Email</div>
               <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
            </div>
            <div class="section-title">
               Thông tin lượt đi <i class="fas fa-info-circle" style="color: #ff3344"></i>
            </div>
            <div class="info-row">
               <div class="info-label">Tuyến xe</div>
               <div class="info-value"><?php echo htmlspecialchars($route); ?></div>
            </div>
            <div class="info-row d-none">
               <div class="info-label">Mã chuyến</div>
               <div class="info-value"><?php echo htmlspecialchars($tripId); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Thời gian xuất bến</div>
               <div class="info-value"><?php echo htmlspecialchars($departureTime); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Số ghế</div>
               <div class="info-value"><?php echo htmlspecialchars(implode(', ', $seats)); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Điểm lên xe</div>
               <div class="info-value"><?php echo htmlspecialchars($tenBenDi); ?></div>
            </div>
            <div class="info-row">
               <div class="info-label">Điểm trả khách</div>
               <div class="info-value"><?php echo htmlspecialchars($tenBenDen); ?></div>
            </div>
            <div class="section-title">
               Chi tiết giá <i class="fas fa-info-circle" style="color: #ff3344"></i>
            </div>
            <div class="info-row">
               <div class="info-label">Giá vé lượt đi</div>
               <div class="info-value"><?php echo number_format($totalPrice, 0, ',', '.'); ?>đ</div>
            </div>
            <div class="info-row">
               <div class="info-label">Phí thanh toán</div>
               <div class="info-value">0đ</div>
            </div>
            <div class="info-row">
               <div class="info-label">Tổng tiền</div>
               <div class="info-value"><?php echo number_format($totalPrice, 0, ',', '.'); ?>đ</div>
            </div>
         </div>
      </div>
      <form action="momo_payment.php" method="post">
         <input type="hidden" name="amount" value="<?php echo $totalPrice; ?>">
         <button type="submit" class="btn btn-primary">Thanh toán bằng Momo</button>
      </form>
      <?php @include '../includes/footer.php'; ?>
      <script>
         document.getElementById('btnPay').addEventListener('click', function () {
         // Thu thập thông tin từ trang thanh toán
         const fullName = "<?php echo $fullName; ?>";
         const phone = "<?php echo $phone; ?>";
         const email = "<?php echo $email; ?>";
         const seats = "<?php echo implode(',', $seats); ?>";
         const route = "<?php echo $route; ?>";
         const departureTime = "<?php echo $departureTime; ?>";
         const totalPrice = "<?php echo $totalPrice; ?>";
         const tripId = "<?php echo $tripId; ?>";
         
         // Gửi Ajax đến server để lưu thông tin
         fetch('process-payment.php', {
             method: 'POST',
             headers: {
                 'Content-Type': 'application/json'
             },
             body: JSON.stringify({
                 fullName: fullName,
                 phone: phone,
                 email: email,
                 seats: seats,
                 route: route,
                 departureTime: departureTime,
                 totalPrice: totalPrice,
                 tripId: tripId
             })
         })
         .then(response => response.json())
         .then(data => {
             if (data.success) {
                 alert('Thanh toán thành công! Mã hóa đơn: ' + data.invoiceId);
                 // Gửi email hóa đơn
                  fetch('send_invoice_email.php', {
                        method: 'POST',
                        headers: {
                           'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                           fullName: fullName,
                           email: email,
                           totalPrice: totalPrice,
                           route: route,
                           departureTime: departureTime,
                           seats: seats,
                           invoiceId: data.invoiceId
                        })
                  })
                  .then(emailResponse => emailResponse.json())
                  .then(emailData => {
                        if (emailData.success) {
                           alert('Hóa đơn đã được gửi qua email');
                        } else {
                           alert('Lỗi khi gửi email: ' + emailData.message);
                        }
                  });
                 window.location.href = 'payment-success.php?invoice=' + data.invoiceId;
             } else {
                 alert('Thanh toán thất bại: ' + data.message);
             }
         })
         .catch(error => {
             console.error('Error:', error);
             alert('Có lỗi xảy ra khi thanh toán!');
         });
         });
         // Handle payment method selection
         const paymentOptions = document.querySelectorAll('.payment-option');
         
         paymentOptions.forEach(option => {
         option.addEventListener('click', function() {
             // Remove selected class from all options
             paymentOptions.forEach(opt => {
                 opt.classList.remove('selected');
                 opt.querySelector('input[type="radio"]').checked = false;
             });
             
             // Add selected class to clicked option
             this.classList.add('selected');
             this.querySelector('input[type="radio"]').checked = true;
         });
         });
         
         // Countdown timer
         function startTimer(duration, display) {
         let timer = duration;
         let minutes, seconds;
         
         let countdown = setInterval(function () {
             minutes = parseInt(timer / 60, 10);
             seconds = parseInt(timer % 60, 10);
         
             minutes = minutes < 10 ? "0" + minutes : minutes;
             seconds = seconds < 10 ? "0" + seconds : seconds;
         
             display.textContent = `Thời gian giữ chỗ còn lại ${minutes}:${seconds}`;
         
             if (--timer < 0) {
                 clearInterval(countdown);
                 display.textContent = "Hết thời gian giữ chỗ";
             }
         }, 1000);
         }
         
         // Start countdown with 15 minutes (900 seconds)
         window.onload = function () {
         let fifteenMinutes = 60 * 15,
             display = document.querySelector('.timer');
         startTimer(fifteenMinutes, display);
         };
      </script>
   </body>
</html>