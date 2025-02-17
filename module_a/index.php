<?php
session_start();
include 'db_connect.php'; // Подключение к базе данных

// Инициализируем корзину только если она не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Получаем количество товаров в корзине
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Добавим проверку на числовые значения
    foreach ($_SESSION['cart'] as $item_quantity) {
        if (is_numeric($item_quantity)) {
            $cart_count += (int)$item_quantity;
        }
    }
}

error_log('Cart contents: ' . print_r($_SESSION['cart'], true));
error_log('Cart count: ' . $cart_count);
?>

   <!DOCTYPE html>
   <html lang="ru">
   <head>
       <meta charset="UTF-8">
       <title>Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .main-content {
            min-height: 90vh;
        }

        .content-box {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-container {
            position: relative;
        }

        .card-img-top {
            height: 300px;
            object-fit: cover;
        }

        .size-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .btn-dark {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }

        .btn-dark:hover {
            background-color: #1a252f;
            border-color: #1a252f;
        }

        .cart-counter {
            font-size: 0.75rem;
            transform: translate(-50%, -50%);
        }

        /* Стили для иконок */
        .bi-gift {
            color: #FF0000;
        }
        
        .bi-percent {
            color: #FF5F00;
        }
        
        .bi-stars {
            color: #00A67C;
        }
        
        .bi-bag-heart {
            color: #00CC00;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Верхняя полоса -->
        <div class="row bg-dark py-2">
            <div class="col-12 d-flex justify-content-between align-items-center px-4">
                <div class="text-white">
                    <a href="http://localhost/module_a/index.php" class="text-white text-decoration-none">Fashion Store</a>
                </div>
                <div class="text-white">
                    <div class="d-inline-block position-relative me-3">
                        <i class="bi bi-cart3"></i>
                        <a href="http://localhost/module_a/cart.html" class="text-white text-decoration-none">Корзина</a>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-counter">
                            <?php echo $cart_count; ?>
                        </span>
                    </div>
                    <a href="http://localhost/module_a/index.html" class="text-white text-decoration-none" onclick="return confirmExit()">Выйти</a>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="row main-content">
            <!-- Большой блок с товарами -->
            <div class="col-9 p-3">
                <div class="content-box h-100">

                    <!-- Новый блок с иконками -->
                    <div class="row text-center mb-4">
                        <div class="col">
                            <div class="category-icon">
                                <i class="bi bi-bag-heart fs-1"></i>
                                <p class="mt-2">Новинки</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-icon">
                                <i class="bi bi-stars fs-1"></i>
                                <p class="mt-2">Популярное</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-icon">
                                <i class="bi bi-percent fs-1"></i>
                                <p class="mt-2">Скидки</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-icon">
                                <i class="bi bi-gift fs-1"></i>
                                <p class="mt-2">Акции</p>
                            </div>
                        </div>
                    </div>


                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php
                        // Получаем все товары из базы данных
                        $query = "SELECT * FROM products ORDER BY id";
                        $result = mysqli_query($db, $query);
                        
                        while ($product = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="col" id="product-<?php echo $product['id']; ?>">
                            <div class="card h-100">
                                <div class="card-img-container">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p class="card-text fw-bold"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</p>
                                    <?php if ($product['quantity'] > 0): ?>
                                        <p class="card-text stock text-success">В наличии <span class="badge bg-secondary"><?php echo $product['quantity']; ?> шт.</span></p>
                                        <button class="btn btn-dark w-100" onclick="addToCart(<?php echo $product['id']; ?>)">В корзину</button>
                                    <?php else: ?>
                                        <p class="card-text stock text-danger">Нет в наличии</p>
                                        <button class="btn btn-danger w-100" disabled>Нет в наличии</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Блок фильтров справа -->
            <div class="col-3 p-3">
                <div class="content-box h-100">
                    <h5 class="mb-4">Фильтры</h5>

                    <div class="mb-4">
                        <h6>Категории</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dress">
                            <label class="form-check-label" for="dress">Платья</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="jeans">
                            <label class="form-check-label" for="jeans">Джинсы</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tshirt">
                            <label class="form-check-label" for="tshirt">Футболки</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Размеры</h6>
                        <div class="size-filter">
                            <button class="btn btn-outline-dark btn-sm me-1">XS</button>
                            <button class="btn btn-outline-dark btn-sm me-1">S</button>
                            <button class="btn btn-outline-dark btn-sm me-1">M</button>
                            <button class="btn btn-outline-dark btn-sm me-1">L</button>
                            <button class="btn btn-outline-dark btn-sm">XL</button>
                        </div>
                    </div>

                    <div>
                        <h6>Цена</h6>
                        <div class="price-range">
                            <input type="range" class="form-range" min="0" max="10000" step="100" value="10000">
                            <div class="d-flex justify-content-between mt-2">
                                <span>0 ₽</span>
                                <span>10 000 ₽</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Нижняя полоса -->
        <div class="row bg-dark py-3">
            <div class="col-12 text-center text-white">
                © 2024 Fashion Store. Все права защищены.
            </div>
        </div>
    </div>

    <script>
    function addToCart(productId) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем счетчик корзины
                const cartCounter = document.querySelector('.cart-counter');
                if (cartCounter) {
                    cartCounter.textContent = data.cart_total || '0';
                }
                
                const productCard = document.getElementById(`product-${productId}`);
                const button = productCard.querySelector('button');
                
                // Проверяем, что quantity определено и является числом
                const remainingQuantity = typeof data.quantity === 'number' ? data.quantity : 0;
                
                if (remainingQuantity <= 0) {
                    const stockText = productCard.querySelector('.stock');
                    stockText.className = 'card-text stock text-danger';
                    stockText.textContent = 'Нет в наличии';
                    button.className = 'btn btn-danger w-100';
                    button.textContent = 'Нет в наличии';
                    button.disabled = true;
                } else {
                    const stockText = productCard.querySelector('.stock');
                    stockText.className = 'card-text stock text-success';
                    stockText.innerHTML = `В наличии <span class="badge bg-secondary">${remainingQuantity} шт.</span>`;
                    button.className = 'btn btn-dark w-100';
                    button.textContent = 'В корзину';
                }
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error('Ошибка:', error);
        });
    }
    </script>

    <script>
    function confirmExit() {
        return confirm("Вы точно хотите выйти?");
    }
    </script>

    <script>
    // Добавляем новую функцию для работы с price-range
    document.addEventListener('DOMContentLoaded', function() {
        const priceRange = document.querySelector('.price-range input');
        const priceDisplay = document.createElement('div');
        priceDisplay.className = 'text-center mb-2';
        priceRange.parentNode.insertBefore(priceDisplay, priceRange);

        // Обновляем отображение цены при загрузке
        updatePriceDisplay(priceRange.value);

        // Обновляем отображение цены при перемещении ползунка
        priceRange.addEventListener('input', function() {
            updatePriceDisplay(this.value);
        });

        // Фильтруем товары при отпускании ползунка
        priceRange.addEventListener('change', function() {
            filterProducts(this.value);
        });


        function updatePriceDisplay(value) {
            priceDisplay.textContent = `Выбранная цена: ${value} ₽`;
        }

        function filterProducts(maxPrice) {
            const products = document.querySelectorAll('.card');
            products.forEach(product => {
                const priceText = product.querySelector('.fw-bold').textContent;
                const price = parseInt(priceText.replace(/[^\d]/g, ''));
                
                if (price <= maxPrice) {
                    product.closest('.col').style.display = 'block';
                } else {
                    product.closest('.col').style.display = 'none';
                }
            });
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
   </html>