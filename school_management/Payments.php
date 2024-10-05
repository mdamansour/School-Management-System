<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle search form submission
$searchTerm = '';
$showSearchResults = false;
if (isset($_POST['search'])) {
    $searchTerm = $conn->real_escape_string($_POST['search_term']);
    $showSearchResults = true; // Show search results after search
}

// Fetch students for search
$searchQuery = "SELECT id, name FROM students WHERE name LIKE '%$searchTerm%'";
$studentsResult = $conn->query($searchQuery);

if (!$studentsResult) {
    die("Query failed: " . $conn->error);
}

// Handle payment form submission (Add Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $payment_amount = $conn->real_escape_string($_POST['payment_amount']);
    $payment_date = $conn->real_escape_string($_POST['payment_date']);
    $notes = $conn->real_escape_string($_POST['notes']);

    // Validation
    if (empty($student_id) || empty($payment_amount) || empty($payment_date)) {
        echo "<script>alert('All fields are required!'); window.location.href='payments.php';</script>";
        exit();
    }

    $insertQuery = "INSERT INTO payments (student_id, payment_amount, payment_date, notes) VALUES ('$student_id', '$payment_amount', '$payment_date', '$notes')";
    if (!$conn->query($insertQuery)) {
        die("Error inserting payment: " . $conn->error);
    }

    echo "<script>alert('Payment recorded successfully!'); window.location.href='payments.php';</script>";
}

// Handle payment form submission (Edit Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_payment'])) {
    $payment_id = $conn->real_escape_string($_POST['payment_id']);
    $payment_amount = $conn->real_escape_string($_POST['payment_amount']);
    $payment_date = $conn->real_escape_string($_POST['payment_date']);
    $notes = $conn->real_escape_string($_POST['notes']);

    // Validation
    if (empty($payment_id) || empty($payment_amount) || empty($payment_date)) {
        echo "<script>alert('All fields are required!'); window.location.href='payments.php';</script>";
        exit();
    }

    $updateQuery = "UPDATE payments SET payment_amount='$payment_amount', payment_date='$payment_date', notes='$notes' WHERE id='$payment_id'";
    if (!$conn->query($updateQuery)) {
        die("Error updating payment: " . $conn->error);
    }

    echo "<script>alert('Payment updated successfully!'); window.location.href='payments.php';</script>";
}

// Handle delete request
if (isset($_GET['delete'])) {
    $payment_id = $conn->real_escape_string($_GET['delete']);
    $deleteQuery = "DELETE FROM payments WHERE id='$payment_id'";
    if (!$conn->query($deleteQuery)) {
        die("Error deleting payment: " . $conn->error);
    }

    echo "<script>alert('Payment deleted successfully!'); window.location.href='payments.php';</script>";
}

// Fetch payments for display
$paymentsQuery = "SELECT p.id, s.name AS student_name, p.payment_amount, p.payment_date, p.notes FROM payments p JOIN students s ON p.student_id = s.id";
$paymentsResult = $conn->query($paymentsQuery);

if (!$paymentsResult) {
    die("Query failed: " . $conn->error);
}

// Fetch student name for the selected student ID
$selectedStudentName = '';
if (isset($_POST['select_student'])) {
    $selectedStudentId = $conn->real_escape_string($_POST['select_student']);
    $studentNameQuery = "SELECT name FROM students WHERE id = '$selectedStudentId'";
    $studentNameResult = $conn->query($studentNameQuery);
    
    if ($studentNameResult && $studentNameResult->num_rows > 0) {
        $studentData = $studentNameResult->fetch_assoc();
        $selectedStudentName = htmlspecialchars($studentData['name']);
    } else {
        $selectedStudentName = 'Unknown Student';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
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
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .popup.show {
            display: flex;
            opacity: 1;
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
        .actions {
            display: flex;
            gap: 5px;
        }
        .results-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 10px;
            background-color: white;
        }
        /* Styles for search form */
        .search-form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        .search-form input[type="submit"] {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        /* Fixed size for notes field */
        textarea {
            width: 100%;
            box-sizing: border-box;
        }
        .fixed-size-notes {
            height: 80px; /* Adjust height as needed */
            resize: vertical; /* Allow vertical resizing only */
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">Management System</a>
        <div class="dropdown">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>!</span>
            <div class="dropdown-content">
                <a href="register.php">Register Admin</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-message">
            <h1>Students Payments</h1>
            <p>Select an option below to manage payments.</p>
        </div>

        <!-- Student Search Form -->
        <form method="post" action="payments.php" class="search-form">
            <label for="search_term">Search Student:</label>
            <input type="text" id="search_term" name="search_term" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <input type="submit" name="search" value="Search">
        </form>

        <!-- Student Search Results -->
        <?php if ($showSearchResults && $studentsResult->num_rows > 0): ?>
            <h2>Search Results</h2>
            <div class="results-container">
                <form id="selectStudentForm" method="post" action="payments.php">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td>
                                        <button type="button" onclick="showPopup('addPaymentPopup', '<?php echo $student['id']; ?>')" class="btn">Select</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        <?php elseif ($showSearchResults): ?>
            <p>No students found.</p>
        <?php endif; ?>

        <!-- Payment Form Popup -->
        <div id="addPaymentPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('addPaymentPopup')">&times;</span>
                <h2>Record Payment</h2>
                <form method="post" action="payments.php">
                    <input type="hidden" id="student_id" name="student_id" value="">
                    <label for="payment_amount">Payment Amount:</label>
                    <input type="number" id="payment_amount" name="payment_amount" step="0.01" required><br><br>
                    
                    <label for="payment_date">Payment Date:</label>
                    <input type="date" id="payment_date" name="payment_date" required><br><br>
                    
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes" rows="4" class="fixed-size-notes"></textarea><br><br>
                    
                    <input type="submit" name="add_payment" class="btn" value="Submit Payment">
                </form>
            </div>
        </div>

        <!-- Edit Payment Form Popup -->
        <div id="editPaymentPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('editPaymentPopup')">&times;</span>
                <h2>Edit Payment</h2>
                <form method="post" action="payments.php">
                    <input type="hidden" id="edit_payment_id" name="payment_id" value="">
                    <label for="edit_payment_amount">Payment Amount:</label>
                    <input type="number" id="edit_payment_amount" name="payment_amount" step="0.01" required><br><br>
                    
                    <label for="edit_payment_date">Payment Date:</label>
                    <input type="date" id="edit_payment_date" name="payment_date" required><br><br>
                    
                    <label for="edit_notes">Notes:</label>
                    <textarea id="edit_notes" name="notes" rows="4" class="fixed-size-notes"></textarea><br><br>
                    
                    <input type="submit" name="edit_payment" class="btn" value="Update Payment">
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <h2>Recorded Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Payment Amount</th>
                    <th>Payment Date</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $paymentsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                        <td class="fixed-size-notes"><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td class="actions">
                            <button type="button" onclick="editPayment(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['payment_amount']); ?>', '<?php echo htmlspecialchars($row['payment_date']); ?>', '<?php echo htmlspecialchars($row['notes']); ?>')" class="btn btn-edit">Edit</button>
                            <a href="payments.php?delete=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this payment?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showPopup(popupId, studentId) {
            const popup = document.getElementById(popupId);
            popup.classList.add('show');
            if (popupId === 'addPaymentPopup' && studentId) {
                document.getElementById('student_id').value = studentId;
            }
        }

        function closePopup(popupId) {
            const popup = document.getElementById(popupId);
            popup.classList.remove('show');
        }

        function editPayment(paymentId, paymentAmount, paymentDate, notes) {
            document.getElementById('edit_payment_id').value = paymentId;
            document.getElementById('edit_payment_amount').value = paymentAmount;
            document.getElementById('edit_payment_date').value = paymentDate;
            document.getElementById('edit_notes').value = notes;
            showPopup('editPaymentPopup');
        }
    </script>
</body>
</html>
