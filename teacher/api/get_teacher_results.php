<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];

$sql = "SELECT r.id, r.test_id, t.name as test_name, s.name as subject_name,
               r.correct_answers, r.total_questions, r.score,
               DATE_FORMAT(r.created_at, '%d.%m.%Y %H:%i') as completed_at
        FROM test_results r
        JOIN tests t ON r.test_id = t.id
        JOIN subjects s ON t.subject_id = s.id
        WHERE r.teacher_id = $teacher_id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Ошибка запроса: ' . $conn->error]);
    exit;
}

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

echo json_encode($results);
$conn->close();
?>