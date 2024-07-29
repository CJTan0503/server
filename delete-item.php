<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$conn = new mysqli('localhost', 'root', '', 'iteminfo'); // Include your database connection

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM item WHERE ID = '$id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}

$conn->close();
?>
