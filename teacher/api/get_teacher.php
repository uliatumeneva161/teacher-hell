<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php'; 
if (!$conn) {
    echo json_encode(['error' => 'Ошибка подключения к БД']);
    exit;
}

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['logged' => false]);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];

$sql = "SELECT t.id, t.full_name as name, t.login, t.subject_id, s.name as subject
        FROM teachers t
        LEFT JOIN subjects s ON t.subject_id = s.id
        WHERE t.id = $teacher_id";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Ошибка запроса: ' . $conn->error]);
    exit;
}

if ($row = $result->fetch_assoc()) {
    $row['logged'] = true;
    echo json_encode($row);
} else {
    echo json_encode(['logged' => false, 'error' => 'Teacher not found']);
}

$conn->close();
?>