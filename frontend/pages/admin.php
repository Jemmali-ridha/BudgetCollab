<?php
require_once __DIR__ . '/../includes/header.php';

function avatarColor(string $name): string
{
    $colors = ['#10B981','#6366F1','#F59E0B','#EF4444','#06B6D4','#8B5CF6','#EC4899'];
    return $colors[abs(crc32($name)) % count($colors)];
}

function initiales(string $prenom, string $nom): string
{
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

function roleBadge(string $role): string
{
    $map = [
        'admin'        => ['Admin',     'admin'],
        'moderator'    => ['Moderator', 'moderator'],
        'utilisateur'  => ['User',      'user'],
    ];
    [$label, $cls] = $map[$role] ?? ['User', 'user'];
    return "<span class=\"role-badge role-badge--{$cls}\">{$label}</span>";
}

function statusBadge(string $role, string $dateCreation): string
{
    $isPending = ($role === 'utilisateur')
        && (strtotime($dateCreation) > strtotime('-24 hours'));

    if ($isPending) {
        return '<span class="status-badge status-badge--pending">Pending</span>';
    }
    return '<span class="status-badge status-badge--active">Active</span>';
}

function formatAmount(float $amount): string
{
    if ($amount >= 1000) {
        return '$' . number_format($amount / 1000, 1) . 'K';
    }
    return '$' . number_format($amount, 0);
}

function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)       return $diff . 's ago';
    if ($diff < 3600)     return floor($diff / 60)  . ' min ago';
    if ($diff < 86400)    return floor($diff / 3600) . ' hr ago';
    return floor($diff / 86400) . ' days ago';
}
?>

<link rel="stylesheet" href="frontend/css/admin.css">

<?php if ($flash): ?>
<div class="flash-message flash-message--<?= nettoyer($flash['type']) ?>">
    <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
    <?= nettoyer($flash['message']) ?>
</div>
<?php endif; ?>

<div class="admin-stats">

    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--green">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-card__value"><?= number_format($stats['total_users']) ?></div>
        <div class="stat-card__label">Total Users</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--amber">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-card__value"><?= formatAmount($stats['total_revenue']) ?></div>
        <div class="stat-card__label">Total Revenue</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--indigo">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-card__value"><?= number_format($stats['active_budgets']) ?></div>
        <div class="stat-card__label">Active Budgets</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--teal">
            <i class="fas fa-activity"></i>
        </div>
        <div class="stat-card__value"><?= number_format($stats['transactions_today']) ?></div>
        <div class="stat-card__label">Transactions Today</div>
    </div>

</div>

<div class="admin-layout">

    <div class="panel-card">
        <div class="panel-header">User Management</div>

        <div class="users-table-header">
            <span class="col-user">User</span>
            <span class="col-role">Role</span>
            <span class="col-status">Status</span>
            <span class="col-actions">Actions</span>
        </div>

        <div class="users-table">
            <?php if (empty($users)): ?>
                <div class="admin-empty">No users found.</div>
            <?php else: ?>
                <?php foreach ($users as $u):
                    $fullName  = nettoyer($u['prenom'] . ' ' . $u['nom']);
                    $initiales = initiales($u['prenom'], $u['nom']);
                    $color     = avatarColor($fullName);
                    $role      = $u['nom_role'] ?? 'utilisateur';
                    $isPending = ($role === 'utilisateur')
                              && (strtotime($u['date_creation']) > strtotime('-24 hours'));
                    $isCurrentUser = ((int)$u['id_utilisateur'] === (int)$_SESSION['user_id']);
                ?>
                <div class="user-row">
                    <div class="col-user">
                        <div class="user-avatar" style="background: <?= $color ?>;">
                            <?= $initiales ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?= $fullName ?></div>
                            <div class="user-email"><?= nettoyer($u['email']) ?></div>
                        </div>
                    </div>

                    <div class="col-role">
                        <?= roleBadge($role) ?>
                    </div>

                    <div class="col-status">
                        <?= statusBadge($role, $u['date_creation']) ?>
                    </div>

                    <div class="col-actions">
                        <?php if ($isPending && !$isCurrentUser): ?>
                            <form method="POST" action="index.php?page=admin" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action"  value="approve_user">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id_utilisateur'] ?>">
                                <button type="submit" class="action-btn approve" title="Approve user">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="index.php?page=admin" style="display:inline;"
                                  onsubmit="return confirm('Reject and delete this user?')">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action"  value="delete">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id_utilisateur'] ?>">
                                <button type="submit" class="action-btn reject" title="Reject user">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        <?php elseif (!$isCurrentUser): ?>
                            <form method="POST" action="index.php?page=admin" style="display:inline;"
                                  onsubmit="return confirm('Delete this user? This action cannot be undone.')">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action"  value="delete">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id_utilisateur'] ?>">
                                <button type="submit" class="action-btn delete" title="Delete user">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <span style="font-size:0.75rem; color:var(--text-secondary);">You</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-right">

        <div class="panel-card">
            <div class="panel-header">Recent Activity</div>

            <?php if (empty($recentActivity)): ?>
                <div class="admin-empty">No recent activity.</div>
            <?php else: ?>
                <?php foreach ($recentActivity as $item):
                    $name = nettoyer($item['prenom'] . ' ' . $item['nom']);
                    if ($item['event_type'] === 'transaction') {
                        $type   = $item['type_transaction'] === 'revenu' ? 'added revenue' : 'added expense';
                        $detail = '$' . number_format((float)$item['montant'], 2);
                        $icon   = $item['type_transaction'] === 'revenu' ? 'fa-arrow-up' : 'fa-arrow-down';
                    } else {
                        $type   = 'joined the app';
                        $detail = '';
                        $icon   = 'fa-user-plus';
                    }
                ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="activity-content">
                        <strong><?= $name ?></strong>
                        <?= $type ?>
                        <?php if ($detail): ?>
                            <strong><?= $detail ?></strong>
                        <?php endif; ?>
                        <?php if (!empty($item['budget_name'])): ?>
                            in <em><?= nettoyer($item['budget_name']) ?></em>
                        <?php endif; ?>
                    </div>
                    <div class="activity-time">
                        <?= timeAgo($item['date_creation']) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="panel-card">
            <div class="panel-header">Shared Budgets</div>

            <?php if (empty($sharedBudgets)): ?>
                <div class="admin-empty">No shared budgets yet.</div>
            <?php else: ?>
                <?php foreach ($sharedBudgets as $b):
                    $spent   = (float) $b['total_depense'];
                    $limit   = (float) ($b['total_limit'] ?? 0);
                    $pct     = $limit > 0 ? min(round(($spent / $limit) * 100), 100) : 0;
                    $isOver  = $b['end_date'] < date('Y-m-d');
                    $statusLabel = $isOver  ? 'On Hold'  : 'Active';
                    $statusCls   = $isOver  ? 'on-hold'  : 'active';
                ?>
                <div class="shared-budget-item">
                    <div class="budget-info">
                        <div class="budget-name"><?= nettoyer($b['budget_name']) ?></div>
                        <div class="budget-meta">
                            <i class="fas fa-users"></i>
                            <?= (int)$b['nb_membres'] ?> members
                            &nbsp;·&nbsp;
                            <?= formatAmount($spent) ?>
                            <?php if ($limit > 0): ?>
                                / <?= formatAmount($limit) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="budget-status">
                        <span class="status-badge status-badge--<?= $statusCls === 'active' ? 'active' : 'pending' ?>"
                              style="<?= $isOver ? 'background:rgba(245,158,11,.14);color:var(--amber);' : '' ?>">
                            <?= $statusLabel ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="frontend/js/admin.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>