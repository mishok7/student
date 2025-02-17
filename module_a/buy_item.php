<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'];

if (isset($_SESSION['cart'][$productId])) {
    // Получаем количество товара в корзине
    $quantityToBuy = $_SESSION['cart'][$productId];
    
    // Проверяем, достаточно ли товара в наличии
    $query = "SELECT quantity FROM products WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if ($product['quantity'] >= $quantityToBuy) {
        // Уменьшаем количество товара в базе данных
        $query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $quantityToBuy, $productId);
        
        if (mysqli_stmt_execute($stmt)) {
            // Удаляем товар из корзины
            unset($_SESSION['cart'][$productId]);
            echo json_encode([
                'success' => true, 
                'message' => 'Товар успешно куплен'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Ошибка при обновлении количества'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Недостаточно товара в наличии'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Товар не найден в корзине'
    ]);
}
?> 