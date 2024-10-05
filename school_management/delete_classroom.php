<?php
// Include database connection
include 'db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query
    $stmt = $conn->prepare("DELETE FROM classrooms WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Classroom deleted successfully!'); window.location.href = 'manage_classrooms.php';</script>";
    } else {
        echo "<script>alert('Error deleting classroom: " . $conn->error . "'); window.location.href = 'manage_classrooms.php';</script>";
    }

    $stmt->close();
}
?>
