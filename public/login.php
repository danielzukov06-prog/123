<?php
require_once __DIR__ . '/../auth.php';
$error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if(login($email,$password)){
        header('Location: '.BASE_URL.'/trainings.php');
        exit;
    } else { $error='Vale e-post või parool.'; }
}
?>
<!doctype html>
<html lang="et">
<head>
<meta charset="utf-8">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:480px;">
<h1 class="h3 mb-3">Logi sisse</h1>
<?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post" novalidate>
<div class="mb-3">
<label class="form-label">E-post</label>
<input type="email" name="email" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label">Parool</label>
<input type="password" name="password" class="form-control" required>
</div>
<button class="btn btn-primary w-100">Sisene</button>
</form>
</div>
</body>
</html>
