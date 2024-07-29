<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$conn = new mysqli('localhost', 'root', '', 'iteminfo');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Debugging: Check if connection is successful
error_log("Database connected successfully.");

$result = $conn->query("SELECT MAX(CAST(SUBSTRING(id, 2) AS UNSIGNED)) AS max_id FROM item");

// Debugging: Check if query execution is successful
if (!$result) {
    error_log("Query failed: " . $conn->error);
    echo json_encode(['success' => false, 'error' => 'Query error: ' . $conn->error]);
    $conn->close();
    exit();
}

// Debugging: Check the result of the query
$row = $result->fetch_assoc();
if ($row === null) {
    error_log("No rows returned from query.");
} else {
    error_log("Query result: " . json_encode($row));
}

$newId = 'I' . str_pad(($row['max_id'] ?? 0) + 1, 2, '0', STR_PAD_LEFT);
echo json_encode(['newId' => $newId]);

// Debugging: Output the new ID
error_log("Generated new ID: " . $newId);

$conn->close();
?>

