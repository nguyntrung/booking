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
      <title>FUTA Bus Lines - Chất lượng là danh dự</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../assets/css/style.css">
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <!-- Main Banner -->
      <div class="main-banner">
         <div class="container">
            <img src="../assets/img/banner.png" alt="23 năm vững tin và phát triển" class="banner-image">
            <!-- Search Form -->
            <form action="index.php" method="GET">
               <div class="search-form">
                  <div class="search-form__title">
                     <h5 class="text-center pb-3 fw-bold" style="color: #00843D;">TÌM KIẾM VÉ</h5>
                  </div>
                  <div class="search-toggle">
                     <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                        <label class="form-check-label" for="flexSwitchCheckDefault">Khứ hồi</label>
                     </div>
                  </div>
                  <div class="row g-3">
                     <div class="col-md-3">
                        <label class="form-label">Điểm đi</label>
                        <select class="form-control" name="diem_di">
                        <?php
                           include '../database/db.php';
                           $query = "SELECT MaBenXe, TenBenXe FROM BENXE";
                           $result = $conn->query($query);
                           while ($row = $result->fetch_assoc()) {
                               echo "<option value='{$row['MaBenXe']}'>{$row['TenBenXe']}</option>";
                           }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label class="form-label">Điểm đến</label>
                        <select class="form-control" name="diem_den">
                        <?php
                           include '../database/db.php';
                           $query = "SELECT MaBenXe, TenBenXe FROM BENXE";
                           $result = $conn->query($query);
                           while ($row = $result->fetch_assoc()) {
                               echo "<option value='{$row['MaBenXe']}'>{$row['TenBenXe']}</option>";
                           }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label class="form-label">Ngày đi</label>
                        <input type="date" class="form-control" name="ngay_di">
                     </div>
                     <div class="col-md-3 d-none" id="return-date">
                        <label class="form-label">Ngày về</label>
                        <input type="date" class="form-control" value="2024-10-24">
                     </div>
                     <div class="col-md-3">
                        <label class="form-label">Số lượng vé</label>
                        <input type="number" class="form-control" min="1" value="1">
                     </div>
                  </div>
                  <div class="row g-3 mt-3">
                     <div class="col-md-12 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        Tìm chuyến xe
                        </button>
                     </div>
                  </div>
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
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
   </body>
</html>