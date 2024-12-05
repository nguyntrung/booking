<?php
include '../database/db.php';
// send_invoice_email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$data = json_decode(file_get_contents('php://input'), true);
$fullName = $data['fullName'];
$email = $data['email'];
$totalPrice = $data['totalPrice'];
$route = $data['route'];
$departureTime = $data['departureTime'];
$seats = $data['seats'];
$invoiceId = $data['invoiceId'];

$query = "SELECT v.MaVeXe
          FROM ve v
          JOIN hoadon h ON v.HoaDon = h.MaHoaDon
          WHERE v.HoaDon = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $invoiceId);
$stmt->execute();
$stmt->bind_result($maVeXe);

$ticketCodes = [];
while ($stmt->fetch()) {
    $ticketCodes[] = $maVeXe;
}
$stmt->close();

$ticketCodesStr = implode('-', $ticketCodes);

$bodyContent = '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn LacThan Bus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .invoice-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .invoice-header {
            background-color: #ff3344;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }
        .invoice-details {
            margin-top: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .invoice-details .label {
            font-weight: bold;
            width: 40%;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            text-align: right;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>Hóa Đơn Thanh Toán</h1>
            <p>Mã hóa đơn: ' . htmlspecialchars($invoiceId) . '</p>
        </div>
        
        <div class="invoice-details">
            <table>
                <tr>
                    <td class="label">Mã vé:</td>
                    <td>' . htmlspecialchars($ticketCodesStr) . '</td>
                </tr>
                <tr>
                    <td class="label">Họ và tên:</td>
                    <td>' . htmlspecialchars($fullName) . '</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>' . htmlspecialchars($email) . '</td>
                </tr>
                <tr>
                    <td class="label">Tuyến xe:</td>
                    <td>' . htmlspecialchars($route) . '</td>
                </tr>
                <tr>
                    <td class="label">Thời gian xuất bến:</td>
                    <td>' . htmlspecialchars($departureTime) . '</td>
                </tr>
                <tr>
                    <td class="label">Số ghế:</td>
                    <td>' . htmlspecialchars($seats) . '</td>
                </tr>
            </table>
            
            <div class="total">
                Tổng tiền: ' . number_format($totalPrice, 0, ',', '.') . 'đ
            </div>
        </div>
        
        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ LacThan Bus</p>
            <p>Liên hệ hỗ trợ: support@lacthanbus.com | Hotline: 1900 xxxx</p>
        </div>
    </div>
</body>
</html>
';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'trungnguyen6503@gmail.com';
    $mail->Password = 'leqe wcxf wals idfg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587; 

    $mail->setFrom('trungnguyen6503@gmail.com', 'LacThan Bus');
    $mail->addAddress($email, $fullName); 

    $mail->isHTML(true);
    $mail->Subject = 'Hóa đơn thanh toán LacThan Bus';
    $mail->Body = $bodyContent;

    $mail->CharSet = 'UTF-8';

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Hóa đơn đã được gửi qua email']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi email: ' . $mail->ErrorInfo]);
}
?>