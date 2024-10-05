<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch students from the database
$query = "SELECT s.id, s.name, s.birth_date, c.name AS classroom, l.name AS level, s.start_date
          FROM students s
          JOIN classrooms c ON s.classroom_id = c.id
          JOIN levels l ON s.level_id = l.id";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Manage Students</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Birth Date</th>
                <th>Classroom</th>
                <th>Level</th>
                <th>Start Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['birth_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['classroom']); ?></td>
                    <td><?php echo htmlspecialchars($row['level']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="index.php">Back to Dashboard</a>
</body>
</html>
