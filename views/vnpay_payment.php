<?php
$config = include('vnpay_config.php');

// Lấy thông tin từ form
$fullName = $_POST['fullName'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$seats = $_POST['seats'];
$route = $_POST['route'];
$departureTime = $_POST['departureTime'];
$totalPrice = $_POST['totalPrice'];
$tripId = $_POST['tripId'];

// Tạo URL thanh toán
$vnp_TmnCode = $config['vnp_TmnCode'];
$vnp_HashSecret = $config['vnp_HashSecret'];
$vnp_Url = $config['vnp_Url'];
$vnp_Returnurl = $config['vnp_Returnurl'];

$vnp_TxnRef = rand(100000, 999999); // Mã giao dịch
$vnp_OrderInfo = "Thanh toán vé xe";
$vnp_Amount = $totalPrice * 100; // VNPay yêu cầu đơn vị VND * 100
$vnp_Locale = 'vn';
$vnp_BankCode = 'VNPAYQR';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

// Tạo mảng tham số
$inputData = [
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => "billpayment",
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_BankCode" => $vnp_BankCode
];

ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . $key . "=" . $value;
    } else {
        $hashdata .= $key . "=" . $value;
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

header('Location: ' . $vnp_Url);
?>
