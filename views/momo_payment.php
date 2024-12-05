<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
    $partnerCode = "MOMOBKUN20180529"; // Thay bằng PartnerCode
    $accessKey = "klm05TvNBzhg7h7j";     // Thay bằng AccessKey
    $secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";     // Thay bằng SecretKey
    $orderId = time(); // Tạo mã đơn hàng duy nhất
    $orderInfo = "Thanh toán đơn hàng Momo";
    $amount = $_POST['amount']; // Số tiền cần thanh toán
    $redirectUrl = "http://localhost/payment-success.php"; // URL thành công
    $ipnUrl = "http://localhost/momo_ipn.php";            // URL IPN
    $extraData = ""; // Dữ liệu thêm nếu cần

    // Tạo mảng dữ liệu gửi
    $requestData = array(
        "partnerCode" => $partnerCode,
        "accessKey" => $accessKey,
        "requestId" => time(),
        "amount" => $amount,
        "orderId" => $orderId,
        "orderInfo" => $orderInfo,
        "redirectUrl" => $redirectUrl,
        "ipnUrl" => $ipnUrl,
        "extraData" => $extraData,
        "requestType" => "captureWallet"
    );

    // Ký dữ liệu
    $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestData['requestId'] . "&requestType=" . $requestData['requestType'];
    $signature = hash_hmac("sha256", $rawHash, $secretKey);
    $requestData['signature'] = $signature;

    // Gửi request tới Momo
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    $response = curl_exec($ch);
    curl_close($ch);

    // Xử lý phản hồi từ Momo
    $result = json_decode($response, true);
    if (isset($result['payUrl'])) {
        header("Location: " . $result['payUrl']);
    } else {
        echo "Thanh toán thất bại! Vui lòng thử lại.";
    }
}
?>
