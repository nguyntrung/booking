<?php
$secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa"; // Thay bằng SecretKey
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Xác thực chữ ký
$rawHash = "accessKey=" . $data['accessKey'] . "&amount=" . $data['amount'] . "&extraData=" . $data['extraData'] . "&message=" . $data['message'] . "&orderId=" . $data['orderId'] . "&orderInfo=" . $data['orderInfo'] . "&orderType=" . $data['orderType'] . "&partnerCode=" . $data['partnerCode'] . "&payType=" . $data['payType'] . "&requestId=" . $data['requestId'] . "&responseTime=" . $data['responseTime'] . "&resultCode=" . $data['resultCode'] . "&transId=" . $data['transId'];
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Kiểm tra chữ ký
if ($signature === $data['signature']) {
    if ($data['resultCode'] == 0) {
        // Thanh toán thành công
        echo "success";
    } else {
        // Thanh toán thất bại
        echo "failure";
    }
} else {
    // Chữ ký không hợp lệ
    echo "invalid signature";
}
?>
