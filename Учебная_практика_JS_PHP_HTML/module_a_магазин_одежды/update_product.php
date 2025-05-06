<?php
session_start();
require_once 'db_connect.php';

// Проверка прав доступа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    
    // Начинаем транзакцию
    $db->begin_transaction();
    
    try {
        // Обновляем основные данные
        $sql = "UPDATE products SET 
                name = ?,
                description = ?,
                price = ?,
                quantity = ?
                WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssddi", $name, $description, $price, $quantity, $id);
        $stmt->execute();
        
        // Если загружено новое изображение
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageFileName = time() . '_' . basename($image['name']);
            
            if (move_uploaded_file($image['tmp_name'], $imageFileName)) {
                // Получаем старое изображение
                $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $oldImage = $row['image'];
                
                // Удаляем старое изображение
                if ($oldImage && file_exists($oldImage)) {
                    unlink($oldImage);
                }
                
                $query = "UPDATE products SET image = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("si", $imageFileName, $id);
                $stmt->execute();
            }
        } else if (isset($_FILES['image'])) {
            error_log("Upload error code: " . $_FILES['image']['error']);
        }
        
        $db->commit();
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}