<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.id AS reg_id, t.name, t.date, t.time, t.duration 
    FROM registrations r
    JOIN trainings t ON r.training_id = t.id
    WHERE r.user_id = ?
    ORDER BY t.date, t.time
");
$stmt->execute([$_SESSION['user_id']]);
$registrations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="et">
<head>
<meta charset="UTF-8">
<title>Minu treeningud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
<h2>Minu registreeringud</h2>
<a href="<?=BASE_URL?>/trainings.php" class="btn btn-secondary mb-3">Tagasi treeningute juurde</a>
<table class="table table-bordered">
<thead>
<tr>
<th>Treening</th><th>Kuupäev</th><th>Kellaaeg</th><th>Kestus</th><th></th>
</tr>
</thead>
<tbody>
<?php foreach ($registrations as $reg): ?>
<tr>
<td><?= htmlspecialchars($reg['name']) ?></td>
<td><?= htmlspecialchars($reg['date']) ?></td>
<td><?= htmlspecialchars($reg['time']) ?></td>
<td><?= htmlspecialchars($reg['duration']) ?> min</td>
<td><a href="<?=BASE_URL?>/cancel.php?id=<?= $reg['reg_id'] ?>" class="btn btn-danger btn-sm">Tühista</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
