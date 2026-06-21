<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['test_id']) || !isset($data['correct']) || !isset($data['total'])) {
    echo json_encode(['error' => 'Не все параметры переданы']);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];
$test_id = (int)$data['test_id'];
$correct = (int)$data['correct'];
$total = (int)$data['total'];
$score = $total > 0 ? round($correct / $total * 100) : 0;

$sql = "INSERT INTO test_results (teacher_id, test_id, correct_answers, total_questions, score) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $teacher_id, $test_id, $correct, $total, $score);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'score' => $score]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сохранения: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>