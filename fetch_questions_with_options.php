<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Validate category
$category = $_GET['category'] ?? '';
if (!$category) {
    echo json_encode(['error' => 'No category provided']);
    exit();
}

// Fetch questions for the category
$questionsQuery = $conn->prepare('SELECT id, text FROM questions WHERE category = ?');
if (!$questionsQuery) {
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    exit();
}

$questionsQuery->bind_param('s', $category);
$questionsQuery->execute();
$questionsResult = $questionsQuery->get_result();

$questions = [];
while ($question = $questionsResult->fetch_assoc()) {
    // Fetch options for each question and mark the correct one
    $optionsQuery = $conn->prepare('SELECT id, text, is_correct FROM options WHERE question_id = ?');
    $optionsQuery->bind_param('i', $question['id']);
    $optionsQuery->execute();
    $optionsResult = $optionsQuery->get_result();

    $options = [];
    $correctAnswerId = null;
    while ($option = $optionsResult->fetch_assoc()) {
        // Store the correct answer ID
        if ($option['is_correct'] == 1) {
            $correctAnswerId = $option['id'];
        }

        // Add option to the options array
        $options[] = $option;
    }

    // Add the correct answer ID along with the options
    $questions[] = [
        'id' => $question['id'],
        'text' => $question['text'],
        'options' => $options,
        'correct_answer_id' => $correctAnswerId
    ];
}

echo json_encode(['questions' => $questions]);
$conn->close();
