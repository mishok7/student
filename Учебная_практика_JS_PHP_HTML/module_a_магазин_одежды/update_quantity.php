<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Проверяем тип запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    // Получаем товары из сессионной корзины
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'Корзина пуста']);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Пользователь не авторизован']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    
    // Начинаем транзакцию
    mysqli_begin_transaction($db);
    
    try {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Проверяем наличие товара
            $query = "SELECT quantity FROM products WHERE id = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            
            if ($product['quantity'] < $quantity) {
                throw new Exception("Недостаточно товара на складе");
            }
            
            // Обновляем количество товара
            $update_query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmt = mysqli_prepare($db, $update_query);
            mysqli_stmt_bind_param($stmt, "ii", $quantity, $product_id);
            mysqli_stmt_execute($stmt);
            
            // Создаем запись в таблице orders
            $order_query = "INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())";
            $stmt = mysqli_prepare($db, $order_query);
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $quantity);
            mysqli_stmt_execute($stmt);
        }
        
        // Очищаем корзину после успешной покупки
        $_SESSION['cart'] = array();
        
        // Если все успешно, подтверждаем транзакцию
        mysqli_commit($db);
        echo json_encode(['status' => 'success', 'message' => 'Заказ успешно оформлен']);
        
    } catch (Exception $e) {
        // В случае ошибки откатываем все изменения
        mysqli_rollback($db);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// Обработка AJAX-запроса для обновления количества
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id']) && isset($data['change'])) {
    $productId = $data['id'];
    $change = $data['change'];

    if (!isset($_SESSION['cart'][$productId])) {
        echo json_encode([
            'success' => false,
            'message' => 'Товар не найден в корзине'
        ]);
        exit;
    }

    $newQuantity = $_SESSION['cart'][$productId] + $change;

    if ($newQuantity < 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Количество не может быть меньше 1'
        ]);
        exit;
    }

    if ($change > 0) {
        $query = "SELECT quantity FROM products WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        if ($product['quantity'] < 1) {
            echo json_encode([
                'success' => false,
                'message' => 'Недостаточно товара в наличии'
            ]);
            exit;
        }
    }

    $_SESSION['cart'][$productId] = $newQuantity;

    echo json_encode([
        'success' => true,
        'message' => 'Количество обновлено'
    ]);
}
?> 