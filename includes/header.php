<?php
    session_start(); 
    $email = isset($_SESSION['userEmail']) ? $_SESSION['userEmail'] : '';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../views">
            <img src="../assets/img/logo.png" alt="LacThan Bus">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav d-flex w-100">
                <li class="nav-item">
                    <a class="nav-link active fw-bold" href="../views">TRANG CHỦ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="schedule.php">LỊCH TRÌNH</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="lookup.php">TRA CỨU VÉ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="#">TIN TỨC</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="#">HÓA ĐƠN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="contact.php">LIÊN HỆ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="#">VỀ CHÚNG TÔI</a>
                </li>
                <?php if ($email): ?>
                    <li class="nav-item ms-auto"style="display: flex" >
                        <a class="nav-link fw-bold" href="profile.php">
                            <!-- Xin chào, <?= $email ?> -->
                            <img src="https://cdn-icons-png.flaticon.com/512/4792/4792929.png" alt="avata" style="width: 30px">
                        </a>
                        <a href="logout.php" class="btn btn-sm btn-light rounded-pill">
                            <i class="nav-link fas fa-sign-out-alt me-1 text-warning"></i>
                            Đăng xuất
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-auto">
                        <a href="../views/login.php" class="btn btn-sm btn-light rounded-pill">
                            <i class="nav-link fas fa-user me-1"></i>
                            Đăng nhập/Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navLinks.forEach(link => link.classList.remove('active'));
                link.classList.add('active');
            });
        });
    });
</script>
