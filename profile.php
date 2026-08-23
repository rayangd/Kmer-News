<?php
require_once __DIR__ . '/bootstrap.php';
apiRequireLogin();

$user = currentUser();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = :id");
$stmt->execute(['id' => $user['id']]);
$nbComments = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM article_likes WHERE user_id = :id");
$stmt->execute(['id' => $user['id']]);
$nbLikes = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE auteur_id = :id");
$stmt->execute(['id' => $user['id']]);
$nbPropositions = (int)$stmt->fetchColumn();

jsonResponse([
    'success' => true,
    'user' => $user,
    'stats' => ['comments' => $nbComments, 'likes' => $nbLikes, 'propositions' => $nbPropositions],
]);
