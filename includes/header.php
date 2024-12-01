<?php session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../views">
            <img src="../assets/img/logo.png" alt="FUTA Bus Lines">
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
                    <a class="nav-link fw-bold" href="#">LỊCH TRÌNH</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="#">TRA CỨU VÉ</a>
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
                <li class="nav-item ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Nếu người dùng đã đăng nhập, hiển thị tên người dùng -->
                        <a href="#" class="btn btn-sm btn-light">
                            <i class="nav-link fas fa-user me-1"></i>
                            Xin chào, <?= $_SESSION['user']; ?>
                        </a>    
                    <?php else: ?>
                        <!-- Nếu chưa đăng nhập, hiển thị nút đăng nhập/đăng ký -->
                        <a href="../views/login.php" class="btn btn-sm btn-light rounded-pill">
                            <i class="nav-link fas fa-user me-1"></i>
                            Đăng nhập/Đăng ký
                        </a>
                    <?php endif; ?>
                </li>
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
