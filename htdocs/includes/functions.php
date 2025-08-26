<?php
// Treeningute pärimine
function getUpcomingTrainings($conn) {
    $sql = "SELECT * FROM trainings WHERE date >= CURDATE() ORDER BY date, time ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Registreeringute arvu pärimine
function getRegisteredCount($conn, $trainingId) {
    $sql = "SELECT COUNT(*) FROM registrations 
            WHERE training_id = :training_id AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':training_id', $trainingId);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Kasutaja registreeringute kontroll
function isUserRegistered($conn, $userId, $trainingId) {
    $sql = "SELECT COUNT(*) FROM registrations 
            WHERE user_id = :user_id AND training_id = :training_id AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':training_id', $trainingId);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Kasutaja andmete pärimine
function getUserData($conn, $userId) {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Treeningu andmete pärimine
function getTrainingData($conn, $trainingId) {
    $sql = "SELECT * FROM trainings WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $trainingId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Isikukoodi valideerimine
function validatePersonalId($personalId) {
    if(strlen($personalId) !== 11 || !is_numeric($personalId)) {
        return false;
    }
    
    // Lihtsam kontroll, täpsem implementatsioon võiks olla
    $century = substr($personalId, 0, 1);
    if(!in_array($century, [3,4,5,6])) {
        return false;
    }
    
    return true;
}

// E-posti valideerimine
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Treeningu tühistamise kontroll
function canCancelTraining($trainingDate, $trainingTime) {
    $trainingDateTime = new DateTime("$trainingDate $trainingTime");
    $currentDateTime = new DateTime();
    $interval = $currentDateTime->diff($trainingDateTime);
    
    // Kontrollime, kas tühistamise aeg on vähemalt 2 tundi enne treeningut
    return ($interval->days * 24 + $interval->h) >= 2;
}
?>