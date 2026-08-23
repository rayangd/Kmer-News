<?php
/**
 * KMER NEWS - Authentification & gestion des sessions
 */

require_once __DIR__ . '/functions.php';

/** L'utilisateur est-il connecté ? */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/** L'utilisateur connecté est-il administrateur ? */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/** Récupère les infos de l'utilisateur connecté (ou null) */
function currentUser(): ?array
{
    if (!isLoggedIn()) return null;
    static $cache = null;
    if ($cache !== null) return $cache;

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT id, prenom, nom, email, role, avatar, statut, created_at FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || $user['statut'] === 'suspendu') {
        logoutUser();
        return null;
    }
    $cache = $user;
    return $cache;
}

/** Force la connexion : sinon redirige vers la page de connexion */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Merci de vous connecter pour accéder à cette page.');
        redirect(SITE_URL . '/login.php');
    }
}

/** Force le rôle admin : sinon redirige */
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        setFlash('error', "Accès réservé à l'administration.");
        redirect(SITE_URL . '/index.php');
    }
}

/**
 * Anti brute-force : vérifie le nombre de tentatives récentes
 * pour un identifiant + une IP donnés.
 */
function tooManyLoginAttempts(PDO $pdo, string $identifiant, string $ip): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE identifiant = :identifiant AND ip_address = :ip
         AND tentative_at > (NOW() - INTERVAL :minutes MINUTE)"
    );
    $stmt->bindValue(':identifiant', $identifiant);
    $stmt->bindValue(':ip', $ip);
    $stmt->bindValue(':minutes', LOGIN_LOCK_MINUTES, PDO::PARAM_INT);
    $stmt->execute();
    return (int)$stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
}

function recordLoginAttempt(PDO $pdo, string $identifiant, string $ip): void
{
    $stmt = $pdo->prepare("INSERT INTO login_attempts (identifiant, ip_address) VALUES (:identifiant, :ip)");
    $stmt->execute(['identifiant' => $identifiant, 'ip' => $ip]);
}

function clearLoginAttempts(PDO $pdo, string $identifiant, string $ip): void
{
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE identifiant = :identifiant AND ip_address = :ip");
    $stmt->execute(['identifiant' => $identifiant, 'ip' => $ip]);
}

/**
 * Authentifie un utilisateur et ouvre la session.
 * Retourne 'admin', 'user', ou false si échec.
 */
function attemptLogin(string $email, string $password): string|false
{
    $pdo = Database::getConnection();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (tooManyLoginAttempts($pdo, $email, $ip)) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash']) || $user['statut'] === 'suspendu') {
        recordLoginAttempt($pdo, $email, $ip);
        return false;
    }

    clearLoginAttempts($pdo, $email, $ip);

    // Régénère l'ID de session pour éviter la fixation de session
    session_regenerate_id(true);

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['prenom'];

    $stmt = $pdo->prepare("UPDATE users SET derniere_connexion = NOW() WHERE id = :id");
    $stmt->execute(['id' => $user['id']]);

    return $user['role']; // 'admin' ou 'user'
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
