<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

if (!isset($_POST['training_id'])) {
    header('Location: ' . BASE_URL . '/trainings.php');
    exit;
}

$training_id = (int)$_POST['training_id'];

try {
    $pdo->beginTransaction();

    $check = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND training_id = ?");
    $check->execute([$_SESSION['user_id'], $training_id]);
    if ($check->fetch()) {
        $pdo->rollBack();
        die("Oled juba registreeritud sellele treeningule.");
    }

    $count = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE training_id = ?");
    $count->execute([$training_id]);
    $current = $count->fetchColumn();

    $max = $pdo->prepare("SELECT max_participants FROM trainings WHERE id = ?");
    $max->execute([$training_id]);
    $limit = $max->fetchColumn();

    if ($current >= $limit) {
        $pdo->rollBack();
        die("Kohad on täis.");
    }

    $insert = $pdo->prepare("INSERT INTO registrations (user_id, training_id, created_at) VALUES (?, ?, NOW())");
    $insert->execute([$_SESSION['user_id'], $training_id]);

    $pdo->commit();
    header('Location: ' . BASE_URL . '/my.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Viga registreerimisel: " . $e->getMessage());
}
