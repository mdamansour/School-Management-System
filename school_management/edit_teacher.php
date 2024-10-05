<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $classroom_id = intval($_POST['classroom_id']);
    $level_id = intval($_POST['level_id']);
    $start_date = $_POST['start_date'];
    $salary = $_POST['salary'];
    $regulated = intval($_POST['regulated']);
    $paid = intval($_POST['paid']);

    $stmt = $conn->prepare("UPDATE teachers SET name = ?, birth_date = ?, classroom_id = ?, level_id = ?, start_date = ?, salary = ?, regulated = ?, paid = ? WHERE id = ?");
    $stmt->bind_param("ssiiidiii", $name, $birth_date, $classroom_id, $level_id, $start_date, $salary, $regulated, $paid, $id);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        die("Update failed: " . $conn->error);
    }
} else {
    header("Location: manage_teachers.php");
    exit();
}
?>
