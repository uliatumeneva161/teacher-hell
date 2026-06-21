<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../db/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Получить все тесты с названием предмета
        $sql = "SELECT t.*, s.name as subject_name, 
                DATE_FORMAT(t.created_at, '%d.%m.%Y') as created_date
                FROM tests t 
                LEFT JOIN subjects s ON t.subject_id = s.id 
                ORDER BY t.created_at DESC";
        
        $result = $conn->query($sql);
        $tests = [];
        while ($row = $result->fetch_assoc()) {
            $tests[] = $row;
        }
        echo json_encode($tests);
        break;
        
    
    case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['name']) || empty($data['subject_id']) || empty($data['questions'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Не все поля заполнены']);
        break;
    }
    
    $conn->begin_transaction();
    
    try {
        $name = $conn->real_escape_string($data['name']);
        $subject_id = intval($data['subject_id']);
        $question_count = count($data['questions']);
        
        $stmt = $conn->prepare("INSERT INTO tests (name, subject_id, question_count) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $name, $subject_id, $question_count);
        $stmt->execute();
        $test_id = $conn->insert_id;
        
        foreach ($data['questions'] as $q_index => $question) {
            $q_text = $conn->real_escape_string($question['text']);
            $q_order = $q_index + 1;
            
            $stmt = $conn->prepare("INSERT INTO questions (test_id, question_text, question_order) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $test_id, $q_text, $q_order);
            $stmt->execute();
            $question_id = $conn->insert_id;
            
            foreach ($question['options'] as $opt_index => $option) {
                $opt_text = $conn->real_escape_string($option['text']);
                $is_correct = $option['isCorrect'] ? 1 : 0;
                
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $question_id, $opt_text, $is_correct);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'test_id' => $test_id]);
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка сохранения: ' . $e->getMessage()]);
    }
    break;            
              
        
    case 'DELETE':
        // Удалить тест
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data['id'])) {
            $id = intval($data['id']);
            
            $sql = "DELETE FROM tests WHERE id = $id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Тест удален']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Ошибка при удалении теста: ' . $conn->error]);
                  }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID теста обязательно']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Метод не поддерживается']);
        break;
}

$conn->close();
?>
