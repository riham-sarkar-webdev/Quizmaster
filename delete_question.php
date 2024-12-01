<?php
// Include database connection
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['question_id'])) {
        $questionId = $_POST['question_id'];

        // Delete the question from the database
        $query = "DELETE FROM questions WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $questionId);

        if ($stmt->execute()) {
            echo "Question deleted successfully.";
        } else {
            echo "Error deleting question.";
        }

        $stmt->close();
    } else {
        echo "No question ID provided.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
