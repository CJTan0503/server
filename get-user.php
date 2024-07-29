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

// Check if user_id is provided
if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];

  // Prepare SQL statement to fetch username
  $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(array('username' => $row['username']));
  } else {
    echo json_encode(array('username' => ''));
  }

  $stmt->close();
} else {
  echo json_encode(array('error' => 'user_id parameter is required'));
}

$conn->close();
?>
