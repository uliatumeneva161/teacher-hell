<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];

$teacherRes = $conn->query("SELECT subject_id FROM teachers WHERE id = $teacher_id");
$teacher = $teacherRes->fetch_assoc();
$subject_id = $teacher['subject_id'];

$stats = [
    'available' => 0,
    'completed' => 0,
    'average' => 0
];

if ($subject_id) {
    // Доступные тесты
    $availRes = $conn->query("SELECT COUNT(*) as cnt FROM tests WHERE subject_id = $subject_id");
    $stats['available'] = $availRes->fetch_assoc()['cnt'];

    // Пройденные тесты
    $compRes = $conn->query("SELECT COUNT(*) as cnt FROM test_results WHERE teacher_id = $teacher_id");
    $stats['completed'] = $compRes->fetch_assoc()['cnt'];

    // Средний балл
    $avgRes = $conn->query("SELECT AVG(score) as avg FROM test_results WHERE teacher_id = $teacher_id");
    $avgRow = $avgRes->fetch_assoc();
    $stats['average'] = $avgRow['avg'] ? round($avgRow['avg']) : 0;
}

echo json_encode($stats);
$conn->close();
?>