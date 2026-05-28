<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - BudgetCollab</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="frontend\css\style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="logo logo-large">
                    <div class="logo-icon">✓</div>
                    <span>Budget<span class="logo-highlight">Collab</span></span>
                </div>
                <p class="auth-tagline">Join the community and manage your finances as a team</p>
                
                <div class="stats-frosted">
                    <div class="stat-item">
                        <i class="fas fa-user-plus"></i>
                        <div>
                            <span class="stat-number">500+</span>
                            <span class="stat-label">New signups/month</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-smile"></i>
                        <div>
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h2>Create an account</h2>
                    <p>Start your financial journey</p>
                </div>

                <form action="#" method="POST" class="auth-form" id="registerForm">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="firstname">
                                <i class="far fa-user"></i>
                                First name
                            </label>
                            <input type="text" id="firstname" name="firstname" required placeholder="John">
                        </div>
                        <div class="form-group">
                            <label for="lastname">
                                <i class="far fa-user"></i>
                                Last name
                            </label>
                            <input type="text" id="lastname" name="lastname" required placeholder="Doe">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="far fa-envelope"></i>
                            Email
                        </label>
                        <input type="email" id="email" name="email" required placeholder="john.doe@example.com">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" required placeholder="••••••••">
                            <button type="button" class="toggle-password" data-target="password">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bars">
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                            </div>
                            <span class="strength-text">Enter a password</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-circle"></i>
                            Confirm password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                    </div>

                    <label class="checkbox">
                        <input type="checkbox" name="terms" required>
                        <span>I accept the <a href="#" class="link">Terms of Use</a> and <a href="#" class="link">Privacy Policy</a></span>
                    </label>

                    <button type="submit" class="btn-primary btn-block">Create account</button>
                </form>

                <div class="auth-divider">
                    <span>or</span>
                </div>

                <button class="btn-google btn-block">
                    <i class="fab fa-google"></i>
                    Sign up with Google
                </button>

                <p class="auth-footer">
                    Already have an account? 
                    <a href="index.php?page=login">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script src="frontend\js\main.js"></script>
</body>
</html>