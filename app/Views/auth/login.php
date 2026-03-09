<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: #333;
            font-weight: 600;
        }

        .login-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e1e5e9;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 10px;
        }

        #loadingOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            font-size: 1.5rem;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay">Sending email...</div>

    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-user-lock"></i>
            <h2>Document Management System</h2>
            <p class="text-muted">Please sign in to continue</p>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php session()->remove('success'); ?>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
            <?php session()->remove('info'); ?>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php session()->remove('error'); ?>
        <?php endif; ?>

        <!-- Step 1: Username -->
        <?php if ($step === 'username'): ?>
            <form method="post" action="<?= base_url('login') ?>">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Username or Email" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-arrow-right me-2"></i>Continue
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 2: Password -->
        <?php if ($step === 'password'): ?>
            <form method="post" action="<?= base_url('login') ?>">
                <input type="hidden" name="username" value="<?= esc($username) ?>">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
                <div class="text-center mt-3 d-flex justify-content-between">
                    <a href="<?= base_url('reset-password') ?>" class="btn btn-link">Forgot / Reset Password?</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-link">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 3: Reset Password Request -->
        <?php if ($step === 'reset'): ?>
            <form method="post" action="<?= base_url('reset-password') ?>" id="resetForm">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Enter Username or Email" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-envelope me-2"></i>Send Password Reset Token
                    </button>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= base_url('login') ?>" class="btn btn-link">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 4: Token Verification + New Password -->
        <?php if ($step === 'token'): ?>
            <form method="post" action="<?= base_url('verifyToken') ?>">
                <input type="hidden" name="username" value="<?= esc($username) ?>">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="text" name="token" class="form-control" placeholder="Enter Token" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="New Password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="text-center">
                    <small id="matchMessage"></small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-check me-2"></i>Verify & Set Password
                    </button>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= base_url('login') ?>" class="btn btn-link">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading overlay for reset form
            const resetForm = document.getElementById('resetForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (resetForm && loadingOverlay) {
                resetForm.addEventListener('submit', function() {
                    // Show overlay only while submitting
                    loadingOverlay.style.display = 'flex';
                });
            }

            // Password match check (token step)
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const matchMessage = document.getElementById('matchMessage');
            const tokenForm = document.querySelector('form[action*="verifyToken"]');

            if (confirmPassword && newPassword && matchMessage) {
                function checkPasswordMatch() {
                    if (newPassword.value && confirmPassword.value) {
                        if (newPassword.value === confirmPassword.value) {
                            matchMessage.textContent = "Passwords match ✔";
                            matchMessage.style.color = "green";
                            return true;
                        } else {
                            matchMessage.textContent = "Passwords do not match ✖";
                            matchMessage.style.color = "red";
                            return false;
                        }
                    } else {
                        matchMessage.textContent = "";
                        return false;
                    }
                }

                confirmPassword.addEventListener('keyup', checkPasswordMatch);
                newPassword.addEventListener('keyup', checkPasswordMatch);

                // Prevent form submission if passwords don't match
                if (tokenForm) {
                    tokenForm.addEventListener('submit', function(e) {
                        if (!checkPasswordMatch()) {
                            e.preventDefault();
                            alert('Passwords do not match. Please try again.');
                        }
                    });
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>