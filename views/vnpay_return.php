<?php
require_once("vnpay_config.php");

$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();

foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = "";

foreach ($inputData as $key => $value) {
    $hashData .= $key . "=" . $value . "&";
}

$hashData = rtrim($hashData, "&");
$secureHash = hash_hmac('sha512', $hashData, VNP_HASH_SECRET);

if ($secureHash === $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] == '00') {
        echo "Thanh toán thành công!";
        // Cập nhật hóa đơn trong CSDL
    } else {
        echo "Giao dịch không thành công.";
    }
} else {
    echo "Chữ ký không hợp lệ!";
}
?>
