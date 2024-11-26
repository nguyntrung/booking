<?php
    include '../database/db.php';
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
            </div>
        </div>
    </div>

    <?php @include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.20.0/firebase-database.js"></script>

    <!-- Firebase Config -->
    <script>
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
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
    </script>

    <!-- Main Script -->
    <script>
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

        // Toggle password visibility
        const togglePassword = document.querySelectorAll('.toggle-password');
        togglePassword.forEach(span => {
            span.addEventListener('click', () => {
                const input = span.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                span.textContent = type === 'password' ? '👁' : '🔒';
            });
        });

        // Email validation function
        const validateEmail = (email) => {
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailPattern.test(email);
        };

        // Password validation function
        const validatePassword = (password) => {
            const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,}$/;
            return passwordPattern.test(password);
        };

        // Login form validation and submission
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail');
            const password = document.getElementById('loginPassword');
            const emailFeedback = document.getElementById('loginEmailFeedback');
            const passwordFeedback = document.getElementById('loginPasswordFeedback');
            
            let isValid = true;

            // Validate email
            if (!validateEmail(email.value)) {
                emailFeedback.style.display = 'block';
                isValid = false;
            } else {
                emailFeedback.style.display = 'none';
            }

            // Validate password
            if (password.value.length < 6) {
                passwordFeedback.style.display = 'block';
                isValid = false;
            } else {
                passwordFeedback.style.display = 'none';
            }

            if (isValid) {
                try {
                    const userCredential = await auth.signInWithEmailAndPassword(email.value, password.value);
                    const user = userCredential.user;
                    
                    // Store user info
                    localStorage.setItem('userEmail', user.email);
                    localStorage.setItem('userId', user.uid);
                    
                    alert('Đăng nhập thành công!');
                    window.location.href = '../index.php';
                    
                } catch (error) {
                    let errorMessage = 'Đã xảy ra lỗi khi đăng nhập!';
                    
                    switch (error.code) {
                        case 'auth/user-not-found':
                            errorMessage = 'Không tìm thấy tài khoản với email này!';
                            break;
                        case 'auth/wrong-password':
                            errorMessage = 'Mật khẩu không chính xác!';
                            break;
                        case 'auth/invalid-email':
                            errorMessage = 'Email không hợp lệ!';
                            break;
                        case 'auth/user-disabled':
                            errorMessage = 'Tài khoản này đã bị vô hiệu hóa!';
                            break;
                    }
                    
                    alert(errorMessage);
                    console.error('Login error:', error);
                }
            }
        });

        // Register form validation and submission
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const emailFeedback = document.getElementById('emailFeedback');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');
            
            let isValid = true;

            // Validate email
            if (!validateEmail(email.value)) {
                emailFeedback.style.display = 'block';
                isValid = false;
            } else {
                emailFeedback.style.display = 'none';
            }

            // Validate password
            if (!validatePassword(password.value)) {
                passwordFeedback.style.display = 'block';
                isValid = false;
            } else {
                passwordFeedback.style.display = 'none';
            }

            // Validate password match
            if (password.value !== confirmPassword.value) {
                confirmPasswordFeedback.style.display = 'block';
                isValid = false;
            } else {
                confirmPasswordFeedback.style.display = 'none';
            }

            if (isValid) {
                try {
                    // Create user
                    const userCredential = await auth.createUserWithEmailAndPassword(email.value, password.value);
                    const user = userCredential.user;
                    
                    // Create user profile
                    const userProfile = {
                        email: user.email,
                        createdAt: new Date().toISOString(),
                        role: 'user'
                    };
                    
                    await firebase.database().ref('users/' + user.uid).set(userProfile);
                    
                    alert('Đăng ký thành công!');
                    
                    // Auto login after registration
                    // localStorage.setItem('userEmail', user.email);
                    // localStorage.setItem('userId', user.uid);
                    
                    window.location.href = 'login.php';
                    
                } catch (error) {
                    let errorMessage = 'Đã xảy ra lỗi khi đăng ký!';
                    
                    switch (error.code) {
                        case 'auth/email-already-in-use':
                            errorMessage = 'Email này đã được sử dụng!';
                            break;
                        case 'auth/invalid-email':
                            errorMessage = 'Email không hợp lệ!';
                            break;
                        case 'auth/operation-not-allowed':
                            errorMessage = 'Đăng ký tài khoản tạm thời bị vô hiệu hóa!';
                            break;
                        case 'auth/weak-password':
                            errorMessage = 'Mật khẩu phải chứa ít nhất 6 ký tự!';
                            break;
                    }
                    
                    alert(errorMessage);
                    console.error('Registration error:', error);
                }
            }
        });

        // Auth state observer
        auth.onAuthStateChanged((user) => {
            if (user) {
                // User is signed in
                console.log('User is signed in:', user.email);
                
                // Check if user is on login page and redirect if necessary
                if (window.location.pathname.includes('login.php')) {
                    window.location.href = '../index.php';
                }
            } else {
                // User is signed out
                console.log('User is signed out');
                localStorage.removeItem('userEmail');
                localStorage.removeItem('userId');
            }
        });

        // Forgot password handling
        document.querySelector('.forgot-password a').addEventListener('click', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            
            if (!email) {
                alert('Vui lòng nhập email của bạn để đặt lại mật khẩu!');
                return;
            }

            if (!validateEmail(email)) {
                alert('Vui lòng nhập email hợp lệ!');
                return;
            }

            try {
                await auth.sendPasswordResetEmail(email);
                alert('Email đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra hộp thư của bạn!');
            } catch (error) {
                let errorMessage = 'Đã xảy ra lỗi khi gửi email đặt lại mật khẩu!';
                
                switch (error.code) {
                    case 'auth/invalid-email':
                        errorMessage = 'Email không hợp lệ!';
                        break;
                    case 'auth/user-not-found':
                        errorMessage = 'Không tìm thấy tài khoản với email này!';
                        break;
                }
                
                alert(errorMessage);
                console.error('Password reset error:', error);
            }
        });

        // Input validation on typing
        const loginEmail = document.getElementById('loginEmail');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');

        loginEmail.addEventListener('input', () => {
            const feedback = document.getElementById('loginEmailFeedback');
            if (!validateEmail(loginEmail.value)) {
                feedback.style.display = 'block';
            } else {
                feedback.style.display = 'none';
            }
        });

        email.addEventListener('input', () => {
            const feedback = document.getElementById('emailFeedback');
            if (!validateEmail(email.value)) {
                feedback.style.display = 'block';
            } else {
                feedback.style.display = 'none';
            }
        });

        password.addEventListener('input', () => {
            const feedback = document.getElementById('passwordFeedback');
            if (!validatePassword(password.value)) {
                feedback.style.display = 'block';
            } else {
                feedback.style.display = 'none';
            }
            
            // Check confirm password match
            const confirmFeedback = document.getElementById('confirmPasswordFeedback');
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                confirmFeedback.style.display = 'block';
            } else {
                confirmFeedback.style.display = 'none';
            }
        });

        confirmPassword.addEventListener('input', () => {
            const feedback = document.getElementById('confirmPasswordFeedback');
            if (password.value !== confirmPassword.value) {
                feedback.style.display = 'block';
            } else {
                feedback.style.display = 'none';
            }
        });
    </script>
</body>
</html>