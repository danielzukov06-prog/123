<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$trainings = getUpcomingTrainings($conn);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Eelolevad treeningud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .training-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            transition: box-shadow 0.2s, transform 0.2s;
            background: #fff;
        }
        .training-card:hover {
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.10);
            transform: translateY(-4px) scale(1.02);
        }
        .training-header {
            font-weight: 600;
            font-size: 1.2rem;
            padding: 1rem 1.25rem 0.5rem 1.25rem;
            background: #fff;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .training-body {
            padding: 1rem 1.25rem 1.25rem 1.25rem;
        }
    </style>
</head>
<body class="bg-light">

<section class="py-5 bg-primary text-white text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold">Eelolevad treeningud</h1>
        <p class="lead">Vaata ja registreeru sobivale treeningule</p>
        <a href="index.php" class="btn btn-light btn-lg mt-3">
            <i class="fas fa-home me-2"></i>Avaleht
        </a>
        <?php if (isAdmin()): ?>
            <a href="admin_add_training.php" class="btn btn-success btn-lg mt-3 ms-2">
                <i class="fas fa-plus me-2"></i>Lisa uus treening
            </a>
        <?php endif; ?>
    </div>
</section>

<div class="container my-5">
    <div class="row g-4">
        <?php if (empty($trainings)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Hetkel pole ühtegi eelolevat treeningut.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($trainings as $training): 
                $registeredCount = getRegisteredCount($conn, $training['id']);
                $percentage = $training['max_participants'] > 0 ? ($registeredCount / $training['max_participants']) * 100 : 0;
                $spotsLeft = $training['max_participants'] - $registeredCount;
                $statusClass = $spotsLeft > 5 ? 'bg-success' : ($spotsLeft > 0 ? 'bg-warning' : 'bg-danger');
                $statusText = $spotsLeft > 5 ? "Vabu kohti: $spotsLeft" : ($spotsLeft > 0 ? "Vähe vabu kohti" : "Täis");
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="training-card h-100">
                    <div class="training-header">
                        <span><?= htmlspecialchars($training['title']) ?></span>
                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                    </div>
                    <div class="training-body">
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span><i class="far fa-calendar me-2"></i><?= date('d.m.Y', strtotime($training['date'])) ?></span>
                            <span><i class="far fa-clock me-2"></i><?= date('H:i', strtotime($training['time'])) ?></span>
                        </div>
                        <p class="mb-3"><i class="fas fa-user-tie me-2"></i>Juhendaja: <?= htmlspecialchars($training['instructor']) ?></p>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar <?= $statusClass ?>" role="progressbar" 
                                 style="width: <?= $percentage ?>%;" 
                                 aria-valuenow="<?= $percentage ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span>Registreerunud: <?= $registeredCount ?>/<?= $training['max_participants'] ?></span>
                            <span><?= round($percentage) ?>% täis</span>
                        </div>
                        <a href="trainings.php?training_id=<?= $training['id'] ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-user-plus me-2"></i>Registreeri
                        </a>
                        <a href="trainings.php?training_id=<?= $training['id'] ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-info-circle me-2"></i>Detailid
                        </a>
                        <?php if (isAdmin()): ?>
                            <a href="admin_edit_training.php?id=<?= $training['id'] ?>" class="btn btn-outline-warning w-100 mt-2">
                                <i class="fas fa-edit me-2"></i>Muuda
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>