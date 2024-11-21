<!-- index.php -->
 <?php
    include '../database/db.php'
 ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FUTA Bus Lines - Chất lượng là danh dự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <?php @include '../includes/header.php'; ?>
    <div class="container login">
        <div class="header m-0">
            <div class="brand">
                <h1>LAC THAN BUS</h1>
                <p>Cùng bạn trên mọi nẻo đường</p>
            </div>
            <div class="nav-tabs">
                <a href="#" class="nav-tab active" data-tab="login">ĐĂNG NHẬP</a>
                <a href="#" class="nav-tab" data-tab="register">ĐĂNG KÝ</a>
            </div>
        </div>

        <div class="content">
            <div class="illustration">
                <img src="https://cdn.futabus.vn/futa-busline-cms-dev/TVC_00aa29ba5b/TVC_00aa29ba5b.svg" alt="Bus illustration">
            </div>

            <div class="form-container">
                <!-- Đăng nhập -->
                <form id="loginForm" style="display: block;">
                    <div class="input-group">
                        <input type="tel" placeholder="Nhập số điện thoại" required>
                    </div>
                    <div class="input-group">
                        <input class="rounded" type="password" placeholder="Nhập mật khẩu" required>
                        <span class="toggle-password">👁</span>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Quên mật khẩu</a>
                    </div>
                    <button type="submit" class="submit-btn">Đăng nhập</button>
                </form>

                <!-- Đăng ký -->
                <form id="registerForm" style="display: none;">
                    <div class="input-group mb-2">
                        <input class="rounded" type="tel" id="phone" placeholder="Nhập số điện thoại" required>
                        <div id="phoneFeedback" class="invalid-feedback">
                            Số điện thoại không hợp lệ
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input class="rounded" type="password" id="password" placeholder="Nhập mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="passwordFeedback" class="invalid-feedback">
                            Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ và số.
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input class="rounded" type="password" id="confirmPassword" placeholder="Xác nhận mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="confirmPasswordFeedback" class="invalid-feedback">
                            Mật khẩu không khớp
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Tiếp tục</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const navTabs = document.querySelectorAll('.nav-tab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        navTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                navTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                if (tab.dataset.tab === 'login') {
                    loginForm.style.display = 'block';
                    registerForm.style.display = 'none';
                } else {
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'block';
                }
            });
        });

        const togglePassword = document.querySelectorAll('.toggle-password');
        togglePassword.forEach(span => {
            span.addEventListener('click', () => {
                const input = span.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
            });
        });

        // Validate phone number
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', () => {
            const phone = phoneInput.value;
            const phonePattern = /^[0-9]{10}$/;
            const phoneFeedback = document.getElementById('phoneFeedback');
            if (!phonePattern.test(phone)) {
                phoneFeedback.style.display = 'block';
            } else {
                phoneFeedback.style.display = 'none';
            }
        });

        // Validate password
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,}$/;
            const passwordFeedback = document.getElementById('passwordFeedback');
            if (!passwordPattern.test(password)) {
                passwordFeedback.style.display = 'block';
            } else {
                passwordFeedback.style.display = 'none';
            }
        });

        // Validate confirm password
        const confirmPasswordInput = document.getElementById('confirmPassword');
        confirmPasswordInput.addEventListener('input', () => {
            const confirmPassword = confirmPasswordInput.value;
            const password = passwordInput.value;
            const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');
            if (confirmPassword !== password) {
                confirmPasswordFeedback.style.display = 'block';
            } else {
                confirmPasswordFeedback.style.display = 'none';
            }
        });

        // Submit form
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // You can proceed with the form submission logic here if everything is valid
            const phoneFeedback = document.getElementById('phoneFeedback').style.display;
            const passwordFeedback = document.getElementById('passwordFeedback').style.display;
            const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback').style.display;

            if (phoneFeedback === 'none' && passwordFeedback === 'none' && confirmPasswordFeedback === 'none') {
                alert('Đăng ký thành công!');
            } else {
                alert('Vui lòng sửa lỗi và thử lại.');
            }
        });
    </script>

    <?php @include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
