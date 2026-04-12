<?php
    require_once __DIR__ . '/../config/config.php';
    include __DIR__ . '/../includes/header.php'; 

    /* page protected
    requireLogin(); 
    if(hasRole('veteran')){
        // show veteran+ content
    }
    */

    $errors = [];
    $success = '';

    // Handle POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($action === 'login') {
            echo '<pre>' . $username . '</pre>';

            // --- LOGIN ---
            if (!$username || !$password) $errors[] = "Username and password required.";

            if (!$errors) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, $user['password'])) {
                    // login success
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    redirect('/../index.php');
                    /* future use
                        redirect('/member_dashboard.php');
                    */
                } else {
                    $errors[] = "Invalid username or password.";
                }
            }
        }

        if ($action === 'register') {
            // --- REGISTRATION ---
            if (!$username || !$email || !$password) $errors[] = "All fields required.";

            if (!$errors) {
                // Check existing user
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username'=>$username,'email'=>$email]);
                if ($stmt->fetch()) {
                    $errors[] = "Username or email already taken.";
                } else {
                    // Insert user
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (:username,:email,:password)");
                    $stmt->execute(['username'=>$username,'email'=>$email,'password'=>$hash]);
                    $success = "Registration successful! You can now log in.";
                }
            }
        }
    }
    ?>

        <body class="container">
            <div class="title-login">
                <?= APP_NAME ?> Login
            </div>
            <?php if($errors): ?>
                <div class="errors">
                    <?php foreach($errors as $err) echo "<p>" . e($err) . "</p>"; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="success"><?= e($success) ?></div>
            <?php endif; ?>
                <!-- LOGIN FORM -->
                <form method="post" id="loginForm" class="auth-form active">
                    <input type="hidden" name="action" value="login">
                    <label>Username</label>
                    <input type="text" name="username" required>
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <button type="submit">Login</button>

                </form>

                <!-- REGISTER FORM -->
                <form method="post" id="registerForm" class="auth-form">
                    <input type="hidden" name="action" value="register">
                    <label>Username</label>
                    <input type="text" name="username" required>
                    <label>Email</label>
                    <input type="email" name="email" required>
                    <label>Password</label>
                    <input type="password" name="password" required>
                    <button type="submit">Register</button>

                </form>
                <!--
                <div class="min-height:20px;"></div>

                <div class="auth-tabs">
                    <button id="showLogin" class="active">Login</button>
                    <button id="showRegister">Register</button>
                </div>
                -->
                <script>
                const loginBtn = document.getElementById('showLogin');
                const registerBtn = document.getElementById('showRegister');
                const loginForm = document.getElementById('loginForm');
                const registerForm = document.getElementById('registerForm');

                function showLogin() {
                    loginForm.style.display = 'block';
                    registerForm.style.display = 'none';
                    loginForm.classList.add('active');
                    registerForm.classList.remove('active');
                    loginBtn.classList.add('active');
                    registerBtn.classList.remove('active');
                }

                function showRegister() {
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'block';
                    loginForm.classList.remove('active');
                    registerForm.classList.add('active');
                    loginBtn.classList.remove('active');
                    registerBtn.classList.add('active');
                }

                // Tab clicks
                loginBtn.addEventListener('click', showLogin);
                registerBtn.addEventListener('click', showRegister);
                document.getElementById('switchToRegister').addEventListener('click', showRegister);
                document.getElementById('switchToLogin').addEventListener('click', showLogin);

                // --- Initial load: login active, registration hidden ---
                showLogin();
                </script>
        </body>

<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/../includes/footer.php';        