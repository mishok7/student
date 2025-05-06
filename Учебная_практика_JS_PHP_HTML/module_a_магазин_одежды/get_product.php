<?php
session_start();
require_once 'db_connect.php';

// Проверка прав доступа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    
    // Получаем данные о товаре
    $query = "SELECT id, name, description, price, quantity, image FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        // Возвращаем данные в формате JSON
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Товар не найден']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID товара не указан']);
}

function getProductDetails($product_id) {
    global $conn;
    $sql = "SELECT id, name, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
} 