<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch data to display on the dashboard
// Example queries to fetch counts and data
$studentCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$teacherCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'teacher'")->fetch_assoc()['count'];
$classroomCount = $conn->query("SELECT COUNT(*) AS count FROM classrooms")->fetch_assoc()['count'];
$levelCount = $conn->query("SELECT COUNT(*) AS count FROM levels")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
    <style>
        /* Add some basic styling for the dashboard */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
            text-align: center;
        }
        .card h2 {
            margin: 0;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .card a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="card">
            <h2>Students</h2>
            <p>Total Students: <?php echo $studentCount; ?></p>
            <a href="manage_students.php">Manage Students</a>
        </div>
        <div class="card">
            <h2>Teachers</h2>
            <p>Total Teachers: <?php echo $teacherCount; ?></p>
            <a href="manage_teachers.php">Manage Teachers</a>
        </div>
        <div class="card">
            <h2>Classrooms</h2>
            <p>Total Classrooms: <?php echo $classroomCount; ?></p>
            <a href="manage_classrooms.php">Manage Classrooms</a>
        </div>
        <div class="card">
            <h2>Levels</h2>
            <p>Total Levels: <?php echo $levelCount; ?></p>
            <a href="manage_levels.php">Manage Levels</a>
        </div>
        <div class="card">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
