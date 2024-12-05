<?php
require_once("vnpay_config.php");

$vnp_TxnRef = rand(100000, 999999); // Mã giao dịch của bạn
$vnp_OrderInfo = "Thanh toan don hang " . $vnp_TxnRef;
$vnp_Amount = $_POST['amount'] * 100; // Đơn vị VNĐ, nhân 100 theo yêu cầu VNPay
$vnp_Locale = "vn"; // Ngôn ngữ
$vnp_BankCode = ""; // Mã ngân hàng (nếu có)
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_Command" => "pay",
    "vnp_TmnCode" => VNP_TMN_CODE,
    "vnp_Amount" => $vnp_Amount,
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_ReturnUrl" => VNP_RETURN_URL,
    "vnp_TxnRef" => $vnp_TxnRef,
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

ksort($inputData);
$query = "";
$hashdata = "";

foreach ($inputData as $key => $value) {
    $hashdata .= $key . "=" . $value . "&";
    $query .= urlencode($key) . "=" . urlencode($value) . "&";
}

$vnp_Url = VNP_URL . "?" . $query;
$vnp_Url = rtrim($vnp_Url, "&");

$secureHash = hash_hmac('sha512', rtrim($hashdata, "&"), VNP_HASH_SECRET);
$vnp_Url .= '&vnp_SecureHash=' . $secureHash;

header('Location: ' . $vnp_Url);
exit();
?>
