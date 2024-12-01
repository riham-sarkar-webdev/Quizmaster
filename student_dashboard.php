<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student ID
$student_id = $_SESSION['user_id'];

// Fetch student details
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

// Fetch quiz history
$query = "SELECT category, score, attempted_at FROM quiz_results WHERE student_name = ? ORDER BY attempted_at DESC";  // Assuming student_name is the column
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $name); // Bind the student's name, or if it's student_id, use "i" here instead of "s"
$stmt->execute();
$stmt->bind_result($category, $score, $attempted_at);

// Fetch all results
$quizHistory = [];
while ($stmt->fetch()) {
    $quizHistory[] = [
        'category' => $category,
        'score' => $score,
        'attempted_at' => $attempted_at
    ];
}

$stmt->close();

// Prepare the response with student's name and quiz history
$response = [
    'name' => $name,
    'quizHistory' => $quizHistory
];

// Output as JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
