<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$conn = new mysqli('localhost', 'root', '', 'iteminfo');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$name = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$query = "SELECT * FROM item WHERE 1";

if ($name) {
    $query .= " AND name LIKE '%$name%'";
}

if ($category) {
    $query .= " AND type = '$category'";
}

$result = $conn->query($query);

$items = [];
while ($row = $result->fetch_assoc()) {
    if ($row['photo']) {
        // Convert BLOB to base64 encoded string
        $row['photo'] = base64_encode($row['photo']);
    }
    $items[] = $row;
}

echo json_encode($items);

$conn->close();
?>