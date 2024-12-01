<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/../vendor/autoload.php'; 

include '../database/db.php';

$invoiceId = isset($_GET['invoice']) ? $_GET['invoice'] : null;

if ($invoiceId) {
    $stmt = $conn->prepare("
        SELECT 
            hoadon.*, 
            hanhkhach.TenHK,
            ve.TenTuyen, 
            ve.ThoiGianKhoiHanh AS GioKhoiHanh,
            GROUP_CONCAT(ve.MaCho ORDER BY ve.MaCho SEPARATOR ', ') AS MaCho
        FROM hoadon 
        JOIN hanhkhach ON hoadon.HanhKhach = hanhkhach.MaHK 
        JOIN ve ON hoadon.MaHoaDon = ve.HoaDon 
        WHERE hoadon.MaHoaDon = ? 
        GROUP BY hoadon.MaHoaDon
    ");
    $stmt->bind_param("s", $invoiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('dejavusans', '', 12, '', true);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LacThan Bus');
$pdf->SetTitle('Hóa Đơn Thanh Toán');

$pdf->SetHeaderData('', 0, 'LacThan Bus', 'Hóa Đơn Thanh Toán', array(0,64,255), array(0,64,128));
$pdf->setHeaderFont(Array('dejavusans', '', 9));
$pdf->setFooterFont(Array('dejavusans', '', 8));

$pdf->AddPage();

$invoiceDate = new DateTime($booking['GioKhoiHanh']);
$formattedDate = $invoiceDate->format('H:i d/m/Y');

$html = '
<style>
    body {
        font-family: "DejaVu Sans", sans-serif;
    }
    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .invoice-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .invoice-details {
        border: 1px solid #ddd;
        padding: 15px;
    }
    .detail-row {
        margin-bottom: 10px;
    }
    .detail-label {
        font-weight: bold;
        display: inline-block;
        width: 150px;
    }
</style>

<div class="invoice-container">
    <div class="invoice-header">
        <h1>HÓA ĐƠN THANH TOÁN</h1>
        <p>Mã Hóa Đơn: ' . htmlspecialchars($invoiceId, ENT_QUOTES, 'UTF-8') . '</p>
    </div>

    <div class="invoice-details">
        <div class="detail-row">
            <span class="detail-label">Hành Khách:</span>
            ' . htmlspecialchars($booking['TenHK'], ENT_QUOTES, 'UTF-8') . '
        </div>
        <div class="detail-row">
            <span class="detail-label">Tuyến Xe:</span>
            ' . htmlspecialchars($booking['TenTuyen'], ENT_QUOTES, 'UTF-8') . '
        </div>
        <div class="detail-row">
            <span class="detail-label">Ngày Khởi Hành:</span>
            ' . htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8') . '
        </div>
        <div class="detail-row">
            <span class="detail-label">Số Ghế:</span>
            ' . htmlspecialchars($booking['MaCho'], ENT_QUOTES, 'UTF-8') . '
        </div>
        <div class="detail-row">
            <span class="detail-label">Tổng Tiền:</span>
            ' . number_format($booking['TongTien'], 0, ',', '.') . 'đ
        </div>
    </div>
</div>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('invoice_' . $invoiceId . '.pdf', 'I');