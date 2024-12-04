<?php
// Kết nối với database
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "datvexekhach";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và xử lý ký tự đặc biệt
    $dichvu = htmlspecialchars(trim($_POST["DichVu"]));
    $hoten = htmlspecialchars(trim($_POST["HoTen"]));
    $email = htmlspecialchars(trim($_POST["Email"]));
    $sdt = htmlspecialchars(trim($_POST["SDT"]));
    $tieude = htmlspecialchars(trim($_POST["TieuDe"]));
    $noidung = htmlspecialchars(trim($_POST["NoiDung"]));

    $emailPattern = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    
     // Kiểm tra tính hợp lệ của dữ liệu
     if (!empty($hoten) && !empty($email) && !empty($noidung) && !empty($sdt)) {
        // Kiểm tra email hợp lệ
        if (!preg_match($emailPattern, $email)) {
            echo "<div class='alert alert-warning'>Địa chỉ email không hợp lệ.</div>";
        } 
        // Kiểm tra số điện thoại chỉ chứa ký tự số
        elseif (!ctype_digit($sdt)) {
            echo "<div class='alert alert-warning'>Số điện thoại phải là số và không chứa ký tự chữ.</div>";
        } 
        else {

            $sql = "INSERT INTO lienhe (DichVu, HoTen, Email, SDT, TieuDe, NoiDung) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $dichvu, $hoten, $email, $sdt, $tieude, $noidung);

            if ($stmt->execute()) {
                echo "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ sớm liên lạc lại với bạn.";
            } else {
                echo "Đã xảy ra lỗi khi gửi tin nhắn: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "Vui lòng điền đầy đủ thông tin.";
    }
} else {
    echo "Phương thức truy cập không hợp lệ.";
}

$conn->close();

?>