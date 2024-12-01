<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized access.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the form
$question_id = $_POST['question_id'];
$question_text = $_POST['text'];
$options = $_POST['options'];  // options should be passed as an array
$correct_option = $_POST['correct'];

// Ensure $options is an array (sanitize input if needed)
if (!is_array($options)) {
    die("Options must be an array.");
}

// Update the question text
$stmt = $conn->prepare("UPDATE questions SET text = ? WHERE id = ?");
$stmt->bind_param("si", $question_text, $question_id);
$stmt->execute();
$stmt->close();

// Delete existing options
$stmt = $conn->prepare("DELETE FROM options WHERE question_id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$stmt->close();

// Insert new options
foreach ($options as $index => $option) {
    $is_correct = ($index + 1 == $correct_option) ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO options (question_id, text, is_correct) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $question_id, $option, $is_correct);
    $stmt->execute();
    $stmt->close();
}

echo "Question updated successfully!";
$conn->close();
?>
