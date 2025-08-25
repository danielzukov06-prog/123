<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../validators.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $pid = preg_replace('/\s+/', '', $_POST['personal_id'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';

    if (!$first || !$last || !valid_isikukood($pid) || !valid_email($email) || strlen($pass) < 6) {
        $err = 'Kontrolli andmeid (isikukood, e-post, parool vähemalt 6 märki).';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users(first_name,last_name,personal_id,email,password_hash) VALUES(?,?,?,?,?)');
            $stmt->execute([$first,$last,$pid,$email,password_hash($pass, PASSWORD_DEFAULT)]);
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        } catch (PDOException $e) {
            $err = 'E-post või isikukood on juba kasutusel.';
        }
    }
}
?>
<!doctype html>
<html lang="et">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Registreeru</title>
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:640px;">
<h1 class="h3 mb-3">Uus kasutaja</h1>
<?php if ($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
<form method="post" novalidate>
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Eesnimi</label><input name="first_name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Perekonnanimi</label><input name="last_name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Isikukood</label><input name="personal_id" class="form-control" pattern="\d{11}" required></div>
<div class="col-md-6"><label class="form-label">E-post</label><input type="email" name="email" class="form-control" required></div>
<div class="col-12"><label class="form-label">Parool</label><input type="password" name="password" class="form-control" minlength="6" required></div>
</div>
<button class="btn btn-primary mt-3">Loo konto</button>
</form>
</div>
</body>
</html>
