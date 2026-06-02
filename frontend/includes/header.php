<?php
$user = utilisateurConnecte();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BudgetCollab</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="frontend/css/style.css">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
    <link rel="stylesheet" href="frontend/css/profile.css">

    <?php if (($_GET['page'] ?? '') === 'budgets'): ?>
    <link rel="stylesheet" href="frontend/css/budgets.css">
<?php endif; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<?php if (($_GET['page'] ?? '') === 'shared-budgets'): ?>
    <link rel="stylesheet" href="frontend/css/shared_budgets.css">
<?php endif; ?>
<?php if (($_GET['page'] ?? '') === 'categories'): ?>
    <link rel="stylesheet" href="frontend/css/categories.css">
<?php endif; ?>

<style>
    .user-profile-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    padding: 8px;
    border-radius: var(--radius-md);
    transition: background 0.15s;
    width: 100%;
    color: inherit; /* ← prevents link color override */
}

.user-profile-btn:hover {
    background: var(--bg-primary);
}

.user-profile-btn:hover .user-name {
    color: var(--green);
}

.user-profile-btn .user-name {
    color: var(--text-primary);
    transition: color 0.15s;
}

.user-profile-btn .user-email {
    color: var(--text-secondary);
}

.user-avatar {
    width: 40px;
    height: 40px;
    min-width: 40px; /* ← prevents squishing */
    border-radius: 50%;
    background: var(--green);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0; /* ← prevents flex from distorting it */
}

.avatar-initials {
    font-size: 13px;
    font-weight: 700;
    color: #04342c;
    line-height: 1;
}
</style>
<body class="dashboard-page">
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">✓</div>
                <span class="logo-text">Budget<span>Collab</span></span>
            </div>
            
            <nav class="sidebar-nav">

                <a href="index.php?page=dashboard"
                class="nav-item <?= ($_GET['page'] ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>

                <a href="index.php?page=transactions"
                class="nav-item <?= ($_GET['page'] ?? '') === 'transactions' ? 'active' : '' ?>">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transactions</span>
                </a>

                <a href="index.php?page=budgets"
                class="nav-item <?= ($_GET['page'] ?? '') === 'budgets' ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Budgets</span>
                </a>

                <a href="index.php?page=shared-budgets"
                class="nav-item <?= ($_GET['page'] ?? '') === 'shared-budgets' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Shared Budgets</span>
                </a>

                <a href="index.php?page=categories"
                class="nav-item <?= ($_GET['page'] ?? '') === 'categories' ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>

                <?php if (estAdmin()): ?>
                    <a href="index.php?page=admin"
                    class="nav-item <?= ($_GET['page'] ?? '') === 'admin' ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>

            </nav>

            <div class="sidebar-user">
                <a href="index.php?page=profile" class="user-profile-btn">
                    <div class="user-avatar">
                        <span class="avatar-initials"><?= strtoupper(
                            substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)
                        ) ?></span>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= nettoyer($user['prenom'] . ' ' . $user['nom']) ?></span>
                        <span class="user-email"><?= nettoyer($user['email']) ?></span>
                    </div>
                </a>
                <a href="index.php?page=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

    <main class="main-content">
            <header class="top-bar">
                <h1 class="page-title"><?= $pageTitle ?? '' ?></h1>
                <div class="top-bar-actions">
                    <button class="notif-btn">
                        <i class="far fa-bell"></i>
                    </button>
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-sun"></i>
                        <div class="toggle-knob"></div>
                    </button>
                    <button class="btn-primary btn-sm" id="newBudgetBtn">
                        <i class="fas fa-plus"></i>
                        New Budget
                    </button>
                </div>
            </header>

            