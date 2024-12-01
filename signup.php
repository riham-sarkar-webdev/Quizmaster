<?php
// Database connection
$servername = "localhost";
$username = "root";  // Default username for localhost
$password = "";  // Default password for localhost (empty if not set)
$dbname = "user_db";  // Name of the database created earlier

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Secure password hash
    $role = $_POST['role'];  // Get the role from the form

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    // Execute the query
    if ($stmt->execute()) {
        // Start a session
        session_start();

        // Fetch the ID of the newly inserted user
        $user_id = $conn->insert_id;

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $username;

        // Redirect based on role
        if ($role === 'Admin') {
            header("Location: admin-dashboard.html");  // Redirect to admin dashboard
            exit();
        } elseif ($role === 'Student') {
            header("Location: student-dashboard.html");  // Redirect to student dashboard
            exit();
        } else {
            echo "Error: Role not recognized.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
