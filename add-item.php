<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'iteminfo');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $name = $conn->real_escape_string($_POST['name']);
    $type = $conn->real_escape_string($_POST['type']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    $time = $conn->real_escape_string($_POST['time']);
    $recorder = $conn->real_escape_string($_POST['recorder']);

    // Handle file upload
    $photoBlob = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoBlob = file_get_contents($_FILES['photo']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO item (name, type, description, location, time, recorder, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssb', $name, $type, $description, $location, $time, $recorder, $photoBlob);
    $null = NULL;
    $stmt->send_long_data(6, $photoBlob);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Query error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
