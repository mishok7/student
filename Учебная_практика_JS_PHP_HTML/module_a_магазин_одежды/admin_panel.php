<?php
session_start();
require_once 'db_connect.php';

// Проверка прав доступа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
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

        .modal-content {
            border: none;
            border-radius: 12px;
        }
        
        .modal-body {
            border-radius: 12px;
        }
        
        .object-fit-cover {
            object-fit: cover;
        }
        
        #editProductImage {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }
        
        @media (max-width: 767.98px) {
            #editProductImage {
                border-top-right-radius: 12px;
                border-bottom-left-radius: 0;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
        <!-- Верхняя полоса -->
        <div class="row bg-dark py-2">
            <div class="col-12 d-flex justify-content-between align-items-center px-4">
                <div class="text-white">
                    <a href="http://localhost/module_a/admin_panel.php" class="text-white text-decoration-none">Привет, администратор!</a>
                </div>
                <div class="text-white">
                    
                    <a href="http://localhost/module_a/index.html" class="text-white text-decoration-none" onclick="return confirmExit()">Выйти</a>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="row main-content">
            <!-- Большой блок с товарами -->
            <div class="col-9 p-3">
                <div class="content-box h-100">


                    <!-- Добавляем кнопку -->
                    <div class="row mb-4">
                        <div class="col">
                            <button class="btn btn-success w-100" onclick="addNewProduct()">
                                <i class="bi bi-plus-lg me-2"></i>Добавить товар
                            </button>
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
                                    <p class="card-text stock text-success">В наличии <span class="badge bg-secondary"><?php echo $product['quantity']; ?> шт.</span></p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark flex-grow-1" onclick="editProduct(<?php echo $product['id']; ?>)">Изменить</button>
                                        <button class="btn btn-danger" style="width: 40px;" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
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

    <!-- Модальное окно для редактирования товара -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row g-0">
                        <!-- Левая часть с изображением -->
                        <div class="col-md-6">
                            <div class="h-100 position-relative">
                                <img id="editProductImage" src="" class="w-100 h-100 object-fit-cover" style="max-height: 500px;">
                                <input type="file" id="imageInput" class="d-none" accept="image/*">
                                <button class="btn btn-light position-absolute bottom-0 start-0 m-3" onclick="document.getElementById('imageInput').click()">
                                    <i class="bi bi-camera"></i> Изменить фото
                                </button>
                            </div>
                        </div>
                        <!-- Правая часть с формой -->
                        <div class="col-md-6">
                            <div class="p-4">
                                <h4 class="mb-4">Редактирование товара</h4>
                                <form id="editProductForm">
                                    <input type="hidden" id="editProductId">
                                    <div class="mb-3">
                                        <label class="form-label">Название</label>
                                        <input type="text" class="form-control" id="editProductName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Описание</label>
                                        <textarea class="form-control" id="editProductDescription" rows="3" maxlength="50" required></textarea>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label class="form-label">Цена</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="editProductPrice" min="0" required>
                                                <span class="input-group-text">₽</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Количество</label>
                                            <input type="number" class="form-control" id="editProductQuantity" min="0" required>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-dark">Сохранить изменения</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для добавления товара -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row g-0">
                        <!-- Левая часть с изображением -->
                        <div class="col-md-6">
                            <div class="h-100 position-relative">
                                <img id="newProductImage" src="placeholder.jpg" class="w-100 h-100 object-fit-cover" style="max-height: 500px;">
                                <input type="file" id="newImageInput" class="d-none" accept="image/*">
                                <button class="btn btn-light position-absolute bottom-0 start-0 m-3" onclick="document.getElementById('newImageInput').click()">
                                    <i class="bi bi-camera"></i> Добавить фото
                                </button>
                            </div>
                        </div>
                        <!-- Правая часть с формой -->
                        <div class="col-md-6">
                            <div class="p-4">
                                <h4 class="mb-4">Добавление нового товара</h4>
                                <form id="addProductForm">
                                    <div class="mb-3">
                                        <label class="form-label">Название</label>
                                        <input type="text" class="form-control" id="newProductName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Описание</label>
                                        <textarea class="form-control" id="newProductDescription" rows="3" maxlength="50" required></textarea>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label class="form-label">Цена</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="newProductPrice" min="0" required>
                                                <span class="input-group-text">₽</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Количество</label>
                                            <input type="number" class="form-control" id="newProductQuantity" min="0" required>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success">Добавить товар</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function editProduct(productId) {
        // Получаем данные о товаре
        fetch('get_product.php?id=' + productId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(product => {
                console.log('Полученные данные:', product); // Для отладки
                
                // Заполняем форму данными
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editProductName').value = product.name || '';
                document.getElementById('editProductDescription').value = product.description || '';
                document.getElementById('editProductPrice').value = product.price || '';
                document.getElementById('editProductQuantity').value = product.quantity || '';
                
                // Проверяем и устанавливаем изображение
                if (product.image) {
                    document.getElementById('editProductImage').src = product.image;
                }
                
                // Показываем модальное окно
                new bootstrap.Modal(document.getElementById('editProductModal')).show();
            })
            .catch(error => {
                console.error('Ошибка при получении данных:', error);
                alert('Ошибка при получении данных о товаре');
            });
    }

    // Добавляем обработчик отправки формы
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('id', document.getElementById('editProductId').value);
        formData.append('name', document.getElementById('editProductName').value);
        formData.append('description', document.getElementById('editProductDescription').value);
        formData.append('price', document.getElementById('editProductPrice').value);
        formData.append('quantity', document.getElementById('editProductQuantity').value);
        
        const imageInput = document.getElementById('imageInput');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        
        fetch('update_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Перезагружаем страницу для отображения изменений
            } else {
                alert('Ошибка при обновлении товара: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении товара');
        });
    });

    // Обработчик изменения изображения
    document.getElementById('imageInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editProductImage').src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    function deleteProduct(productId) {
        if (confirm('Вы уверены, что хотите удалить этот товар?')) {
            fetch('delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'productId=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Удаляем карточку товара из DOM
                    document.getElementById('product-' + productId).remove();
                } else {
                    alert('Ошибка при удалении товара: ' + (data.message || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при удалении товара');
            });
        }
    }

    function confirmExit() {
        return confirm("Вы точно хотите выйти?");
    }

    function addNewProduct() {
        // Очищаем форму
        document.getElementById('addProductForm').reset();
        document.getElementById('newProductImage').src = 'placeholder.jpg';
        
        // Показываем модальное окно
        new bootstrap.Modal(document.getElementById('addProductModal')).show();
    }

    // Обработчик изменения изображения для нового товара
    document.getElementById('newImageInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('newProductImage').src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Обработчик отправки формы нового товара
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('name', document.getElementById('newProductName').value);
        formData.append('description', document.getElementById('newProductDescription').value);
        formData.append('price', document.getElementById('newProductPrice').value);
        formData.append('quantity', document.getElementById('newProductQuantity').value);
        
        const imageInput = document.getElementById('newImageInput');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        
        fetch('add_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Перезагружаем страницу для отображения нового товара
            } else {
                alert('Ошибка при добавлении товара: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при добавлении товара');
        });
    });

    
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