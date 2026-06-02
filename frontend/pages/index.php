<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BudgetCollab | Collaborative Budget Management</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="frontend\css\style.css">
</head>
<body class="landing-page">
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">✓</div>
                <span class="logo-text">Budget<span class="logo-highlight">Collab</span></span>
            </div>
            <div class="nav-links">
                <a href="index.php?page=login" class="nav-link">Login</a>
                <a href="index.php?page=register" class="btn-primary">Register</a>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="fas fa-sun toggle-icon" id="toggleIcon"></i>
                    <div class="toggle-knob"></div>
                </button>
            </div>
        </div>
    </nav>

    <main class="landing-main">
        <section class="banner-section">
            <div class="banner-badge">
                <i class="fas fa-microchip"></i> New: AI-powered budget insights
            </div>
            <h1 class="banner-title">
                Manage Your Budgets, <span class="highlight-amber">Together</span><br>
                <span class="highlight-green">Effortlessly</span>
            </h1>
            <p class="banner-subtitle">
                Collaborate with your team or family on shared budgets. Track expenses, <br>
                set limits, and achieve your financial goals together.
            </p>
            <div class="banner-buttons">
                <a href="index.php?page=register" class="btn-primary btn-large">Get Started Free</a>
                
            </div>
        </section>

        <div class="features-grid" id="features">
            <div class="feature-card">
                <div class="feature-icon feature-icon--green"><i class="fas fa-chart-line"></i></div>
                <h3>Smart Budget Tracking</h3>
                <p>Set budgets by category, track spending in real-time, and get alerts when you're approaching limits.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-icon--indigo"><i class="fas fa-users"></i></div>
                <h3>Collaborative Features</h3>
                <p>Share budgets with family or team members. See who spent what, when, and stay aligned on financial goals.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-icon--amber"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure & Private</h3>
                <p>Bank-level encryption keeps your financial data safe. We never sell your data to third parties.</p>
            </div>
        </div>
    </main>

    <footer class="landing-footer">
        <div class="footer-content">
            <div class="footer-column">
                <div class="logo">
                    <div class="logo-icon">✓</div>
                    <span class="logo-text">Budget<span class="logo-highlight">Collab</span></span>
                </div>
                <p>Collaborative budget management made simple. Track, share, and achieve your financial goals together.</p>
            </div>
            <div class="footer-column">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact & Social</h4>
                <div class="contact-email">
                    <i class="far fa-envelope"></i>
                    <a href="mailto:support@budgetcollab.com">support@budgetcollab.com</a>
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>© 2026 BudgetCollab. All rights reserved.</p>
        </div>
    </footer>

    <button class="help-fab" aria-label="Help">
        <i class="fas fa-question"></i>
    </button>

    <script src="frontend\js\main.js"></script>
</body>
</html>