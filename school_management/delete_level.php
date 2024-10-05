<?php
// Include database connection
include 'db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query
    $stmt = $conn->prepare("DELETE FROM levels WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Level deleted successfully!'); window.location.href = 'manage_levels.php';</script>";
    } else {
        echo "<script>alert('Error deleting level: " . $conn->error . "'); window.location.href = 'manage_levels.php';</script>";
    }

    $stmt->close();
}
?>
