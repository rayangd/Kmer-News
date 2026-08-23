<?php
/**
 * KMER NEWS - Fonctions utilitaires
 */

/** Échapper une sortie HTML (anti-XSS) */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Générer un slug propre à partir d'un texte */
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'article';
}

/** Rendre un slug unique dans la table articles */
function uniqueArticleSlug(PDO $pdo, string $base, ?int $excludeId = null): string
{
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM articles WHERE slug = :slug" . ($excludeId ? " AND id != :id" : "");
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        if ($excludeId) {
            $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $original . '-' . $i;
        $i++;
    }
}

/** Formater une date en français */
function formatDateFr(?string $datetime): string
{
    if (!$datetime) return '';
    $mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $ts = strtotime($datetime);
    return (int)date('j', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

/** Temps écoulé relatif (ex: "il y a 2h") */
function timeAgo(?string $datetime): string
{
    if (!$datetime) return '';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return "à l'instant";
    if ($diff < 3600) return "il y a " . floor($diff / 60) . " min";
    if ($diff < 86400) return "il y a " . floor($diff / 3600) . " h";
    if ($diff < 604800) return "il y a " . floor($diff / 86400) . " j";
    return formatDateFr($datetime);
}

/** Extrait sécurisé de texte */
function excerpt(string $text, int $length = 150): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

/** Redirection avec arrêt immédiat */
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

/** Génère (ou récupère) le jeton CSRF de la session */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Vérifie le jeton CSRF envoyé en POST */
function checkCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Requête invalide (jeton de sécurité manquant ou expiré). Merci de recharger la page.');
    }
}

/** Champ input caché CSRF prêt à l'emploi dans un <form> */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/** Valide un mot de passe (8 caractères minimum) */
function isPasswordValid(string $password): bool
{
    return strlen($password) >= 8;
}

/** Message flash (succès/erreur) affiché une seule fois */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
