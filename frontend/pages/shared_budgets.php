<?php

require_once __DIR__ . '/../includes/header.php';

function sbBarColor(float $pct): string
{
    if ($pct > 100) return 'red';
    if ($pct >= 90)  return 'amber';
    return 'green';
}

function sbCardAccent(int $index): string
{
    $accents = ['', '--amber', '--indigo', '--red', '', '--amber'];
    return $accents[$index % count($accents)];
}

$sharedBudgets  = $sharedBudgets  ?? [];
$pendingInvites = $pendingInvites ?? [];
$recentActivity = $recentActivity ?? [];
$allUsers = $allUsers ?? [];

// Fonction helper pour la couleur d'avatar
function getAvatarColor($id) {
    $colors = ['#10B981', '#6366F1', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#EC4899'];
    return $colors[$id % count($colors)];
}
?>
    <?php if (!empty($flash)): ?>
        <div class="flash-message flash-message--<?= htmlspecialchars($flash['type']) ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $flash['message'] ?>
        </div>
    <?php endif; ?>

    <?php foreach ($pendingInvites as $invite): ?>
    <div class="invitation-banner">

        <div class="invitation-banner__avatar">
            <?= strtoupper(
                substr($invite['inviter_prenom'] ?? 'U', 0, 1) .
                substr($invite['inviter_nom']    ?? 'U', 0, 1)
            ) ?>
        </div>

        <div class="invitation-banner__body">
            <div class="invitation-banner__title">Budget Invitation</div>
            <div class="invitation-banner__text">
                <strong><?= htmlspecialchars(($invite['inviter_prenom'] ?? '') . ' ' . ($invite['inviter_nom'] ?? '')) ?></strong>
                vous a invité à rejoindre
                <a href="#">"<?= htmlspecialchars($invite['budget_name'] ?? '') ?>"</a>
            </div>
            <div class="invitation-banner__meta">
                <?php if (!empty($invite['total_limit'])): ?>
                <span><i class="fas fa-dollar-sign"></i> $<?= number_format((float)$invite['total_limit'], 0, '.', ',') ?> budget</span>
                <?php endif; ?>
                <?php if (!empty($invite['member_count'])): ?>
                <span><i class="fas fa-users"></i> <?= (int)$invite['member_count'] ?> members</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="invitation-banner__actions">
            <a href="index.php?page=shared-budgets&action=accept-token&token=<?= htmlspecialchars($invite['token']) ?>"
               class="btn-accept">
                <i class="fas fa-check"></i> Accept
            </a>
            <button class="btn-decline" data-decline-invite data-invite-id="<?= (int)($invite['id_invitation'] ?? 0) ?>">
                <i class="fas fa-times"></i> Decline
            </button>
        </div>

    </div>
    <?php endforeach; ?>

    <div class="shared-budgets-header">
        <h2 class="section-title">Your Shared Budgets</h2>
    </div>

    <div class="shared-grid">

        <?php if (empty($sharedBudgets)): ?>
            <div class="shared-empty">
                <i class="fas fa-users"></i>
                <p>No shared budgets yet.<br>Create one or wait for an invitation.</p>
            </div>
        <?php else: ?>
            <?php foreach ($sharedBudgets as $i => $budget):
                $limit   = (float)($budget['total_limit'] ?? 0);
                $spent   = (float)($budget['spent']       ?? 0);
                $pct     = ($limit > 0) ? round(($spent / $limit) * 100, 1) : 0;
                $color   = sbBarColor($pct);
                $accent  = sbCardAccent($i);
                $members = $budget['members'] ?? [];
            ?>
            <div class="shared-card shared-card<?= $accent ?>">

                <div class="shared-card__header">
                    <div class="shared-card__name">
                        <?= htmlspecialchars($budget['budget_name']) ?>
                    </div>
                    <button
                        class="btn-invite-card"
                        data-budget-id="<?= (int)$budget['id_budget'] ?>"
                        data-budget-name="<?= htmlspecialchars($budget['budget_name']) ?>"
                        title="Inviter un membre"
                    >
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <?php if (!empty($members)): ?>
                <div class="member-avatars">
                    <?php
                    $altClasses = ['', '--alt1', '--alt2', '--alt3', '--alt4'];
                    foreach (array_slice($members, 0, 5) as $j => $m):
                        $initials = strtoupper(
                            substr($m['prenom'] ?? 'U', 0, 1) .
                            substr($m['nom']    ?? 'U', 0, 1)
                        );
                    ?>
                    <div class="member-avatar member-avatar<?= $altClasses[$j % count($altClasses)] ?>"
                         title="<?= htmlspecialchars(($m['prenom'] ?? '') . ' ' . ($m['nom'] ?? '')) ?>">
                        <?= $initials ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div>
                    <div class="shared-card__amount">
                        $<?= number_format($spent, 0, '.', ',') ?>
                    </div>
                    <?php if ($limit > 0): ?>
                        <div class="shared-card__limit">of $<?= number_format($limit, 0, '.', ',') ?></div>
                    <?php else: ?>
                        <div class="shared-card__limit">No limit set</div>
                    <?php endif; ?>
                </div>

                <?php if ($limit > 0): ?>
                <div>
                    <div class="progress-bar">
                        <div
                            class="progress-bar__fill progress-bar__fill--<?= $color ?>"
                            style="width: <?= min($pct, 100) ?>%"
                            data-width="<?= min($pct, 100) ?>"
                        ></div>
                    </div>
                    <div class="shared-card__percent"><?= $pct ?>% used</div>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <?php if (!empty($recentActivity)): ?>
    <h2 class="section-title">Recent Activity</h2>

    <div class="activity-list">
        <?php foreach ($recentActivity as $act):
            $initials = strtoupper(
                substr($act['prenom'] ?? 'U', 0, 1) .
                substr($act['nom']    ?? 'U', 0, 1)
            );
            $amount  = (float)($act['montant'] ?? 0);
            $isDebit = ($act['type_transaction'] ?? '') === 'depense';
        ?>
        <div class="activity-item">

            <div class="activity-avatar"><?= $initials ?></div>

            <div class="activity-body">
                <div class="activity-text">
                    <strong><?= htmlspecialchars(($act['prenom'] ?? '') . ' ' . ($act['nom'] ?? '')) ?></strong>
                    <?= htmlspecialchars($act['action_label'] ?? 'added a transaction') ?>
                    <a href="#">"<?= htmlspecialchars($act['budget_name'] ?? '') ?>"</a>
                </div>

                <?php if ($amount > 0): ?>
                <div class="activity-amount <?= $isDebit ? '' : 'activity-amount--positive' ?>">
                    <?= $isDebit ? '−' : '+' ?>$<?= number_format($amount, 2) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($act['description'])): ?>
                <div class="activity-note">
                    <i class="far fa-comment"></i>
                    <?= htmlspecialchars($act['description']) ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="activity-time">
                <?php
                if (!empty($act['date_creation'])) {
                    $diff = time() - strtotime($act['date_creation']);
                    if ($diff < 3600)       echo round($diff / 60) . ' min ago';
                    elseif ($diff < 86400)  echo round($diff / 3600) . ' hours ago';
                    else                    echo round($diff / 86400) . ' days ago';
                }
                ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>


<div id="inviteModal" class="invite-modal" role="dialog" aria-modal="true" aria-labelledby="inviteModalTitle" hidden>
    <div class="invite-modal__backdrop"></div>
    <div class="invite-modal__panel">

        <div class="invite-modal__header">
            <h3 class="invite-modal__title" id="inviteModalTitle">
                <i class="fas fa-user-plus"></i>
                Inviter des membres
            </h3>
            <button class="invite-modal__close" id="inviteModalClose" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="invite-modal__body">
            <p class="invite-modal__desc">
                Sélectionnez les utilisateurs à inviter pour rejoindre le budget
                <strong id="inviteModalBudgetName"></strong>.
            </p>

            <form method="POST" action="index.php?page=shared-budgets&action=invite" id="inviteForm">
                <input type="hidden" name="budget_id" id="inviteModalBudgetId">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="invite-modal__field">
                    <label class="invite-modal__label">
                        <i class="fas fa-users"></i> Utilisateurs à inviter
                    </label>
                    
                    <div class="users-list-container">
                        <div class="users-search">
                            <i class="fas fa-search"></i>
                            <input 
                                type="text" 
                                id="usersSearchInput" 
                                placeholder="Rechercher un utilisateur..."
                                class="users-search-input"
                            >
                        </div>
                        
                        <div class="users-checkbox-list" id="usersCheckboxList">
                            <?php if (empty($allUsers)): ?>
                                <div class="users-empty">Aucun utilisateur trouvé.</div>
                            <?php else: ?>
                                <?php 
                                $currentUserId = $_SESSION['user_id'] ?? 0;
                                foreach ($allUsers as $user): 
                                    if ($user['id_utilisateur'] == $currentUserId) continue;
                                ?>
                                <label class="user-checkbox-item">
                                    <input 
                                        type="checkbox" 
                                        name="users[]" 
                                        value="<?= (int)$user['id_utilisateur'] ?>"
                                        class="user-checkbox"
                                    >
                                    <div class="user-checkbox-avatar" style="background: <?= getAvatarColor($user['id_utilisateur']) ?>;">
                                        <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="user-checkbox-info">
                                        <span class="user-checkbox-name">
                                            <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                                        </span>
                                        <span class="user-checkbox-email">
                                            <?= htmlspecialchars($user['email'] ?? '') ?>
                                        </span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="users-select-actions">
                            <button type="button" class="users-select-all-btn" id="selectAllUsers">
                                <i class="fas fa-check-square"></i> Tout sélectionner
                            </button>
                            <button type="button" class="users-deselect-all-btn" id="deselectAllUsers">
                                <i class="fas fa-square"></i> Tout désélectionner
                            </button>
                        </div>
                    </div>
                </div>

                <div class="invite-modal__actions">
                    <button type="button" class="invite-modal__btn invite-modal__btn--cancel" id="inviteModalCancel">
                        Annuler
                    </button>
                    <button type="submit" class="invite-modal__btn invite-modal__btn--submit">
                        <i class="fas fa-paper-plane"></i> Envoyer les invitations
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>


<script src="frontend/js/shared_budgets.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>