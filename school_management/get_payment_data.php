<?php
include 'db.php';

$payment_id = $conn->real_escape_string($_GET['id']);

$query = "SELECT * FROM payments WHERE id = '$payment_id'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
?>
