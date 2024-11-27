<?php
session_start();

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Lấy dữ liệu doanh thu theo tháng
$stmtMonth = $conn->prepare("
    SELECT 
        MONTH(NgayLap) AS Thang,
        SUM(TongTien) AS TongTien
    FROM hoadon
    GROUP BY MONTH(NgayLap)
    ORDER BY Thang ASC
");
$stmtMonth->execute();
$resultMonth = $stmtMonth->get_result();
$chartDataMonth = $resultMonth->fetch_all(MYSQLI_ASSOC);

// Lấy dữ liệu doanh thu theo ngày nếu chọn khoảng ngày
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if ($startDate && $endDate) {
    $stmtDay = $conn->prepare("
        SELECT 
            DATE(NgayLap) AS Ngay,
            SUM(TongTien) AS TongTien
        FROM hoadon
        WHERE DATE(NgayLap) BETWEEN ? AND ?
        GROUP BY DATE(NgayLap)
        ORDER BY Ngay ASC
    ");
    $stmtDay->bind_param('ss', $startDate, $endDate);
    $stmtDay->execute();
    $resultDay = $stmtDay->get_result();
    $chartDataDay = $resultDay->fetch_all(MYSQLI_ASSOC);
} else {
    $chartDataDay = [];
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Biểu đồ doanh thu</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Thư viện CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'sidebar.php'; ?>
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Form chọn khoảng ngày -->
                        

                        <!-- Biểu đồ doanh thu theo tháng -->
                        <div class="card mt-4">
                            <h5 class="card-header">Biểu đồ doanh thu theo tháng</h5>
                            <div class="card-body">
                                <canvas id="monthlyRevenueChart"></canvas>
                            </div>
                        </div>
						<div class="card mt-4">
                            <h5 class="card-header">Chọn khoảng ngày</h5>
                            <div class="card-body">
                                <form method="GET" action="">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Từ ngày:</label>
                                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">Đến ngày:</label>
                                            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary w-100">Xem</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Biểu đồ doanh thu theo ngày -->
                        <?php if (!empty($chartDataDay)) : ?>
                        <div class="card mt-4">
                            <h5 class="card-header">Biểu đồ doanh thu theo ngày</h5>
                            <div class="card-body">
                                <canvas id="dailyRevenueChart"></canvas>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Thư viện Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Dữ liệu biểu đồ theo tháng từ PHP
        const chartDataMonth = <?php echo json_encode($chartDataMonth); ?>;
        const monthLabels = chartDataMonth.map(data => `Tháng ${data.Thang}`);
        const monthDataPoints = chartDataMonth.map(data => data.TongTien);

        // Vẽ biểu đồ doanh thu theo tháng
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Doanh thu theo tháng (VNĐ)',
                    data: monthDataPoints,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw.toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });

        // Dữ liệu biểu đồ theo ngày từ PHP
        const chartDataDay = <?php echo json_encode($chartDataDay); ?>;
        if (chartDataDay.length > 0) {
            const dayLabels = chartDataDay.map(data => data.Ngay);
            const dayDataPoints = chartDataDay.map(data => data.TongTien);

            // Vẽ biểu đồ doanh thu theo ngày
            const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
            const dailyRevenueChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Doanh thu theo ngày (VNĐ)',
                        data: dayDataPoints,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.raw.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>
