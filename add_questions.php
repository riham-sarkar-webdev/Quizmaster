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
$category = $_POST['category'];
$questions = $_POST['questions'];

// Insert questions and options into the database
foreach ($questions as $question) {
    // Insert the question into the questions table
    $stmt = $conn->prepare("INSERT INTO questions (category, text) VALUES (?, ?)");
    $stmt->bind_param("ss", $category, $question['text']);
    $stmt->execute();
    $question_id = $stmt->insert_id; // Get the inserted question ID
    $stmt->close();

    // Insert the options for the question
    foreach ($question['options'] as $index => $option_text) {
        $is_correct = ($index + 1 == $question['correct']) ? 1 : 0; // Check if this is the correct option
        $stmt = $conn->prepare("INSERT INTO options (question_id, text, is_correct) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
        $stmt->execute();
        $stmt->close();
    }
}
// Display success message and redirect
echo "<script>
    alert('Questions added successfully!');
    window.location.href = 'admin-dashboard.html'; // Redirect to admin dashboard
</script>";
$conn->close();
?>
