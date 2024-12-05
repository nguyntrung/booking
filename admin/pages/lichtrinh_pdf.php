<?php

ob_start(); 
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/../../vendor/autoload.php'; 

include '../../database/db.php';

if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? $_GET['id'] : null;


if ($id) {
    $stmt = $conn->prepare("
        SELECT c.MaChuyenXe, t.TenTuyenXe, c.ThoiGianKhoiHanh, c.ThoiGianKetThuc, c.SoChoTrong, 
            x.BienSoXe, nv.TenNV, nv.SDT, c.enableflag
        FROM chuyenxe c
        LEFT JOIN tuyenxe t ON c.Tuyen = t.MaTuyenXe
        LEFT JOIN nhanvien nv ON c.TaiXe = nv.MaNV
        LEFT JOIN xe x ON c.Xe = x.MaXe
        WHERE c.MaChuyenXe=?;
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ttchuyenxe = $result->fetch_assoc();

    $stmtVE = $conn->prepare("
    SELECT v.MaVeXe, v.MaCho, hd.MaHoaDon, hd.TrangThaiThanhToan, c.GiaTien, hk.TenHK, hk.SDT, hk.NamSinh, hk.GioiTinh, hk.CCCD
    FROM ve v
    INNER JOIN hoadon hd ON v.HoaDon = hd.MaHoaDon
    INNER JOIN hanhkhach hk ON hd.HanhKhach = hk.MaHK
    INNER JOIN chuyenxe c ON v.ChuyenXe = c.MaChuyenXe
    WHERE v.ChuyenXe = ? AND v.enableflag = 0 AND hd.enableflag = 0

    "); 
    $stmtVE->bind_param("i", $id); 

    if (!$stmtVE) {
        die("Lỗi prepare SQL: " . $conn->error);
    }
    if ($stmtVE->error) {
        echo "<script>console.error('SQL Error: " . addslashes($stmtVE->error) . "');</script>";
    }
    

    $stmtVE->execute();
    $resultVE = $stmtVE->get_result();
    $chuyenxeList = $resultVE->fetch_all(MYSQLI_ASSOC); 
    if (empty($chuyenxeList)) {
        echo "<script>console.warn('Không có dữ liệu vé xe nào!');</script>";
    }
    echo "<script>console.log(" . json_encode($chuyenxeList) . ");</script>";

    
}else {
    header("Location: lichtrinh_chitiet.php");
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont('dejavusans', '', 12, '', true);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LacThan Bus');
$pdf->SetTitle('Danh sách chuyến xe '.$id);

$pdf->SetHeaderData('', 0, 'LacThan Bus', 'Danh sách chuyến xe '.$ttchuyenxe["TenTuyenXe"], array(0,64,255), array(0,64,128));
$pdf->setHeaderFont(Array('dejavusans', '', 9));
$pdf->setFooterFont(Array('dejavusans', '', 8));

$pdf->AddPage();

// $invoiceDate = new DateTime($ttchuyenxe['ThoiGianKhoiHanh']);
// $formattedDate = $invoiceDate->format('H:i d/m/Y');

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
        <h1>DANH SÁCH VÉ</h1>
        <p>Chuyến: ' .$ttchuyenxe["TenTuyenXe"]. '</p>
        <p>Thời gian khởi hành: ' .$ttchuyenxe["ThoiGianKhoiHanh"]. '</p>
        <p>Tài xế: ' .$ttchuyenxe["TenNV"]. ' - ' .$ttchuyenxe["SDT"]. '</p>
        <p>Xe: ' .$ttchuyenxe["BienSoXe"]. '</p>
    </div>

    <div class="invoice-details">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã vé </th>
                    <th>Tên </th>
                    <th>Mã chỗ </th>
                    <th>Giá tiền </th>
                    <th>Thanh toán </th>
                    <th>SDT </th>
                    <th>Ghi chú </th>
                </tr>
            </thead>
            <tbody>
                ';
                foreach ($chuyenxeList as $chuyenxe) {
                    $html .= '
                    <tr>
                        <td>' . htmlentities($chuyenxe['MaVeXe'], ENT_QUOTES, 'UTF-8') . '</td>
                        <td>' . htmlspecialchars($chuyenxe['TenHK']) . '</td>
                        <td>' . htmlspecialchars($chuyenxe['MaCho']) . '</td>
                        <td>' . htmlspecialchars($chuyenxe['GiaTien']) . '</td>
                        <td>' . htmlspecialchars($chuyenxe['TrangThaiThanhToan']) . '</td>
                        <td>' . htmlspecialchars($chuyenxe['SDT']) . '</td>
                        <td></td>
                    </tr>';
                }
                
            $html= $html.'</tbody>
        </table>
    </div>
</div>';

echo $html; // Dòng này đang xuất HTML ra trình duyệt



$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('DanhSachVeCuaChuyen' . $id . '.pdf', 'I');