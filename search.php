<?php
require_once __DIR__ . '/bootstrap.php';

$q = trim($_GET['q'] ?? '');
$results = $q !== '' ? searchArticles($pdo, $q, 30) : [];

jsonResponse(['success' => true, 'query' => $q, 'results' => $results, 'count' => count($results)]);
