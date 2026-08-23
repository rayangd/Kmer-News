<?php
require_once __DIR__ . '/bootstrap.php';
apiRequireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
checkCsrfApi();

$body = jsonBody();
$articleId = (int)($body['article_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if (!getArticleById($pdo, $articleId)) {
    jsonResponse(['success' => false, 'message' => 'Article introuvable.'], 404);
}

if (userHasLiked($pdo, $articleId, $userId)) {
    $stmt = $pdo->prepare("DELETE FROM article_likes WHERE article_id = :a AND user_id = :u");
    $stmt->execute(['a' => $articleId, 'u' => $userId]);
    $liked = false;
} else {
    $stmt = $pdo->prepare("INSERT INTO article_likes (article_id, user_id) VALUES (:a, :u)");
    $stmt->execute(['a' => $articleId, 'u' => $userId]);
    $liked = true;
}

jsonResponse(['success' => true, 'liked' => $liked, 'count' => countLikes($pdo, $articleId)]);
