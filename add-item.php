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
    $photoPath = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $photoPath = 'uploads/' . basename($photo['name']);
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            echo json_encode(['error' => 'Failed to upload photo']);
            exit();
        }
    }

    $query = "INSERT INTO item (name, type, description, location, time, recorder, photo) VALUES ('$name', '$type', '$description', '$location', '$time', '$recorder', '$photoPath')";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Query error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
