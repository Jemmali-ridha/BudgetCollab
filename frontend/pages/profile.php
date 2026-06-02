<?php
require_once __DIR__ . '/../includes/header.php';

$user  = $user  ?? [];
$stats = $stats ?? ['budgets' => 0, 'transactions' => 0, 'shared' => 0];
$flash = $flash ?? null;

$initials = strtoupper(
    substr($user['prenom'] ?? 'U', 0, 1) .
    substr($user['nom']    ?? 'U', 0, 1)
);
?>

<?php if (!empty($flash)): ?>
    <div class="flash-message flash-message--<?= htmlspecialchars($flash['type']) ?>">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="profile-layout">

    <!-- ── Left Panel ── -->
    <div class="profile-left">

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-banner"></div>
            <div class="profile-avatar-wrap">
                <div class="profile-avatar"><?= $initials ?></div>
            </div>
            <div class="profile-body">
                <div class="profile-name">
                    <?= nettoyer(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                </div>
                <div class="profile-email"><?= nettoyer($user['email'] ?? '') ?></div>
                <div class="profile-divider"></div>
                <div class="profile-stats">
                    <div>
                        <div class="profile-stat-value"><?= (int)($stats['budgets'] ?? 0) ?></div>
                        <div class="profile-stat-label">Budgets</div>
                    </div>
                    <div>
                        <div class="profile-stat-value"><?= (int)($stats['transactions'] ?? 0) ?></div>
                        <div class="profile-stat-label">Transactions</div>
                    </div>
                    <div>
                        <div class="profile-stat-value"><?= (int)($stats['shared'] ?? 0) ?></div>
                        <div class="profile-stat-label">Shared</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="security-card">
            <div class="security-card__title">Security Settings</div>
            <div class="security-row">
                <i class="fas fa-shield-alt"></i>
                <span class="security-label">Two-Factor Auth</span>
                <input type="checkbox" class="security-checkbox" id="twoFactor">
            </div>
            <div class="security-row">
                <i class="fas fa-eye"></i>
                <span class="security-label">Login Alerts</span>
                <input type="checkbox" class="security-checkbox" id="loginAlerts" checked>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="danger-card">
            <div class="danger-title">Danger Zone</div>
            <div class="danger-desc">Once you delete your account, there is no going back. Please be certain.</div>
                <form method="POST" action="index.php?page=profile&action=delete"
                    onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label>Confirm your password to delete</label>
                        <input type="password" name="confirm_password" class="form-input"
                            placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete Account
                    </button>
                </form>
        </div>

    </div>

    <!-- ── Right Panel ── -->
    <div class="profile-right">

        <!-- Personal Information -->
        <div class="form-card">
            <div class="form-card__title">Personal Information</div>
            <form method="POST" action="index.php?page=profile&action=update">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="prenom" class="form-input"
                               value="<?= nettoyer($user['prenom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="nom" class="form-input"
                               value="<?= nettoyer($user['nom'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Email</label>
                        <div class="form-input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-input"
                                   value="<?= nettoyer($user['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <div class="form-input-icon">
                            <i class="fas fa-user-shield"></i>
                            <input type="text" class="form-input"
                                value="<?= nettoyer($user['nom_role'] ?? $_SESSION['role'] ?? 'user') ?>"
                                disabled>
                        </div>
                    </div>
                </div>

                

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="form-card">
            <div class="form-card__title">Change Password</div>
            <form method="POST" action="index.php?page=profile&action=password">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="password-grid">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-input"
                               placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-input"
                               placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-input"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-lock"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>