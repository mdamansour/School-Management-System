<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Query to get teacher data
$query = "SELECT t.id, t.name, t.birth_date, c.name AS classroom, l.name AS level, t.start_date, t.salary, t.regulated
          FROM teachers t
          JOIN classrooms c ON t.classroom_id = c.id
          JOIN levels l ON t.level_id = l.id";
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
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Basic styling for the manage teachers page */
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
            padding: 10px 20px;
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
            <h1>Manage Teachers</h1>
            <p>Select an option below to manage teacher information.</p>
        </div>
        <button class="btn" onclick="openAddPopup()">Add New Teacher</button>

        <!-- Add New Teacher Popup -->
        <div id="addPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closeAddPopup()">&times;</span>
                <h2>Add New Teacher</h2>
                <form id="addTeacherForm" action="add_teacher.php" method="post">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required><br><br>
                    
                    <label for="birth_date">Birth Date:</label>
                    <input type="date" id="birth_date" name="birth_date" required><br><br>
                    
                    <label for="classroom_id">Classroom:</label>
                    <select id="classroom_id" name="classroom_id" required>
                        <?php
                        // Fetch classrooms for the dropdown
                        $classrooms = $conn->query("SELECT id, name FROM classrooms");
                        while ($classroom = $classrooms->fetch_assoc()) {
                            echo "<option value='{$classroom['id']}'>{$classroom['name']}</option>";
                        }
                        ?>
                    </select><br><br>
                    
                    <label for="level_id">Level:</label>
                    <select id="level_id" name="level_id" required>
                        <?php
                        // Fetch levels for the dropdown
                        $levels = $conn->query("SELECT id, name FROM levels");
                        while ($level = $levels->fetch_assoc()) {
                            echo "<option value='{$level['id']}'>{$level['name']}</option>";
                        }
                        ?>
                    </select><br><br>
                    
                    <label for="start_date">Starting Date:</label>
                    <input type="date" id="start_date" name="start_date" required><br><br>
                    
                    <label for="salary">Salary:</label>
                    <input type="number" step="0.01" id="salary" name="salary" required><br><br>

                    <label for="regulated">Regulated:</label>
                    <select id="regulated" name="regulated" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select><br><br>
                    
                    <input type="submit" class="btn" value="Add Teacher">
                </form>
            </div>
        </div>

        <!-- Edit Teacher Popup -->
        <div id="editPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closeEditPopup()">&times;</span>
                <h2>Edit Teacher</h2>
                <form id="editTeacherForm" action="edit_teacher.php" method="post">
                    <input type="hidden" id="edit_id" name="id">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required><br><br>
                    
                    <label for="edit_birth_date">Birth Date:</label>
                    <input type="date" id="edit_birth_date" name="birth_date" required><br><br>
                    
                    <label for="edit_classroom_id">Classroom:</label>
                    <select id="edit_classroom_id" name="classroom_id" required>
                        <?php
                        // Fetch classrooms for the dropdown
                        $classrooms = $conn->query("SELECT id, name FROM classrooms");
                        while ($classroom = $classrooms->fetch_assoc()) {
                            echo "<option value='{$classroom['id']}'>{$classroom['name']}</option>";
                        }
                        ?>
                    </select><br><br>
                    
                    <label for="edit_level_id">Level:</label>
                    <select id="edit_level_id" name="level_id" required>
                        <?php
                        // Fetch levels for the dropdown
                        $levels = $conn->query("SELECT id, name FROM levels");
                        while ($level = $levels->fetch_assoc()) {
                            echo "<option value='{$level['id']}'>{$level['name']}</option>";
                        }
                        ?>
                    </select><br><br>
                    
                    <label for="edit_start_date">Starting Date:</label>
                    <input type="date" id="edit_start_date" name="start_date" required><br><br>
                    
                    <label for="edit_salary">Salary:</label>
                    <input type="number" step="0.01" id="edit_salary" name="salary" required><br><br>

                    <label for="edit_regulated">Regulated:</label>
                    <select id="edit_regulated" name="regulated" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select><br><br>
                    
                    <input type="submit" class="btn" value="Update Teacher">
                </form>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Birth Date</th>
                    <th>Classroom</th>
                    <th>Level</th>
                    <th>Starting Date</th>
                    <th>Salary</th>
                    <th>Regulated</th>
                    <th>Actions</th>
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
                        <td><?php echo htmlspecialchars($row['salary']); ?></td>
                        <td><?php echo htmlspecialchars($row['regulated']) === '1' ? 'Yes' : 'No'; ?></td>
                        <td class="actions">
                            <button class="btn-edit" onclick="openEditPopup(<?php echo htmlspecialchars($row['id']); ?>)">Edit</button>
                            <a href="delete_teacher.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn">Back to Dashboard</a>
    </div>

    <script>
        function openAddPopup() {
            document.getElementById('addPopup').style.display = 'flex';
        }
        function closeAddPopup() {
            document.getElementById('addPopup').style.display = 'none';
        }

        function openEditPopup(teacherId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_teacher.php?id=' + teacherId, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var teacher = JSON.parse(this.responseText);
                    document.getElementById('edit_id').value = teacher.id;
                    document.getElementById('edit_name').value = teacher.name;
                    document.getElementById('edit_birth_date').value = teacher.birth_date;
                    document.getElementById('edit_classroom_id').value = teacher.classroom_id;
                    document.getElementById('edit_level_id').value = teacher.level_id;
                    document.getElementById('edit_start_date').value = teacher.start_date;
                    document.getElementById('edit_salary').value = teacher.salary;
                    document.getElementById('edit_regulated').value = teacher.regulated;
                    document.getElementById('editPopup').style.display = 'flex';
                }
            }
            xhr.send();
        }
        function closeEditPopup() {
            document.getElementById('editPopup').style.display = 'none';
        }
    </script>
</body>
</html>
