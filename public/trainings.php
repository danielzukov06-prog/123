<?php
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../config.php';
require_login();

// loetelu
$stmt = $pdo->query('SELECT t.*,
(SELECT COUNT(*) FROM registrations r WHERE r.training_id=t.id) AS taken
FROM trainings t ORDER BY date,time');
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="et">
<head>
<meta charset="utf-8">
<title>Treeningud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
<div class="container-fluid">
<a class="navbar-brand" href="#"><?=APP_NAME?></a>
<div class="ms-auto">
<?php if(is_logged_in()): ?>
<a class="btn btn-outline-secondary me-2" href="<?=BASE_URL?>/my.php">Minu registreeringud</a>
<a class="btn btn-outline-danger" href="<?=BASE_URL?>/logout.php">Logi välja</a>
<?php else: ?>
<a class="btn btn-outline-primary me-2" href="<?=BASE_URL?>/login.php">Logi sisse</a>
<?php endif; ?>
<?php if(is_admin()): ?>
<a class="btn btn-warning ms-2" href="<?=BASE_URL?>/admin/trainings.php">Admin</a>
<?php endif; ?>
</div></div></nav>

<div class="container py-4">
<h1 class="h4 mb-3">Treeningud</h1>
<div class="row row-cols-1 row-cols-md-2 g-3">
<?php foreach($rows as $t): $free=$t['max_participants']-$t['taken']; ?>
<div class="col">
<div class="card h-100">
<div class="card-body d-flex flex-column">
<h5 class="card-title"><?=htmlspecialchars($t['name'])?></h5>
<p class="card-text mb-1">
<strong>Kuupäev:</strong> <?=$t['date']?>
<strong class="ms-2">Algus:</strong> <?=$t['time']?>
<strong class="ms-2">Kestus:</strong> <?=$t['duration']?> min
</p>
<p class="card-text">Kohti: <span><?=$free?></span>/<span><?=$t['max_participants']?></span></p>
<div class="mt-auto">
<?php if(is_logged_in()): ?>
<form method="get" action="<?=BASE_URL?>/register_training.php">
<input type="hidden" name="id" value="<?=$t['id']?>">
<button class="btn btn-primary" <?=$free<=0?'disabled':''?>>Registreeru</button>
</form>
<?php else: ?>
<a class="btn btn-primary" href="<?=BASE_URL?>/login.php">Logi sisse, et registreeruda</a>
<?php endif; ?>
</div></div></div></div>
<?php endforeach; ?>
</div></div>
</body>
</html>
