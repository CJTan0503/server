<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";  // Password for your MySQL database
$dbname = "userinfo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Decode JSON input from Angular
$input = json_decode(file_get_contents('php://input'), true);

// Check if username, secret number, and new password are provided
if(isset($input['username']) && isset($input['secretnum']) && isset($input['newPassword'])) {
    $username = $input['username'];
    $secretnum = $input['secretnum'];
    $newPassword = $input['newPassword'];

    // Prepare SQL statement to fetch user data
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND secretnum = ?");
    $stmt->bind_param("si", $username, $secretnum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hash the new password
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

        // Prepare SQL statement to update password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ? AND secretnum = ?");
        $update_stmt->bind_param("ssi", $hashed_password, $username, $secretnum);

        if ($update_stmt->execute()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'error' => $conn->error));
        }

        $update_stmt->close();
    } else {
        echo json_encode(array('success' => false, 'error' => 'User not found or incorrect secret number'));
    }

    $stmt->close();
} else {
    echo json_encode(array('success' => false, 'error' => 'Username, secret number, and new password are required fields.'));
}

$conn->close();
?>
