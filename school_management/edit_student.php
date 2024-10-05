<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $birth_date = $conn->real_escape_string($_POST['birth_date']);
    $classroom_id = (int)$_POST['classroom_id'];
    $level_id = (int)$_POST['level_id'];
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $amount_agreed = (float)$_POST['amount_agreed'];
    $amount_paid = (float)$_POST['amount_paid'];

    $query = "UPDATE students
              SET name='$name', birth_date='$birth_date', classroom_id=$classroom_id, level_id=$level_id, start_date='$start_date', amount_agreed=$amount_agreed, amount_paid=$amount_paid
              WHERE id=$id";

    if ($conn->query($query) === TRUE) {
        header("Location: manage_students.php");
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>
