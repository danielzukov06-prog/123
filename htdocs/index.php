<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Get upcoming trainings
$upcomingTrainings = getUpcomingTrainings($conn);

// Get total active registrations
function getTotalRegistrations($conn) {
    $sql = "SELECT COUNT(*) as total FROM registrations";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['total'] : 0;
}

// Get total active users
function getTotalUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['total'] : 0;
}

// Get number of trainings today
function getTrainingsToday($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total FROM trainings WHERE date = :today";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Sporditreeningud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.05);
            padding: 2rem 1rem;
            text-align: center;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .stats-card:hover {
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.10);
            transform: translateY(-4px) scale(1.03);
        }
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #0d6efd;
        }
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
        <h1 class="display-4 fw-bold">Registreeri end sporditreeningutele</h1>
        <p class="lead">Vali endale sobiv treening ja tee esimene samm tervislikuma eluviisi poole</p>
        <a href="trainings.php" class="btn btn-light btn-lg mt-3">
            <i class="fas fa-search me-2"></i>Sirvi treeninguid
        </a>
    </div>
</section>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="fas fa-running"></i>
                </div>
                <h3><?= count($upcomingTrainings) ?></h3>
                <p class="text-muted mb-0">Eelolevat treeningut</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?= getTotalRegistrations($conn) ?></h3>
                <p class="text-muted mb-0">Aktiivset registreeringut</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-info">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3><?= getTotalUsers($conn) ?></h3>
                <p class="text-muted mb-0">Aktiivset kasutajat</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon text-warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3><?= getTrainingsToday($conn) ?></h3>
                <p class="text-muted mb-0">Täna toimuvat treeningut</p>
            </div>
        </div>
    </div>
    
    <section class="mb-5">
        <h2 class="mb-4"><i class="fas fa-calendar-alt me-3"></i>Peamised eelolevad treeningud</h2>
        
        <div class="row g-4">
            <?php foreach(array_slice($upcomingTrainings, 0, 3) as $training): 
                $registeredCount = getRegisteredCount($conn, $training['id']);
                $percentage = $training['max_participants'] > 0 ? ($registeredCount / $training['max_participants']) * 100 : 0;
                $spotsLeft = $training['max_participants'] - $registeredCount;
                
                $statusClass = $spotsLeft > 5 ? 'bg-success' : ($spotsLeft > 0 ? 'bg-warning' : 'bg-danger');
                $statusText = $spotsLeft > 5 ? "Vabu kohti: $spotsLeft" : ($spotsLeft > 0 ? "Vähe vabu kohti" : "Täis");
            ?>
            <div class="col-md-4">
                <div class="training-card">
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
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="trainings.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-list me-2"></i>Vaata kõiki treeninguid
            </a>
        </div>
    </section>
</div>

<!-- Bootstrap JS (optional, for dropdowns/tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>