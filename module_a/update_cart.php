<?php
include 'db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['id'];

    // Проверяем доступное количество товара
    $query = "SELECT quantity FROM products WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    // Получаем текущее количество товара в корзине
    $current_cart_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;

    // Проверяем, можно ли добавить ещё товар
    if ($current_cart_quantity >= $product['quantity']) {
        echo json_encode([
            'success' => false,
            'message' => 'Невозможно добавить больше товара, чем есть в наличии',
            'quantity' => $product['quantity']
        ]);
        exit;
    }

    // Если всё в порядке, добавляем товар в корзину
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id]++;
    }

    $remaining_quantity = $product['quantity'] - $_SESSION['cart'][$product_id];

    // Подсчитываем общее количество товаров в корзине
    $cart_total = 0;
    foreach ($_SESSION['cart'] as $quantity) {
        $cart_total += (int)$quantity;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Товар добавлен в корзину',
        'quantity' => $remaining_quantity,
        'cart_total' => $cart_total
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>