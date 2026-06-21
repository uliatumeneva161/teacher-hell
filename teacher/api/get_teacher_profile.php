<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];

$teacherSql = "SELECT t.id, t.full_name as name, t.login, 
                      t.subject_id, s.name as subject,
                      DATE_FORMAT(t.created_at, '%d.%m.%Y') as registered
               FROM teachers t
               LEFT JOIN subjects s ON t.subject_id = s.id
               WHERE t.id = $teacher_id";
$teacherRes = $conn->query($teacherSql);
$profile = $teacherRes->fetch_assoc();

if (!$profile) {
    echo json_encode(['error' => 'Профиль не найден']);
    exit;
}

// Статистика
$stats = [];

if ($profile['subject_id']) {
    $availRes = $conn->query("SELECT COUNT(*) as cnt FROM tests WHERE subject_id = " . $profile['subject_id']);
    $stats['total_tests'] = $availRes->fetch_assoc()['cnt'];
} else {
    $stats['total_tests'] = 0;
}

$compRes = $conn->query("SELECT COUNT(*) as cnt FROM test_results WHERE teacher_id = $teacher_id");
$stats['completed_tests'] = $compRes->fetch_assoc()['cnt'];

$bestRes = $conn->query("SELECT MAX(score) as best FROM test_results WHERE teacher_id = $teacher_id");
$bestRow = $bestRes->fetch_assoc();
$stats['best_score'] = $bestRow['best'] ?: 0;

$avgRes = $conn->query("SELECT AVG(score) as avg FROM test_results WHERE teacher_id = $teacher_id");
$avgRow = $avgRes->fetch_assoc();
$stats['average_score'] = $avgRow['avg'] ? round($avgRow['avg']) : 0;

$profile['stats'] = $stats;

echo json_encode($profile);
$conn->close();
?>