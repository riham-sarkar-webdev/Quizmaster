<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
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

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];

    // Query to fetch questions and their options
    $stmt = $conn->prepare("
        SELECT 
            q.id AS question_id,
            q.text AS question_text,
            GROUP_CONCAT(o.text ORDER BY o.id SEPARATOR '|') AS options,
            (
                SELECT o2.text 
                FROM options o2 
                WHERE o2.question_id = q.id AND o2.is_correct = 1
            ) AS correct_option
        FROM 
            questions q
        JOIN 
            options o ON q.id = o.question_id
        WHERE 
            q.category = ?
        GROUP BY 
            q.id
    ");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'id' => $row['question_id'],
            'text' => $row['question_text'],
            'options' => explode('|', $row['options']),
            'correct_option' => $row['correct_option'],
        ];
    }

    echo json_encode($questions);
    $stmt->close();
}

$conn->close();
?>
