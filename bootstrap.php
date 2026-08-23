<?php
/**
 * KMER NEWS API - Amorçage commun
 * À inclure en tête de chaque fichier api/*.php
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::getConnection();

/** Envoie une réponse JSON et arrête le script */
function jsonResponse(array $data, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Vérifie le jeton CSRF envoyé en en-tête HTTP (X-CSRF-Token) ou en POST */
function checkCsrfApi(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        jsonResponse(['success' => false, 'message' => 'Jeton de sécurité invalide ou expiré. Rechargez la page.'], 403);
    }
}

/** Lit le corps JSON envoyé en POST/PUT (fetch avec Content-Type: application/json) */
function jsonBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/** Bloque si l'utilisateur n'est pas connecté */
function apiRequireLogin(): void
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'message' => 'Connexion requise.'], 401);
    }
}

/** Bloque si l'utilisateur n'est pas administrateur */
function apiRequireAdmin(): void
{
    apiRequireLogin();
    if (!isAdmin()) {
        jsonResponse(['success' => false, 'message' => 'Accès réservé aux administrateurs.'], 403);
    }
}
