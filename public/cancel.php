<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . '/my.php');
    exit;
}

$reg_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT r.id, t.date, t.time 
    FROM registrations r 
    JOIN trainings t ON r.training_id = t.id 
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reg_id, $_SESSION['user_id']]);
$reg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reg) die("Registreeringut ei leitud.");

$start = new DateTime($reg['date'].' '.$reg['time']);
$now = new DateTime();
$diff = $start->getTimestamp() - $now->getTimestamp();
if ($diff < 7200) die("Tühistamine pole enam lubatud (vähem kui 2 tundi alguseni).");

$del = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
$del->execute([$reg_id]);
header('Location: ' . BASE_URL . '/my.php');
exit;
