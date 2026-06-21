<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../db/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM teachers WHERE subject_id = s.id) as teacher_count,
                (SELECT COUNT(*) FROM tests WHERE subject_id = s.id) as test_count
                FROM subjects s 
                ORDER BY s.name";
        
        $result = $conn->query($sql);
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
            }
        echo json_encode($subjects);
        break;
        
    case 'POST':
        // Добавить новый предмет
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['name'])) {
            $name = $conn->real_escape_string(trim($data['name']));
            
            // Проверяем, существует ли уже такой предмет
            $checkSql = "SELECT id FROM subjects WHERE name = '$name'";
            $checkResult = $conn->query($checkSql);
            
            if ($checkResult->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Предмет с таким названием уже существует']);
                 } else {
                $sql = "INSERT INTO subjects (name) VALUES ('$name')";
                
                if ($conn->query($sql)) {
                    $newId = $conn->insert_id;
                    echo json_encode([
                        'id' => $newId,
                        'name' => $name,
                        'teacher_count' => 0,
                        'test_count' => 0
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Ошибка при добавлении предмета: ' . $conn->error]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Название предмета обязательно']);
        }
        break;
        
    case 'DELETE':
        // Удалить предмет
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['id'])) {
            $id = intval($data['id']);
            
            // Проверка, есть ли учителя с этим предметом
            $checkSql = "SELECT COUNT(*) as count FROM teachers WHERE subject_id = $id";
            $checkResult = $conn->query($checkSql);
            $row = $checkResult->fetch_assoc();
            
            if ($row['count'] > 0) {
                http_response_code(400);

                echo json_encode(['error' => 'Невозможно удалить предмет, так как с ним связаны преподаватели']);
            } else {
                $sql = "DELETE FROM subjects WHERE id = $id";
                if ($conn->query($sql)) {
                    echo json_encode(['success' => true, 'message' => 'Предмет удален']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Ошибка при удалении предмета: ' . $conn->error]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID предмета обязательно']);
        }
        break;
         default:
        http_response_code(405);
        echo json_encode(['error' => 'Метод не поддерживается']);
        break;
}

$conn->close();
?>