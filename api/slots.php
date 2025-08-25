<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'missing id']);
    exit;
}
$training_id = (int)$_GET['id'];

$count = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE training_id = ?");
$count->execute([$training_id]);
$current = (int)$count->fetchColumn();

$max = $pdo->prepare("SELECT max_participants FROM trainings WHERE id = ?");
$max->execute([$training_id]);
$limit = (int)$max->fetchColumn();

echo json_encode([
    'current' => $current,
    'limit' => $limit,
    'available' => max(0, $limit - $current)
]);
