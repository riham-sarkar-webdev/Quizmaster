<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
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

// Fetch admin's name
$admin_id = $_SESSION['user_id'];
$sql_admin = "SELECT name FROM users WHERE id = ? AND role = 'Admin'";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("i", $admin_id);
$stmt_admin->execute();
$stmt_admin->bind_result($admin_name);
$stmt_admin->fetch();
$stmt_admin->close();

// Fetch total students count
$sql_students = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'Student'";
$result_students = $conn->query($sql_students);
$total_students = $result_students->fetch_assoc()['total_students'] ?? 0;

// Return data as JSON
echo json_encode([
    'admin_name' => $admin_name,
    'total_students' => $total_students
]);

$conn->close();
?>
