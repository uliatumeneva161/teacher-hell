<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../db/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Получить всех учителей с названием предмета
        $sql = "SELECT t.*, s.name as subject_name 
                FROM teachers t 
                LEFT JOIN subjects s ON t.subject_id = s.id 
                ORDER BY t.full_name";
        
        $result = $conn->query($sql);
        $teachers = [];
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        echo json_encode($teachers);
        break;
        
    case 'POST':
        // Добавить нового учителя
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['full_name']) && !empty($data['login']) && !empty($data['password'])) {
            $full_name = $conn->real_escape_string(trim($data['full_name']));
            $login = $conn->real_escape_string(trim($data['login']));
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $subject_id = !empty($data['subject_id']) ? intval($data['subject_id']) : null;
            
            // Проверяем, существует ли уже такой логин
            $checkSql = "SELECT id FROM teachers WHERE login = '$login'";
            $checkResult = $conn->query($checkSql);
            if ($checkResult->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Учитель с таким логином уже существует']);
            } else {
                $sql = "INSERT INTO teachers (full_name, login, password, subject_id) 
                        VALUES ('$full_name', '$login', '$password', $subject_id)";
                
                if ($conn->query($sql)) {
                    $newId = $conn->insert_id;
                    
                    // Получаем данные нового учителя с названием предмета
                    $getSql = "SELECT t.*, s.name as subject_name 
                              FROM teachers t 
                              LEFT JOIN subjects s ON t.subject_id = s.id 
                              WHERE t.id = $newId";
                    $getResult = $conn->query($getSql);
                    $newTeacher = $getResult->fetch_assoc();
                    
                    echo json_encode($newTeacher);
                } else {
                     http_response_code(500);
                    echo json_encode(['error' => 'Ошибка при добавлении учителя: ' . $conn->error]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Все обязательные поля должны быть заполнены']);
        }
        break;
        
    case 'DELETE':
        // Удалить учителя
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['id'])) {
            $id = intval($data['id']);
              $sql = "DELETE FROM teachers WHERE id = $id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Учитель удален']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Ошибка при удалении учителя: ' . $conn->error]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID учителя обязательно']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Метод не поддерживается']);
        break;
        }

$conn->close();
?>