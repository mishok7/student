<?php
session_start();
include 'db_connect.php';

// Устанавливаем заголовок JSON до любого вывода
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if (!isset($_SESSION['cart'])) {
        echo json_encode([]);
        exit;
    }

    $cartItems = [];

    foreach ($_SESSION['cart'] as $productId => $cartQuantity) {
        $query = "SELECT id, name, price, quantity FROM products WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . mysqli_error($db));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $productId);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Ошибка выполнения запроса: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        
        if ($product = mysqli_fetch_assoc($result)) {
            
            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'image' => $image,
                'size' => 'M',
                'quantity' => $cartQuantity,
                'stock' => (int)$product['quantity']
            ];
        }
        
        mysqli_stmt_close($stmt);
    }

    echo json_encode($cartItems);

} catch (Exception $e) {
    // Логируем ошибку (можно настроить логирование в файл)
    error_log("Ошибка в get_cart.php: " . $e->getMessage());
    
    // Возвращаем клиенту JSON с сообщением об ошибке
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Произошла ошибка при получении данных корзины'
    ]);
}
?> 