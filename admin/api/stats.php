<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../db/db.php';

$stats = [];

$result = $conn->query("SELECT COUNT(*) as count FROM subjects"); 
$row = $result->fetch_assoc();
$stats['subjects'] = $row['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM teachers");
$row = $result->fetch_assoc();//echo $row; 
$stats['teachers'] = $row['count'];
$result = $conn->query("SELECT COUNT(*) as count FROM tests");
$row = $result->fetch_assoc();
$stats['tests'] = $row['count'];

echo json_encode($stats); 

$conn->close();
?>