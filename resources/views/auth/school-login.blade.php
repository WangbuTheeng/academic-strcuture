<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Login - Academic Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 25%, #581c87 50%, #7c2d12 75%, #dc2626 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
            z-index: 1;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 16px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        }

        .login-header {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 25%, #6d28d9 50%, #7c2d12 75%, #dc2626 100%);
            color: white;
            padding: 50px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.08)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.06)"/><circle cx="70" cy="70" r="1.2" fill="rgba(255,255,255,0.09)"/></svg>');
            animation: sparkle 8s linear infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        .logo-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255,255,255,0.3), transparent, rgba(255,255,255,0.1));
            z-index: -1;
        }

        .login-body {
            padding: 50px 40px;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap fa-3x text-white"></i>
                </div>
                <h1 class="h3 mb-2 fw-bold">AMS</h1>
                <h2 class="h4 mb-3 fw-normal">System Login</h2>
                <p class="mb-0 opacity-90">Enter your credentials to access the system</p>
            </div>

            <div class="login-body">
                <div class="alert alert-info border-0 mb-4" style="background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%); border-radius: 16px;">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);">
                                <i class="fas fa-info text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-bold text-primary mb-2">Academic Management System</h6>
                            <p class="mb-3 text-muted small">Flexible login system supporting schools, teachers, students, and principals. Enter your credentials to access your account.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 8px; height: 8px; background: #10b981;"></div>
                                    <small class="text-muted">School Login</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 8px; height: 8px; background: #3b82f6;"></div>
                                    <small class="text-muted">Teacher Login</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 8px; height: 8px; background: #8b5cf6;"></div>
                                    <small class="text-muted">Student Login</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 mb-4" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 16px;">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-danger mb-2">Login Error</h6>
                                    @foreach ($errors->all() as $error)
                                        <p class="mb-1 text-danger small">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="login_id" class="form-label fw-semibold text-dark">
                            <i class="fas fa-user me-2 text-primary"></i>Login ID
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e5e7eb;">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input id="login_id" name="login_field" type="text" required
                                   class="form-control border-start-0 shadow-sm"
                                   style="border-radius: 0 12px 12px 0; border-color: #e5e7eb; padding: 12px 16px; font-size: 14px;"
                                   placeholder="Enter your login ID" value="{{ old('login_field') }}">
                        </div>
                        <small class="text-muted">Enter your school ID, teacher ID, student ID, or principal ID</small>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-dark">
                            <i class="fas fa-lock me-2 text-primary"></i>Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e5e7eb;">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="form-control border-start-0 shadow-sm"
                                   style="border-radius: 0 12px 12px 0; border-color: #e5e7eb; padding: 12px 16px; font-size: 14px;"
                                   placeholder="••••••••••">
                        </div>
                        <small class="text-muted">Enter your password</small>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('super-admin.login') }}" class="text-decoration-none fw-semibold" style="color: #6366f1;">
                            Super Admin Access →
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on login_id field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('login_id').focus();
        });

        // Form validation and loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = document.querySelector('button[type="submit"]');
            const loginId = document.getElementById('login_id').value.trim();
            const password = document.getElementById('password').value;

            // Basic validation
            if (!loginId || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Signing in...
            `;

            // Re-enable button after 10 seconds as fallback
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = `
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                `;
            }, 10000);
        });

        // Remove duplicate focus code (already handled above)

        // Add hover effects to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.querySelector('button[type="submit"]');

            submitBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(99, 102, 241, 0.3)';
            });

            submitBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(99, 102, 241, 0.2)';
            });
        });
    </script>
</body>
</html>
