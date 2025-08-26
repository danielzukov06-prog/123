<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRegistrations($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE user_id = ? ORDER BY registered_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    $stmt->close();
    return $registrations;
}

// Kontrollime, kas kasutaja on sisse loginud
if(!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$user = getUserData($conn, $userId);
$registrations = getUserRegistrations($conn, $userId);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Minu profiil</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-center"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <ul class="list-group list-group-flush mt-3">
                        <li class="list-group-item">
                            <i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($user['email']) ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-id-card me-2"></i> <?= htmlspecialchars($user['personal_id']) ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-calendar-alt me-2"></i> Liitunud: <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                        </li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-2"></i>Muuda profiili
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-list me-3"></i>Minu registreeringud</h2>
                <a href="trainings.php" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>Registreeri uuele treeningule
                </a>
            </div>
            
            <?php if(empty($registrations)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Teil pole ühtegi aktiivset registreeringut. 
                    <a href="trainings.php" class="alert-link">Sirvi treeninguid</a> ja registreeri endale sobivale.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Treening</th>
                                <th>Kuupäev</th>
                                <th>Aeg</th>
                                <th>Registreeritud</th>
                                <th>Staatus</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($registrations as $reg): 
                                $training = getTrainingData($conn, $reg['training_id']);
                                $trainingDate = new DateTime($training['date']);
                                $currentDate = new DateTime();
                                $isPast = $trainingDate < $currentDate;
                                $canCancel = !$isPast && canCancelTraining($training['date'], $training['time']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($training['title']) ?></td>
                                <td><?= date('d.m.Y', strtotime($training['date'])) ?></td>
                                <td><?= date('H:i', strtotime($training['time'])) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($reg['registered_at'])) ?></td>
                                <td>
                                    <?php if($reg['status'] == 'active'): ?>
                                        <?php if($isPast): ?>
                                            <span class="badge bg-secondary status-badge">Lõppenud</span>
                                        <?php else: ?>
                                            <span class="badge bg-success status-badge">Aktiivne</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning status-badge">Tühistatud</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($reg['status'] == 'active' && !$isPast): ?>
                                        <?php if($canCancel): ?>
                                            <button class="btn btn-sm btn-danger cancel-registration" 
                                                    data-registration-id="<?= $reg['id'] ?>">
                                                <i class="fas fa-times me-1"></i>Tühista
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">Ei saa enam tühistada</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>