<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get username from session, or use a default value if not set
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
    <style>
        /* Basic styling for the admin dashboard */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin: 0;
            font-size: 24px;
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
    <header>
        <a href="index.php" class="logo">Management System</a>
        <div class="dropdown">
            <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <div class="dropdown-content">
                <a href="register.php">Register Admin</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-message">
            <h1>Welcome to the School Management System</h1>
            <p>Select an option below to manage the system.</p>
        </div>
        <div class="cards-grid">
            <div class="card">
                <h2>Manage Students</h2>
                <p>View and manage student information.</p>
                <a href="manage_students.php">Manage Students</a>
            </div>
            <div class="card">
                <h2>Manage Teachers</h2>
                <p>View and manage teacher information.</p>
                <a href="manage_teachers.php">Manage Teachers</a>
            </div>
            <div class="card">
                <h2>Manage Classrooms</h2>
                <p>View and manage classroom information.</p>
                <a href="manage_classrooms.php">Manage Classrooms</a>
            </div>
            <div class="card">
                <h2>Manage Levels</h2>
                <p>View and manage levels of study.</p>
                <a href="manage_levels.php">Manage Levels</a>
            </div>
            <div class="card">
                <h2>Students Payments</h2>
                <p>View and manage payment information.</p>
                <a href="payments.php">Manage Payments</a>
            </div>
        </div>
    </div>
</body>
</html>
