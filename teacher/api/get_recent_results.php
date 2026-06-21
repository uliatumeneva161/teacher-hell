<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../db/db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$teacher_id = (int)$_SESSION['teacher_id'];

$sql = "SELECT r.id, t.name as test_name, r.score,
               DATE_FORMAT(r.created_at, '%d.%m.%Y') as date
        FROM test_results r
        JOIN tests t ON r.test_id = t.id
        WHERE r.teacher_id = $teacher_id
        ORDER BY r.created_at DESC
        LIMIT 5";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Ошибка запроса: ' . $conn->error]);
    exit;
}

$recent = [];
while ($row = $result->fetch_assoc()) {
    $recent[] = $row;
}

echo json_encode($recent);
$conn->close();
?>