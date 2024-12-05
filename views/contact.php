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
   </head>
   <body>
      <?php @include '../includes/header.php'; ?>
      <!-- Main Contact -->
        <div class="container py-5">
            <div class="row">
                <!-- Left Column - Contact Information -->
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4">LIÊN HỆ VỚI CHÚNG TÔI</h5>
                    
                    <div class="mb-4">
                        <a href="#" class="text-dark text-decoration-none">
                            <i class="fas fa-angle-right"></i> LacThan Bus
                        </a>
                    </div>
                    
                    <div class="company-info">
                        <p class="text-danger mb-3">CÔNG TY CỔ PHẦN XE KHÁCH LẠC THẦN - LACTHAN BUS</p>
                        
                        <p class="mb-2">
                            <strong>Địa chỉ:</strong> Số 01 Tô Hiến Thành, Phường 3, Thành phố Đà Lạt, Tỉnh Lâm Đồng, Việt Nam
                        </p>
                        
                        <p class="mb-2">
                            <strong>Website:</strong> <a href="https://lacthanbus.vn/" class="text-decoration-none">https://lacthanbus.vn/</a>
                        </p>
                        
                        <p class="mb-2">
                            <strong>Điện thoại:</strong> 02838386852
                        </p>
                        
                        <p class="mb-2">
                            <strong>Fax:</strong> 02838386853
                        </p>
                        
                        <p class="mb-2">
                            <strong>Email:</strong> hotro@lacthan.vn
                        </p>
                        
                        <p class="mb-2">
                            <strong>Hotline:</strong> 19006067
                        </p>
                    </div>
                </div>
                
                <!-- Right Column - Contact Form -->
                <div class="col-md-8">
                    <div class="contact-form-header d-flex align-items-center mb-4">
                        <i class="fas fa-envelope text-danger me-2"></i>
                        <h4 class="text-danger m-0">Gửi thông tin liên hệ đến chúng tôi</h4>
                    </div>
                    
                    <form action="submit_contact.php" method="POST" class="bg-body-secondary p-4 rounded">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select class="form-select mb-3" name="DichVu">
                                    <option selected>LACTHAN BUS</option>
                                </select>
                                
                                <input type="email" class="form-control" name="Email" placeholder="Email" required>
                            </div>
                            
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-3" name="HoTen" placeholder="Họ và tên" required>
                                <input type="tel" class="form-control" name="SDT" placeholder="Điện thoại" pattern="\d+" title="Chỉ nhập số" required>
                            </div>
                        </div>
                        
                        <input type="text" class="form-control mb-3" name="TieuDe" placeholder="Nhập tiêu đề">
                        
                        <textarea class="form-control mb-4" name="NoiDung" rows="6" placeholder="Nhập nội dung"></textarea>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger px-5 rounded-pill">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
   </body>
</html>