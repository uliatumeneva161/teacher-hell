<?php
$host = 'MySQL-8.0'; 
$username = 'root';
$password = '';
$database = 'podgotovka_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode([
        'error' => 'Ошибка подключения к базе данных: ' . $conn->connect_error
    ]));    
}
$conn->set_charset("utf8mb4");

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

function isAdmin() {
    session_start();
    return isset($_SESSION['login']) && isset($_SESSION['pass']);
}
function isTeacher() {
    session_start();
    return isset($_SESSION['teacher_id']);
}
function getCurrentTeacher() {
    if (!isTeacher()) return null;
    global $conn;
    $teacher_id = $_SESSION['teacher_id'];
    $result = $conn->query("SELECT * FROM teachers WHERE id = $teacher_id");
    return $result->fetch_assoc();
}

function getStats() {
    global $conn;
    $stats = [];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM subjects");
    $stats['subjects'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM teachers");
    $stats['teachers'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM tests");
    $stats['tests'] = $result->fetch_assoc()['count'];
    
    return $stats;
}
?>