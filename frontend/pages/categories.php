<?php

require_once __DIR__ . '/../includes/header.php';

$categories = $categories ?? [];
$flash      = $flash      ?? null;

/* Séparer défaut / custom */
$defaults = array_filter($categories, fn($c) => !empty($c['est_systeme']));
$customs  = array_filter($categories, fn($c) =>  empty($c['est_systeme']));

$iconMap = [
    'shopping-cart'   => 'fas fa-shopping-cart',
    'car'             => 'fas fa-car',
    'home'            => 'fas fa-home',
    'heart'           => 'fas fa-heart',
    'film'            => 'fas fa-film',
    'book'            => 'fas fa-book',
    'tag'             => 'fas fa-tag',
    'more-horizontal' => 'fas fa-ellipsis-h',
    'gas-station'     => 'fas fa-gas-pump',
    'plane'           => 'fas fa-plane',
    'coffee'          => 'fas fa-coffee',
    'shopping-bag'    => 'fas fa-shopping-bag',
    'briefcase'       => 'fas fa-briefcase',
    'zap'             => 'fas fa-bolt',
    'wifi'            => 'fas fa-wifi',
    'dollar-sign'     => 'fas fa-dollar-sign',
];
function catEmoji(string $icone, array $map): string
{
    return $map[$icone] ?? '📂';
}

   (à remplacer par de vraies données depuis les transactions si disponibles) */
$chartLabels = [];
$chartValues = [];
foreach ($categories as $cat) {
    $chartLabels[] = htmlspecialchars($cat['nom_categorie'] ?? $cat['name'] ?? '');
    $id = (int)($cat['id_categorie'] ?? $cat['id'] ?? 1);
    $chartValues[] = ($cat['total_spent'] ?? ($id * 137 % 1300 + 50));
}
arsort($chartValues); // trier décroissant
$sortedLabels = array_values(array_map(fn($k) => $chartLabels[$k], array_keys($chartValues)));
$sortedValues = array_values($chartValues);
?>

    <?php if (!empty($flash)): ?>
        <div class="flash-message flash-message--<?= htmlspecialchars($flash['type']) ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $flash['message'] ?>
        </div>
    <?php endif; ?>


    <div class="categories-layout">

        <div class="cat-left">

            <div style="margin-bottom: 28px;">
                <div class="cat-section-header">
                    <h2 class="cat-section-title">Default Categories</h2>
                </div>

                <div class="cat-list">
                    <?php if (empty($defaults)): ?>
                        <div class="cat-empty">No default categories found.</div>
                    <?php else: ?>
                        <?php foreach ($defaults as $cat):
                            $name  = htmlspecialchars($cat['nom_categorie'] ?? $cat['name'] ?? '');
                            $icone = $cat['icone'] ?? '';
                            $id    = (int)($cat['id_categorie'] ?? $cat['id'] ?? 0);
                        ?>
                        <div class="cat-row">
                            <div class="cat-icon"><?= catEmoji($icone, $iconMap) ?></div>
                            <div class="cat-info">
                                <div class="cat-name"><?= $name ?></div>
                                <span class="cat-badge cat-badge--default">Default</span>
                            </div>
                            <div class="cat-actions">
                                <button
                                    class="btn-icon"
                                    data-edit-cat="<?= $id ?>"
                                    data-name="<?= $name ?>"
                                    title="Edit"
                                >
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="cat-section-header">
                    <h2 class="cat-section-title">Custom Categories</h2>
                    <button class="btn-primary btn-sm" id="addCategoryBtn">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>

                <div class="cat-list">
                    <?php if (empty($customs)): ?>
                        <div class="cat-empty">
                            No custom categories yet.<br>
                            <small>Click "Add Category" to create one.</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($customs as $cat):
                            $name  = htmlspecialchars($cat['nom_categorie'] ?? $cat['name'] ?? '');
                            $icone = $cat['icone'] ?? '';
                            $id    = (int)($cat['id_categorie'] ?? $cat['id'] ?? 0);
                        ?>
                        <div class="cat-row">
                            <div class="cat-icon"><?= catEmoji($icone, $iconMap) ?></div>
                            <div class="cat-info">
                                <div class="cat-name"><?= $name ?></div>
                                <span class="cat-badge cat-badge--custom">Custom</span>
                            </div>
                            <div class="cat-actions">
                                <button
                                    class="btn-icon"
                                    data-edit-cat="<?= $id ?>"
                                    data-name="<?= $name ?>"
                                    title="Edit"
                                >
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <a
                                    href="index.php?page=categories&action=delete&id=<?= $id ?>"
                                    class="btn-icon btn-icon--danger"
                                    data-confirm="Delete &quot;<?= $name ?>&quot;? This cannot be undone."
                                    title="Delete"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="cat-right">

            <div class="chart-card">
                <div class="chart-card__title">Spending by Category</div>
                <div class="chart-wrap">
                    <canvas
                        id="categoryChart"
                        data-labels='<?= json_encode(array_slice($sortedLabels, 0, 8)) ?>'
                        data-values='<?= json_encode(array_values(array_slice($sortedValues, 0, 8))) ?>'
                    ></canvas>
                </div>
            </div>

            <?php if (!empty($categories)): ?>
            <div class="insights-card">

                <?php
                $topIdx   = array_search(max($sortedValues), $sortedValues);
                $topLabel = $sortedLabels[$topIdx] ?? '—';
                $topVal   = max($sortedValues);

                $lowIdx   = array_search(min($sortedValues), $sortedValues);
                $lowLabel = $sortedLabels[$lowIdx] ?? '—';
                $lowVal   = min($sortedValues);

                $activeLabel = $sortedLabels[0] ?? '—';
                $activeTx    = 8; /* Placeholder — à remplacer avec vrai COUNT */
                ?>

                <div class="insight-row">
                    <div class="insight-icon insight-icon--green">
                        <?= catEmoji('shopping-cart', $iconMap) ?>
                    </div>
                    <span class="insight-label">Top spending category</span>
                    <span class="insight-value insight-value--green">
                        $<?= number_format((float)$topVal, 0, '.', ',') ?>
                    </span>
                </div>

                <div class="insight-row">
                    <div class="insight-icon insight-icon--amber">
                        <?= catEmoji('book', $iconMap) ?>
                    </div>
                    <span class="insight-label">Lowest spending category</span>
                    <span class="insight-value insight-value--amber">
                        $<?= number_format((float)$lowVal, 0, '.', ',') ?>
                    </span>
                </div>

                <div class="insight-row">
                    <div class="insight-icon insight-icon--indigo">
                        <?= catEmoji('film', $iconMap) ?>
                    </div>
                    <span class="insight-label">Most active this week</span>
                    <span class="insight-value insight-value--indigo">
                        <?= $activeTx ?> transactions
                    </span>
                </div>

            </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="modal-overlay" id="categoryModal" role="dialog" aria-modal="true">
        <div class="modal-container">

            <div class="modal-header">
                <h2 id="modalCatTitle">Add Category</h2>
                <button class="modal-close" id="closeCatModal" aria-label="Close">&times;</button>
            </div>

            <form class="modal-form" method="POST" action="index.php?page=categories">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action"     id="catFormAction" value="create">
                <input type="hidden" name="id"         id="catIdInput"    value="">

                <div>
                    <label for="catNameInput">Category Name</label>
                    <input
                        type="text"
                        id="catNameInput"
                        name="name"
                        placeholder="e.g. Gaming, Pet Care…"
                        required
                        autocomplete="off"
                    >
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelCatModal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Save
                    </button>
                </div>
            </form>

        </div>
    </div>


<script src="frontend/js/categories.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>