<?php
$config = include('vnpay_config.php');

$vnp_HashSecret = $config['vnp_HashSecret'];
$inputData = [];
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

$vnp_SecureHash = $inputData['vnp_SecureHash'];
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = "";
foreach ($inputData as $key => $value) {
    $hashData .= $key . '=' . $value . '&';
}
$hashData = rtrim($hashData, '&');

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

if ($secureHash === $vnp_SecureHash) {
    $responseCode = $inputData['vnp_ResponseCode'];
    $invoiceId = $inputData['vnp_TxnRef'];

    if ($responseCode == '00') {
        // Cập nhật hóa đơn thành công
        $stmt = $conn->prepare("UPDATE hoadon SET TrangThaiThanhToan = 'Success', MaGiaoDichVNPay = ? WHERE MaHoaDon = ?");
        $stmt->bind_param("si", $invoiceId, $invoiceId);
        $stmt->execute();
        echo "Thanh toán thành công!";
    } else {
        echo "Thanh toán thất bại!";
    }
} else {
    echo "Chữ ký không hợp lệ!";
}
?>
