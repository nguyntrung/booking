<?php
   // Kết nối cơ sở dữ liệu
   include '../database/db.php';
   
   $query = "SELECT MaBenXe, TenBenXe FROM BENXE";
   $result = mysqli_query($conn, $query);
   
   if (!$result) {
       die("Truy vấn thất bại: " . mysqli_error($conn));
   }
   
   function renderOptions($result)
   {
       $options = '';
       while ($row = mysqli_fetch_assoc($result)) {
           $options .= "<option value='{$row['MaBenXe']}'>{$row['TenBenXe']}</option>";
       }
       return $options;
   }
   
   $benXeOptions = renderOptions($result);
   ?>
<!-- index.php -->
<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>LacThan Bus</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
      <link rel="stylesheet" href="../assets/css/ticket.css">
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <!-- Main Banner -->
      <div class="main-banner">
         <div class="container" style="max-width: 1130px">
            <img src="../assets/img/banner.png" alt="23 năm vững tin và phát triển" class="banner-image">
            <!-- Search Form -->
            <form action="index.php" method="GET">
               <div class="search-form">
                  <div class="search-form__title">
                     <h5 class="text-center pb-3 fw-bold" style="color: #00843D;">TÌM KIẾM VÉ</h5>
                  </div>
                  <div class="search-toggle">
                     <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" <?php echo (isset($_GET['khu_hoi']) && $_GET['khu_hoi'] == '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="flexSwitchCheckDefault">Khứ hồi</label>
                     </div>
                  </div>
                  <div class="row g-3">
                     <div class="col-md-3">
                        <label class="form-label">Điểm đi</label>
                        <select class="form-control" name="diem_di">
                           <?php
                              $query = "SELECT MaBenXe, TenBenXe FROM BENXE";
                              $result = $conn->query($query);
                              while ($row = $result->fetch_assoc()) {
                                 $selected = (isset($_GET['diem_di']) && $_GET['diem_di'] == $row['MaBenXe']) ? 'selected' : '';
                                 echo "<option value='{$row['MaBenXe']}' {$selected}>{$row['TenBenXe']}</option>";
                              }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label class="form-label">Điểm đến</label>
                        <select class="form-control" name="diem_den">
                           <?php
                              $query = "SELECT MaBenXe, TenBenXe FROM BENXE";
                              $result = $conn->query($query);
                              while ($row = $result->fetch_assoc()) {
                                 $selected = (isset($_GET['diem_den']) && $_GET['diem_den'] == $row['MaBenXe']) ? 'selected' : '';
                                 echo "<option value='{$row['MaBenXe']}' {$selected}>{$row['TenBenXe']}</option>";
                              }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label class="form-label">Ngày đi</label>
                        <input type="date" class="form-control" name="ngay_di" min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($_GET['ngay_di']) ? $_GET['ngay_di'] : date('Y-m-d'); ?>">
                     </div>
                     <div class="col-md-3 d-none" id="return-date">
                        <label class="form-label">Ngày về</label>
                        <input type="date" class="form-control" name="ngay_ve" min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($_GET['ngay_ve']) ? $_GET['ngay_ve'] : date('Y-m-d'); ?>" id="ngayVe">
                     </div>
                     <!-- <div class="col-md-3">
                        <label class="form-label">Số lượng vé</label>
                        <input type="number" class="form-control" min="1" value="1">
                     </div> -->
                  </div>
                  <div class="row g-3 mt-3">
                     <!-- Adjusted to center the button using col-md-12 and a custom class -->
                     <div class="col-12 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                           Tìm chuyến xe
                        </button>
                     </div>
                  </div>
                  <input type="hidden" name="khu_hoi" id="khuHoiInput" value="0">
               </div>
            </form>
         </div>
      </div>
      <?php
         if (isset($_GET['diem_di'], $_GET['diem_den'], $_GET['ngay_di'])) {
             include 'ticket_list.php';
         } else {
             include 'promotions.php';
         }
         ?>

      <?php @include '../views/showfeedback.php'; ?>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
      <script>
         function handleRoundTrip() {
            const switchChecked = document.getElementById('flexSwitchCheckDefault').checked;
            const returnDateInput = document.getElementById('return-date').querySelector('input');
         
            // Nếu công tắc không được bật, loại bỏ tham số ngày về khỏi form
            if (!switchChecked) {
               returnDateInput.removeAttribute('name');
            } else {
               returnDateInput.setAttribute('name', 'ngay_ve');
            }
         }
         
         document.getElementById('flexSwitchCheckDefault').addEventListener('change', function() {
            const returnDate = document.getElementById('return-date');
            const khuHoiInput = document.getElementById('khuHoiInput');
            
            if (this.checked) {
               returnDate.classList.remove('d-none');
               khuHoiInput.value = '1';
            } else {
               returnDate.classList.add('d-none'); 
               khuHoiInput.value = '0';
            }

            if (this.checked && document.getElementById('ngayVe').value === "") {
               document.getElementById('ngayVe').value = "<?php echo isset($_GET['ngay_ve']) ? $_GET['ngay_ve'] : date('Y-m-d'); ?>";
            }
         });
      </script>
   </body>
</html>