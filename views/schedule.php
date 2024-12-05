<?php
// Kết nối cơ sở dữ liệu
include '../database/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch trình các tuyến xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>

    <div class="container py-5">
        <div class="col-md-8" style="margin: 0 auto;">
            <div class="card-header text-center">
                <h4 class="mb-0">LỊCH TRÌNH</h4>
            </div>
            <div class="card-body">
                
            </div>
        </div>

        <?php



    



  

        // Truy vấn SQL để lấy dữ liệu các tuyến xe

        $sql1 = "SELECT * from tuyenxe where enableflag=0 ";

        $stmt1 = $conn->prepare($sql1);

        $stmt1->execute();

        $result1 = $stmt1->get_result();

        



        if ($result1->num_rows > 0) {

            echo "<div class='table-responsive mt-4'>";

            echo "<table class='table table-bordered'>";

            echo "<thead class='table-primary text-center'>";

            echo "<tr>

                    <th>Tuyến xe</th>
                    <th>Quãng đường (km)</th>
                    <th>Bến đi</th>
                    <th>Bến đến</th>


                  </tr>";

            echo "</thead><tbody>";



            while ($row = $result1->fetch_assoc()) {

                echo "<tr>";

                echo "<td>" . $row['TenTuyenXe'] . "</td>";
                echo "<td class='text-center'>" . $row['KhoangCach'] . "</td>";

                $sql2 = "SELECT * from benxe where enableflag=0 ";

                $stmt2 = $conn->prepare($sql2);

                $stmt2->execute();

                $result2 = $stmt2->get_result();
                while ($row2 = $result2->fetch_assoc()){
                    if($row2["MaBenXe"]==$row["BenDi"]){
                        echo "<td class='text-center'>" . $row2['TenBenXe'] . "</td>";
                        break;
                    }
                }

                $sql2 = "SELECT * from benxe where enableflag=0 ";

                $stmt2 = $conn->prepare($sql2);

                $stmt2->execute();

                $result2 = $stmt2->get_result();
                while ($row2 = $result2->fetch_assoc()){
                    if($row2["MaBenXe"]==$row["BenDen"]){
                        echo "<td class='text-center'>" . $row2['TenBenXe'] . "</td>";
                        break;
                    }
                }

                

                echo "</tr>";

            }
            echo "</tbody></table></div>";

        } else {

            echo "<div class='alert alert-warning text-center mt-4'>Không tìm thấy lịch trình nào phù hợp.</div>";

        }

        
?>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
