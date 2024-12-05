<?php
    include '../database/db.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAC THAN Bus Lines - Chất lượng là danh dự</title>
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
                        <input type="email" id="loginEmail" placeholder="Nhập email" required>
                        <div id="loginEmailFeedback" class="invalid-feedback">
                            Email không hợp lệ
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="password" id="loginPassword" placeholder="Nhập mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="loginPasswordFeedback" class="invalid-feedback">
                            Vui lòng nhập mật khẩu
                        </div>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Quên mật khẩu</a>
                    </div>
                    <button type="submit" class="submit-btn">Đăng nhập</button>
                </form>

                <!-- Đăng ký -->
                <form id="registerForm" style="display: none;">
                    <div class="input-group">
                        <input type="email" id="email" placeholder="Nhập email" required>
                        <div id="emailFeedback" class="invalid-feedback">
                            Email không hợp lệ
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="password" id="password" placeholder="Nhập mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="passwordFeedback" class="invalid-feedback">
                            Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ và số
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" placeholder="Xác nhận mật khẩu" required>
                        <span class="toggle-password">👁</span>
                        <div id="confirmPasswordFeedback" class="invalid-feedback">
                            Mật khẩu không khớp
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Đăng ký</button>
                </form>

                <!-- Quên mật khẩu -->
                <div id="forgotPasswordForm" style="display: none;">
                    <div class="input-group">
                        <input type="email" id="forgotEmail" placeholder="Nhập email để đặt lại mật khẩu" required>
                        <div id="forgotEmailFeedback" class="invalid-feedback">
                            Email không hợp lệ
                        </div>
                    </div>
                    <button type="button" id="sendResetLinkBtn" class="submit-btn">Gửi liên kết đặt lại mật khẩu</button>
                    <div id="forgotPasswordMessage" class="success-message" style="display: none;">
                        Đã gửi liên kết đặt lại mật khẩu vào email của bạn.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php @include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth.js"></script>

    <script type="module">
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyB1tRbEekMJFb1dvq_Av1hwdp-QWvSUID8",
            authDomain: "datvexekhach-b9b29.firebaseapp.com",
            databaseURL: "https://datvexekhach-b9b29-default-rtdb.firebaseio.com",
            projectId: "datvexekhach-b9b29",
            storageBucket: "datvexekhach-b9b29.firebasestorage.app",
            messagingSenderId: "1085110352840",
            appId: "1:1085110352840:web:a0744a89b4d807cfec5efd",
            measurementId: "G-2MPZ8B9WZJ"
        };

        // Initialize Firebase
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js';
        import { getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword, sendPasswordResetEmail } from 'https://www.gstatic.com/firebasejs/9.0.0/firebase-auth.js';
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Email validation function
        const validateEmail = (email) => {
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailPattern.test(email);
        };

        // Toggle between login and register forms
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

        // Forgot password form
        document.querySelector('.forgot-password a').addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'none';
            registerForm.style.display = 'none';
            document.getElementById('forgotPasswordForm').style.display = 'block';
        });

        // Login form submission
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('loginEmail');
            const password = document.getElementById('loginPassword');
            const emailFeedback = document.getElementById('loginEmailFeedback');
            const passwordFeedback = document.getElementById('loginPasswordFeedback');
            
            if (!validateEmail(email.value)) {
                emailFeedback.style.display = 'block';
            } else {
                emailFeedback.style.display = 'none';
            }

            if (password.value.length < 6) {
                passwordFeedback.style.display = 'block';
            } else {
                passwordFeedback.style.display = 'none';
            }

            if (email.value && password.value.length >= 6) {
                try {
                    const userCredential = await signInWithEmailAndPassword(auth, email.value, password.value);
                    alert('Đăng nhập thành công!');

                    // Store the email in sessionStorage
                    sessionStorage.setItem('userEmail', email.value);

                    // Send the email to the server via AJAX
                    const formData = new FormData();
                    formData.append('userEmail', email.value); // Append the email to the form data

                    const response = await fetch('save_user_session.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        window.location.href = 'index.php';
                        console.log('Email sent to the server and session set successfully');
                    } else {
                        console.log('Error when sending email to the server');
                    }

                } catch (error) {
                    console.error(error);
                    alert('Đã xảy ra lỗi khi đăng nhập!');
                }
            }
        });

        // Register form submission
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const emailFeedback = document.getElementById('emailFeedback');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

            if (!validateEmail(email.value)) {
                emailFeedback.style.display = 'block';
            } else {
                emailFeedback.style.display = 'none';
            }

            if (password.value !== confirmPassword.value) {
                confirmPasswordFeedback.style.display = 'block';
            } else {
                confirmPasswordFeedback.style.display = 'none';
            }

            if (password.value.length < 6) {
                passwordFeedback.style.display = 'block';
            } else {
                passwordFeedback.style.display = 'none';
            }

            if (email.value && password.value.length >= 6 && password.value === confirmPassword.value) {
                try {
                    const userCredential = await createUserWithEmailAndPassword(auth, email.value, password.value);
                    alert('Đăng ký tài khoản thành công!');
                    
                    // Gửi email đến server để lưu vào cơ sở dữ liệu
                    const formData = new FormData();
                    formData.append('email', email.value);

                    const response = await fetch('save_email.php', {
                        method: 'POST',
                        body: formData,
                    });

                    if (response.ok) {
                        window.location.href = 'login.php'; // Điều hướng sau khi lưu email thành công
                    } else {
                        alert('Lỗi khi lưu email vào cơ sở dữ liệu');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Đã xảy ra lỗi khi đăng nhập!');
                }
            }
        });

        // Reset password link
        document.getElementById('sendResetLinkBtn').addEventListener('click', async () => {
            const forgotEmail = document.getElementById('forgotEmail');
            const forgotEmailFeedback = document.getElementById('forgotEmailFeedback');

            if (!validateEmail(forgotEmail.value)) {
                forgotEmailFeedback.style.display = 'block';
            } else {
                forgotEmailFeedback.style.display = 'none';
            }

            if (forgotEmail.value) {
                try {
                    await sendPasswordResetEmail(auth, forgotEmail.value);

                    document.getElementById('forgotPasswordMessage').style.display = 'block';

                    setTimeout(() => {
                        document.getElementById('forgotPasswordForm').style.display = 'none';
                        loginForm.style.display = 'block';
                        forgotEmail.value = '';
                        document.getElementById('forgotPasswordMessage').style.display = 'none';
                    }, 3000);
                } catch (error) {
                    console.error(error);
                    alert('Đã xảy ra lỗi khi gửi liên kết đặt lại mật khẩu!');
                }
            }
        });
    </script>
</body>
</html>
