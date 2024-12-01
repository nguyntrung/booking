<?php
   header('Content-Type: application/json');
   include '../database/db.php';
   
   $response = ['success' => false, 'message' => ''];
   
   try {
       // Nhận dữ liệu từ Ajax
       $input = json_decode(file_get_contents('php://input'), true);
       $fullName = $input['fullName'];
       $phone = $input['phone'];
       $email = $input['email'];
       $seats = explode(',', $input['seats']);
       $route = $input['route'];
       $departureTime = $input['departureTime'];
       $totalPrice = intval($input['totalPrice']);
       $tripId = $input['tripId'];
   
       // Bắt đầu giao dịch
       $conn->begin_transaction();
   
       // Kiểm tra thông tin khách hàng
       $stmt = $conn->prepare("SELECT MaHK FROM hanhkhach WHERE email = ? AND enableflag = 0");
       $stmt->bind_param("s", $email);
       $stmt->execute();
       $result = $stmt->get_result();
   
       if ($result->num_rows > 0) {
           $row = $result->fetch_assoc();
           $customerId = $row['MaHK'];
       } else {
           $stmt = $conn->prepare("INSERT INTO hanhkhach (TenHK, SDT, email, enableflag) VALUES (?, ?, ?, 0)");
           $stmt->bind_param("sss", $fullName, $phone, $email);
           $stmt->execute();
           $customerId = $conn->insert_id;
       }
   
       // Tạo hóa đơn
       $seatCount = count($seats);
       $stmt = $conn->prepare("INSERT INTO hoadon (NgayLap, SoLuongVe, TongTien, HanhKhach, enableflag) VALUES (CURRENT_DATE, ?, ?, ?, 0)");
       $stmt->bind_param("iii", $seatCount, $totalPrice, $customerId);
       $stmt->execute();
       $invoiceId = $conn->insert_id;
   
       // Tạo vé cho từng ghế
       $stmt = $conn->prepare("INSERT INTO ve (TenTuyen, ThoiGianKhoiHanh, ThoiGianKetThuc, MaCho, HoaDon, ChuyenXe, enableflag) VALUES (?, ?, ?, ?, ?, ?, 0)");
       foreach ($seats as $seat) {
           // Tính toán thời gian khởi hành cho mỗi vé
           $departureTimeForTicket = DateTime::createFromFormat('H:i d/m/Y', $departureTime)->format('Y-m-d H:i:s');
           $departureEndTime = date('Y-m-d H:i:s', strtotime($departureTimeForTicket . ' +2 hours')); // Giả định giờ kết thúc
       
           // Gửi dữ liệu vào cơ sở dữ liệu
           $stmt->bind_param("ssssii", $route, $departureTimeForTicket, $departureEndTime, $seat, $invoiceId, $tripId);
           if (!$stmt->execute()) {
               $response['message'] = "Lỗi khi lưu vé: " . $stmt->error;
               echo json_encode($response);
               exit;
           }
       }    
   
       // Hoàn tất giao dịch
       $conn->commit();
   
       // Trả về kết quả thành công
       $response['success'] = true;
       $response['invoiceId'] = $invoiceId;
   
   } catch (Exception $e) {
       // Rollback nếu xảy ra lỗi
       $conn->rollback();
       $response['message'] = "Lỗi khi thanh toán: " . $e->getMessage();
   }
   
   // Trả về phản hồi JSON
   echo json_encode($response);
?>