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
      <!-- Main Lookup -->
        <div class="container py-5">
            <div class="col-md-8" style="margin: 0 auto">
                <div class="card">
                    <div class="card-header">
                    <h4 class="mb-0 text-center">TRA CỨU THÔNG TIN ĐẶT VÉ</h4>
                    </div>
                    <div class="card-body">
                    <form method="POST" action="check_ticket.php">
                        <div class="mb-3">
                        <label for="phone" class="form-label">Vui lòng nhập số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="SDT"placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                        <label for="ticket-number" class="form-label">Vui lòng nhập mã vé</label>
                        <input type="text" class="form-control" id="ticket-number" name="MaVeXe"placeholder="Nhập mã vé">
                        </div>
                        <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Tra cứu</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
      <?php @include '../includes/footer.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../assets/js/script.js"></script>
   </body>
</html>