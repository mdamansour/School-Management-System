<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];

    // Prepare and execute the query
    $stmt = $conn->prepare("UPDATE levels SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Level updated successfully!'); window.location.href = 'manage_levels.php';</script>";
    } else {
        echo "<script>alert('Error updating level: " . $conn->error . "'); window.location.href = 'manage_levels.php';</script>";
    }

    $stmt->close();
}
?>
