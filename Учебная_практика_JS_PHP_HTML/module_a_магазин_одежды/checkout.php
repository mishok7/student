<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Корзина пуста'
    ]);
    exit;
}

// Начинаем транзакцию
mysqli_begin_transaction($db);

try {
    $success = true;
    $error_message = '';

    // Проверяем наличие всех товаров
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $query = "SELECT quantity FROM products WHERE id = ? FOR UPDATE";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        if ($product['quantity'] < $quantity) {
            $success = false;
            $error_message = 'Недостаточно товара в наличии';
            break;
        }
    }

    if ($success) {
        // Обновляем количество всех товаров
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $quantity, $productId);
            
            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
                $error_message = 'Ошибка при обновлении количества товаров';
                break;
            }
        }
    }

    if ($success) {
        // Если все успешно, подтверждаем транзакцию
        mysqli_commit($db);
        // Очищаем корзину
        $_SESSION['cart'] = array();
        echo json_encode([
            'success' => true,
            'message' => 'Заказ успешно оформлен'
        ]);
    } else {
        // Если была ошибка, откатываем транзакцию
        mysqli_rollback($db);
        echo json_encode([
            'success' => false,
            'message' => $error_message
        ]);
    }
} catch (Exception $e) {
    // В случае исключения откатываем транзакцию
    mysqli_rollback($db);
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при оформлении заказа'
    ]);
}
?>