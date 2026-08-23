<?php
require_once __DIR__ . '/bootstrap.php';

$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    jsonResponse(['success' => false, 'message' => 'Article non spécifié.'], 400);
}

$article = getArticleBySlug($pdo, $slug);
if (!$article) {
    jsonResponse(['success' => false, 'message' => 'Article introuvable.'], 404);
}

incrementArticleViews($pdo, (int)$article['id']);
$article['vues']++;

$user = currentUser();
$comments = getApprovedComments($pdo, (int)$article['id']);
$related = getArticlesByCategory($pdo, (int)$article['categorie_id'], 4);
$related = array_values(array_filter($related, fn($a) => $a['id'] != $article['id']));
$related = array_slice($related, 0, 3);

jsonResponse([
    'success' => true,
    'article' => $article,
    'comments' => $comments,
    'comment_count' => count($comments),
    'like_count' => countLikes($pdo, (int)$article['id']),
    'liked_by_me' => $user ? userHasLiked($pdo, (int)$article['id'], (int)$user['id']) : false,
    'related' => $related,
    'popular' => getPopularArticles($pdo, 5),
]);
