<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private Category $model;

    public function __construct()
    {
        $this->model = new Category();
    }

    public function show(): void
    {
        requiertConnexion();

        $userId     = $_SESSION['user_id'];
        $categories = $this->model->all();
        $customs    = $this->model->getCustomCategories($userId);
        $spending   = $this->model->getSpendingByCategory($userId);
        $mostActive = $this->model->getMostActiveThisWeek($userId);

        $spending = array_filter($spending, fn($c) => (float)$c['total'] > 0);
        $spending = array_values($spending);

        $sortedLabels = array_column($spending, 'nom_categorie');
        $sortedValues = array_column($spending, 'total');
        $sortedColors = array_column($spending, 'couleur');

        $topCat = !empty($spending) ? $spending[0] : null;
        $lowCat = !empty($spending) ? end($spending)  : null;

        $flash     = getFlash();
        $pageTitle = "Categories";

        require_once __DIR__ . '/../../frontend/pages/categories.php';
    }

    public function create(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token. Please try again.');
            header('Location: index.php?page=categories');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $id_createur = $_SESSION['user_id'] ?? null;

        if (empty($name)) {
            flashMessage('danger', 'Category name is required.');
            header('Location: index.php?page=categories');
            exit;
        }

        $this->model->create($name, $id_createur ?: null);

        flashMessage('success', 'Category created successfully.');
        header('Location: index.php?page=categories');
        exit;
    }

    public function update(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token. Please try again.');
            header('Location: index.php?page=categories');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            flashMessage('danger', 'Category name is required.');
            header("Location: index.php?page=categories&action=edit&id=$id");
            exit;
        }

        $this->model->update($id, $name, $description ?: null);

        flashMessage('success', 'Category updated successfully.');
        header('Location: index.php?page=categories');
        exit;
    }

    public function delete(): void
    {
        requiertConnexion();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            flashMessage('danger', 'Invalid category ID.');
            header('Location: index.php?page=categories');
            exit;
        }

        $this->model->delete($id);

        flashMessage('success', 'Category deleted successfully.');
        header('Location: index.php?page=categories');
        exit;
    }
}