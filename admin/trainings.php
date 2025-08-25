<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Lisa/muuda/kustuta loogika
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO trainings (name, date, time, duration, max_participants) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['date'], $_POST['time'], $_POST['duration'], $_POST['max']]);
    }
    if (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE trainings SET name=?, date=?, time=?, duration=?, max_participants=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['date'], $_POST['time'], $_POST['duration'], $_POST['max'], $_POST['id']]);
    }
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM trainings WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
}

$trainings = $pdo->query("SELECT * FROM trainings ORDER BY date, time")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Admin – Treeningud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
<h2>Treeningute haldus</h2>
<a href="../logout.php" class="btn btn-secondary mb-3">Logi välja</a>

<h4>Lisa treening</h4>
<form method="post" class="row g-2 mb-4">
    <input type="text" name="name" class="form-control" placeholder="Nimi" required>
    <input type="date" name="date" class="form-control" required>
    <input type="time" name="time" class="form-control" required>
    <input type="number" name="duration" class="form-control" placeholder="Kestus min" required>
    <input type="number" name="max" class="form-control" placeholder="Max osalejaid" required>
    <button type="submit" name="add" class="btn btn-primary">Lisa</button>
</form>

<h4>Olemasolevad treeningud</h4>
<table class="table table-bordered">
    <thead><tr><th>Nimi</th><th>Kuupäev</th><th>Kellaaeg</th><th>Kestus</th><th>Max</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($trainings as $t): ?>
        <tr>
            <form method="post">
                <td><input type="text" name="name" value="<?= htmlspecialchars($t['name']) ?>" class="form-control"></td>
                <td><input type="date" name="date" value="<?= $t['date'] ?>" class="form-control"></td>
                <td><input type="time" name="time" value="<?= $t['time'] ?>" class="form-control"></td>
                <td><input type="number" name="duration" value="<?= $t['duration'] ?>" class="form-control"></td>
                <td><input type="number" name="max" value="<?= $t['max_participants'] ?>" class="form-control"></td>
                <td>
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button type="submit" name="update" class="btn btn-success btn-sm mb-1">Uuenda</button>
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Kustuta</button>
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
