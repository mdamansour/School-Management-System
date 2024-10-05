<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];

    // Prepare and execute the query
    $stmt = $conn->prepare("INSERT INTO classrooms (name, capacity) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $capacity);

    if ($stmt->execute()) {
        echo "<script>alert('Classroom added successfully!'); window.location.href = 'manage_classrooms.php';</script>";
    } else {
        echo "<script>alert('Error adding classroom: " . $conn->error . "'); window.location.href = 'manage_classrooms.php';</script>";
    }

    $stmt->close();
}
?>
