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
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-user-lock"></i>
            <h2>Reset Your Password</h2>
            <p class="text-muted">Enter your username/email to request a reset token</p>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <!-- Step 1: Request Reset Token -->
        <?php if ($step === 'reset'): ?>
            <form method="post" action="<?= base_url('resetPasswordRequest') ?>">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Enter Username or Email" required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-envelope me-2"></i>Send Reset Token
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 2: Verify Token + Set New Password -->
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
                        <input type="password" id="newPassword" name="password" class="form-control" placeholder="New Password" required>
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
                        <i class="fas fa-check me-2"></i>Verify & Reset Password
                    </button>
                </div>
            </form>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="<?= base_url('login') ?>" class="btn btn-link">Back to Login</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const matchMessage = document.getElementById('matchMessage');

            if (confirmPassword) {
                confirmPassword.addEventListener('keyup', function() {
                    if (newPassword.value && confirmPassword.value) {
                        if (newPassword.value === confirmPassword.value) {
                            matchMessage.textContent = "Passwords match ✔";
                            matchMessage.style.color = "green";
                        } else {
                            matchMessage.textContent = "Passwords do not match ✖";
                            matchMessage.style.color = "red";
                        }
                    } else {
                        matchMessage.textContent = "";
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>