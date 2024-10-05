<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch levels and student count for display
$query = "
    SELECT 
        levels.id, 
        levels.name, 
        COALESCE(COUNT(students.id), 0) AS student_count
    FROM 
        levels
    LEFT JOIN 
        students ON students.level_id = levels.id
    GROUP BY 
        levels.id, levels.name
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get username from session, or use a default value if not set
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Levels</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your CSS styles here */
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
        .btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .close-btn {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        .close-btn:hover {
            color: red;
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
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .actions {
            display: flex;
            gap: 5px;
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
            <h1>Manage Levels</h1>
            <p>Select an option below to manage levels in the system.</p>
        </div>
        <button class="btn" onclick="openAddPopup()">Add New Level</button>
        
        <!-- Add New Level Popup -->
        <div id="addPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closeAddPopup()">&times;</span>
                <h2>Add New Level</h2>
                <form id="addLevelForm" action="add_level.php" method="post">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required><br><br>
                    
                    <input type="submit" class="btn" value="Add Level">
                </form>
            </div>
        </div>

        <!-- Edit Level Popup -->
        <div id="editPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closeEditPopup()">&times;</span>
                <h2>Edit Level</h2>
                <form id="editLevelForm" action="edit_level.php" method="post">
                    <input type="hidden" id="edit_id" name="id">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required><br><br>
                    
                    <input type="submit" class="btn" value="Update Level">
                </form>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Students Attending</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_count']); ?></td>
                        <td class="actions">
                            <button class="btn-edit" onclick="openEditPopup(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">Edit</button>
                            <button class="btn-delete" onclick="deleteLevel(<?php echo $row['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function openAddPopup() {
            document.getElementById('addPopup').style.display = 'flex';
        }

        function closeAddPopup() {
            document.getElementById('addPopup').style.display = 'none';
        }

        function openEditPopup(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('editPopup').style.display = 'flex';
        }

        function closeEditPopup() {
            document.getElementById('editPopup').style.display = 'none';
        }

        function deleteLevel(id) {
            if (confirm('Are you sure you want to delete this level?')) {
                window.location.href = 'delete_level.php?id=' + id;
            }
        }
    </script>
</body>
</html>
