<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];

    // Prepare and execute the query
    $stmt = $conn->prepare("UPDATE classrooms SET name = ?, capacity = ? WHERE id = ?");
    $stmt->bind_param("sii", $name, $capacity, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Classroom updated successfully!'); window.location.href = 'manage_classrooms.php';</script>";
    } else {
        echo "<script>alert('Error updating classroom: " . $conn->error . "'); window.location.href = 'manage_classrooms.php';</script>";
    }

    $stmt->close();
}
?>
