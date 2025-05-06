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
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    
    // Начинаем транзакцию
    $db->begin_transaction();
    
    try {
        // Добавляем основные данные
        $query = "INSERT INTO products (name, description, price, quantity) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssdd", $name, $description, $price, $quantity);
        $stmt->execute();
        
        $productId = $db->insert_id;
        
        // Если загружено изображение
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageFileName = time() . '_' . basename($image['name']); // Добавляем временную метку
            
            if (move_uploaded_file($image['tmp_name'], $imageFileName)) {
                $query = "UPDATE products SET image = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("si", $imageFileName, $productId);
                $stmt->execute();
            }
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