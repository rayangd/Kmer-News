<?php
require_once __DIR__ . '/bootstrap.php';

$categorySlug = $_GET['category'] ?? null;
$limit = isset($_GET['limit']) ? max(1, min(60, (int)$_GET['limit'])) : 20;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$mode = $_GET['mode'] ?? 'recent'; // recent | popular

if ($categorySlug) {
    $cat = getCategoryBySlug($pdo, $categorySlug);
    if (!$cat) {
        jsonResponse(['success' => false, 'message' => 'Rubrique introuvable.'], 404);
    }
    $total = countArticlesByCategory($pdo, (int)$cat['id']);
    $articles = getArticlesByCategory($pdo, (int)$cat['id'], $limit, $offset);
    jsonResponse([
        'success' => true, 'category' => $cat, 'articles' => $articles,
        'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => (int)ceil($total / $limit)],
    ]);
}

if ($mode === 'popular') {
    jsonResponse(['success' => true, 'articles' => getPopularArticles($pdo, $limit)]);
}

// Par défaut : tous les articles récents, regroupés par catégorie (pour la page d'accueil)
$categories = getCategories($pdo);
$grouped = [];
foreach ($categories as $cat) {
    $grouped[] = [
        'category' => $cat,
        'articles' => getArticlesByCategory($pdo, (int)$cat['id'], 3),
    ];
}
jsonResponse(['success' => true, 'grouped' => $grouped]);
