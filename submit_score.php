<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Get the student name from the POST request
$student_name = isset($_POST['student_name']) ? $_POST['student_name'] : null;
$score = isset($_POST['score']) ? $_POST['score'] : null;
$category = isset($_POST['category']) ? $_POST['category'] : null;

if ($student_name && $score !== null && $category) {
    // Set up database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert score, category, and student name into the database
    $sql = "INSERT INTO quiz_results (student_name, score, category, attempted_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sds", $student_name, $score, $category); // student_name, score, category

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to submit score."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Missing data."]);
}
?>
