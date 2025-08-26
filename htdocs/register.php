<?php
// register.php

// Start session
session_start();

// Database connection settings
$host = 'localhost';
$db   = 'your_database';
$user = 'your_username';
$pass = 'your_password';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Connect to database
        $mysqli = new mysqli($host, $user, $pass, $db);

        if ($mysqli->connect_error) {
            $message = 'Database connection failed.';
        } else {
            // Check if username exists
            $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = 'Username already taken.';
            } else {
                // Insert new user
                $stmt = $mysqli->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt->bind_param('ss', $username, $hashedPassword);
                if ($stmt->execute()) {
                    $message = 'Registration successful!';
                } else {
                    $message = 'Registration failed.';
                }
            }
            $stmt->close();
            $mysqli->close();
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<form method="post" action="">
    <label>Username: <input type="text" name="username" required></label><br><br>
    <label>Password: <input type="password" name="password" required></label><br><br>
    <button type="submit">Register</button>
</form>
</body>
</html>