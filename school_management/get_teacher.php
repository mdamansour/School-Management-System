<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, birth_date, classroom_id, level_id, start_date, salary, regulated, paid FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    echo json_encode($teacher);
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
