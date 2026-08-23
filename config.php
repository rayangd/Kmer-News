<?php
/**
 * KMER NEWS - Configuration globale
 */

// --- Paramètres base de données ---
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'kmernews');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// --- Paramètres généraux du site ---
define('SITE_NAME', 'Kmer News');
define('SITE_SLOGAN', "L'info en temps réel");
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/kmernews');

// --- Sécurité ---
define('MAX_LOGIN_ATTEMPTS', 5);       // tentatives autorisées
define('LOGIN_LOCK_MINUTES', 15);      // durée de blocage après trop de tentatives
define('SESSION_LIFETIME', 60 * 60 * 4); // 4h

// --- Affichage des erreurs (mettre à 0 en production) ---
define('APP_DEBUG', true);
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// --- Fuseau horaire ---
date_default_timezone_set('Africa/Douala');

// --- Session sécurisée ---
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_set_cookie_params(SESSION_LIFETIME);
    session_start();
}
