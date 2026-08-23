<?php
require_once __DIR__ . '/bootstrap.php';

$user = currentUser();
jsonResponse([
    'success' => true,
    'logged_in' => (bool)$user,
    'user' => $user,
    'csrf_token' => csrfToken(),
]);
