<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    // Prepare and execute the query
    $stmt = $conn->prepare("INSERT INTO levels (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "<script>alert('Level added successfully!'); window.location.href = 'manage_levels.php';</script>";
    } else {
        echo "<script>alert('Error adding level: " . $conn->error . "'); window.location.href = 'manage_levels.php';</script>";
    }

    $stmt->close();
}
?>
