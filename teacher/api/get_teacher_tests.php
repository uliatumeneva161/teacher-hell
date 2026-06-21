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
if (!$teacherRes || $teacherRes->num_rows == 0) {
    echo json_encode([]);
    exit;
}
$teacher = $teacherRes->fetch_assoc();
$subject_id = $teacher['subject_id'];

if (!$subject_id) {
    echo json_encode([]);
    exit;
}

$testsSql = "SELECT id, name, question_count 
             FROM tests 
             WHERE subject_id = $subject_id 
             ORDER BY created_at DESC";
$testsRes = $conn->query($testsSql);

$tests = [];
while ($test = $testsRes->fetch_assoc()) {
    
    $resultSql = "SELECT id FROM test_results 
                  WHERE teacher_id = $teacher_id AND test_id = " . $test['id'];
    $resultRes = $conn->query($resultSql);
    $test['completed'] = ($resultRes->num_rows > 0);
    $tests[] = $test;
}

echo json_encode($tests);
$conn->close();
?>