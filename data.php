<?php


function getCategories(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM categories ORDER BY ordre ASC")->fetchAll();
}

function getCategoryBySlug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
    $stmt->execute(['slug' => $slug]);
    $cat = $stmt->fetch();
    return $cat ?: null;
}

/** Articles publiés d'une catégorie, les plus récents en premier (avec pagination) */
function getArticlesByCategory(PDO $pdo, int $categorieId, int $limit = 3, int $offset = 0): array
{
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.slug AS categorie_slug, c.couleur AS categorie_couleur,
                u.prenom AS auteur_prenom, u.nom AS auteur_nom
         FROM articles a
         JOIN categories c ON c.id = a.categorie_id
         JOIN users u ON u.id = a.auteur_id
         WHERE a.categorie_id = :cid AND a.statut = 'publie'
         ORDER BY a.published_at DESC
         LIMIT :lim OFFSET :off"
    );
    $stmt->bindValue(':cid', $categorieId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countArticlesByCategory(PDO $pdo, int $categorieId): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :cid AND statut = 'publie'");
    $stmt->execute(['cid' => $categorieId]);
    return (int)$stmt->fetchColumn();
}

function getArticleBySlug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.slug AS categorie_slug, c.couleur AS categorie_couleur,
                u.prenom AS auteur_prenom, u.nom AS auteur_nom
         FROM articles a
         JOIN categories c ON c.id = a.categorie_id
         JOIN users u ON u.id = a.auteur_id
         WHERE a.slug = :slug AND a.statut = 'publie'
         LIMIT 1"
    );
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch();
    return $article ?: null;
}

function getArticleById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
         FROM articles a JOIN categories c ON c.id = a.categorie_id
         WHERE a.id = :id LIMIT 1"
    );
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();
    return $article ?: null;
}

function incrementArticleViews(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare("UPDATE articles SET vues = vues + 1 WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

/** Articles les plus populaires (par vues) */
function getPopularArticles(PDO $pdo, int $limit = 5): array
{
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
         FROM articles a JOIN categories c ON c.id = a.categorie_id
         WHERE a.statut = 'publie'
         ORDER BY a.vues DESC LIMIT :lim"
    );
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** Article(s) à la une */
function getFeaturedArticle(PDO $pdo): ?array
{
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
         FROM articles a JOIN categories c ON c.id = a.categorie_id
         WHERE a.statut = 'publie' AND a.a_la_une = 1
         ORDER BY a.published_at DESC LIMIT 1"
    );
    $stmt->execute();
    $a = $stmt->fetch();
    return $a ?: null;
}

/** Recherche plein texte + fallback LIKE */
function searchArticles(PDO $pdo, string $query, int $limit = 20): array
{
    $like = '%' . $query . '%';
    $stmt = $pdo->prepare(
        "SELECT a.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
         FROM articles a JOIN categories c ON c.id = a.categorie_id
         WHERE a.statut = 'publie'
           AND (a.titre LIKE :like OR a.chapo LIKE :like OR a.contenu LIKE :like)
         ORDER BY a.published_at DESC
         LIMIT :lim"
    );
    $stmt->bindValue(':like', $like);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getApprovedComments(PDO $pdo, int $articleId): array
{
    $stmt = $pdo->prepare(
        "SELECT cm.*, u.prenom, u.nom, u.avatar
         FROM comments cm JOIN users u ON u.id = cm.user_id
         WHERE cm.article_id = :aid AND cm.statut = 'approuve'
         ORDER BY cm.created_at DESC"
    );
    $stmt->execute(['aid' => $articleId]);
    return $stmt->fetchAll();
}

function countComments(PDO $pdo, int $articleId): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE article_id = :aid AND statut = 'approuve'");
    $stmt->execute(['aid' => $articleId]);
    return (int)$stmt->fetchColumn();
}

function countLikes(PDO $pdo, int $articleId): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM article_likes WHERE article_id = :aid");
    $stmt->execute(['aid' => $articleId]);
    return (int)$stmt->fetchColumn();
}

function userHasLiked(PDO $pdo, int $articleId, int $userId): bool
{
    $stmt = $pdo->prepare("SELECT 1 FROM article_likes WHERE article_id = :aid AND user_id = :uid");
    $stmt->execute(['aid' => $articleId, 'uid' => $userId]);
    return (bool)$stmt->fetchColumn();
}
