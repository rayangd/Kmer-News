<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$categories = getCategories($pdo);
$result = [];

foreach ($categories as $cat) {
    $catId = (int)$cat['id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id=:id AND statut='publie'");
    $stmt->execute(['id' => $catId]);
    $articlesPublies = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT auteur_id) FROM articles WHERE categorie_id=:id");
    $stmt->execute(['id' => $catId]);
    $contributeurs = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT tag) FROM articles WHERE categorie_id=:id AND tag IS NOT NULL AND tag != ''");
    $stmt->execute(['id' => $catId]);
    $tagsUtilises = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT id, titre, slug, tag, image, statut, vues, published_at, created_at
         FROM articles WHERE categorie_id = :id
         ORDER BY created_at DESC LIMIT 4"
    );
    $stmt->execute(['id' => $catId]);
    $derniers = $stmt->fetchAll();

    $result[] = [
        'category' => $cat,
        'stats' => [
            'articles_publies' => $articlesPublies,
            'contributeurs' => $contributeurs,
            'tags_utilises' => $tagsUtilises,
        ],
        'derniers_articles' => $derniers,
    ];
}

jsonResponse(['success' => true, 'rubriques' => $result]);
