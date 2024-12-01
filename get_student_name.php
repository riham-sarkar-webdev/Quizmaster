<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get email from session
$email = $_SESSION['email'];

// Retrieve student name from the database
$stmt = $conn->prepare('SELECT name FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Student not found']);
    exit();
}

$user = $result->fetch_assoc();
echo json_encode(['name' => $user['name']]);

$stmt->close();
$conn->close();
?>
