function createGame() {
    // Получаем элемент canvas и его контекст для рисования
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    // Устанавливаем размеры игрового поля
    canvas.width = 800;
    canvas.height = 600;

    // Игровые константы для удобной настройки параметров
    const PLAYER_SPEED = 3.2;        // Скорость движения игрока
    const BULLET_SPEED = 7;          // Скорость пуль игрока
    const ENEMY_SPEED = 3.5;         // Скорость движения врагов
    const ENEMY_BULLET_SPEED = 5;    // Скорость пуль врагов
    const SHOOT_DELAY = 1000;        // Задержка между выстрелами игрока (1 секунда)
    const ENEMY_SHOOT_DELAY = 5000;  // Задержка между выстрелами врагов (5 секунд)
    const MOVE_DISTANCE = 40;        // Расстояние движения врагов по квадрату

    // Изменяем константы для таймера
    const GAME_DURATION = 90;   // Длительность игры в секундах
    const TIMER_HEIGHT = 20;    // Меняем местами высоту и ширину
    const TIMER_WIDTH = 400;    // для горизонтальной шкалы

    // Цвета для разных элементов игры
    const COLORS = {
        player: '#4ECDC4',    // Бирюзовый для игрока
        enemy: '#FF6B6B',     // Красный для врагов
        bullet: '#FF0000',    // Красный для пуль игрока
        enemyBullet: '#FFFFFF' // Белый для пуль врагов
    };

    // Добавляем константы для возрождения
    const RESPAWN_DELAY = 1000;     // Задержка возрождения (1 секунда)
    const RESPAWN_DURATION = 1000;   // Длительность анимации появления (1 секунда)
    const SPAWN_START_Y = canvas.height + 50;  // Начальная позиция за пределами экрана

    // Состояние игры - содержит все изменяемые данные
    let state = {
        player: createPlayer(),      // Объект игрока
        bullets: [],                 // Массив пуль игрока
        enemyBullets: [],           // Массив пуль врагов
        enemies: createEnemies(),    // Массив врагов
        score: 0,                   // Счет игры
        lives: 5,                   // Количество жизней
        wave: 1,                    // Текущая волна
        keys: {},                    // Объект для отслеживания нажатых клавиш
        timeLeft: GAME_DURATION,  // Оставшееся время в секундах
        lastTimerUpdate: Date.now(),  // Время последнего обновления таймера
        isRunning: false,  // Флаг запущена ли игра
        animationFrame: null,  // Для хранения ID анимации
        playerRespawning: false,     // Флаг возрождения
        respawnStartTime: 0,         // Время начала возрождения
        respawnProgress: 0           // Прогресс анимации (0-1)
    };

    // Получаем кнопки
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');

    // Функция запуска игры
    function startGame() {
        if (!state.isRunning) {
            state.isRunning = true;
            startButton.disabled = true;
            stopButton.disabled = false;
            gameLoop();
        }
    }

    // Функция остановки игры
    function stopGame() {
        if (state.isRunning) {
            endGame('stopped');
        }
    }

    // Обработка выстрела
    function handleShoot(e) {
        if (state.isRunning && e.code === 'Space') {
            shoot();
        }
    }

    // Создает и возвращает объект игрока с начальными параметрами
    function createPlayer() {
        return {
            x: canvas.width / 2,     // Позиция по центру
            y: canvas.height - 50,    // Отступ снизу
            width: 50,               // Ширина игрока
            height: 30,              // Высота игрока
            lastShot: 0              // Время последнего выстрела
        };
    }

    // Создает массив врагов с начальными позициями
    function createEnemies() {
        const enemies = [];
        const count = 10;            // Количество врагов
        const margin = 100;          // Отступ от краев экрана
        // Вычисляем расстояние между врагами
        const spacing = (canvas.width - 2 * margin) / (count - 1);

        for (let i = 0; i < count; i++) {
            const x = margin + i * spacing;
            enemies.push({
                x,
                y: 100,              // Начальная высота
                width: 30,
                height: 30,
                initialX: x,         // Сохраняем начальную позицию
                initialY: 100,
                moveState: 0,        // Состояние движения (0-3)
                moveProgress: 0,     // Прогресс движения
                isEven: i % 2 === 0, // Четный/нечетный для разной траектории
                lastShot: Math.random() * ENEMY_SHOOT_DELAY // Случайная задержка первого выстрела
            });
        }
        return enemies;
    }

    // Обработка ввода пользователя
    function handleInput() {
        const { player, keys } = state;
        const RESTRICTED_ZONE = 250; // Зона ограничения сверху
        const MAX_Y = canvas.height - 50; // Нижняя граница (отступ от низа экрана)

        // Движение в 4 направлениях
        if (keys['ArrowLeft']) player.x -= PLAYER_SPEED;
        if (keys['ArrowRight']) player.x += PLAYER_SPEED;
        if (keys['ArrowUp']) player.y -= PLAYER_SPEED;
        if (keys['ArrowDown']) player.y += PLAYER_SPEED;

        // Ограничение движения игрока
        player.x = Math.max(0, Math.min(canvas.width - player.width, player.x));
        // Ограничиваем движение по Y между RESTRICTED_ZONE и нижней границей
        player.y = Math.max(canvas.height - RESTRICTED_ZONE, Math.min(MAX_Y, player.y));
    }

    // Создание выстрела игрока
    function shoot() {
        const { player, bullets } = state;
        const now = Date.now();
        if (now - player.lastShot >= SHOOT_DELAY) {
            bullets.push({
                x: player.x + player.width / 2, // Пуля вылетает из центра игрока
                y: player.y,
                width: 4,
                height: 10
            });
            player.lastShot = now;
        }
    }

    // Обновление положения всех пуль
    function updateBullets() {
        // Обновляем пули игрока и удаляем вышедшие за экран
        state.bullets = state.bullets.filter(bullet => {
            bullet.y -= BULLET_SPEED;
            return bullet.y > 0;
        });

        // Обновляем пули врагов
        state.enemyBullets = state.enemyBullets.filter(bullet => {
            bullet.y += ENEMY_BULLET_SPEED;
            return bullet.y < canvas.height;
        });
    }

    // Обновление положения врага
    function moveEnemy(enemy) {
        enemy.moveProgress += ENEMY_SPEED;
        // Массив направлений движения [dx, dy] для каждого состояния
        const moves = enemy.isEven ?
            [[0, 1], [-1, 0], [0, -1], [1, 0]] : // вниз, влево, вверх, вправо
            [[0, -1], [1, 0], [0, 1], [-1, 0]];  // вверх, вправо, вниз, влево

        // Получаем текущее направление движения
        const [dx, dy] = moves[enemy.moveState];
        // Обновляем позицию врага
        enemy.x = enemy.initialX + dx * enemy.moveProgress;
        enemy.y = enemy.initialY + dy * enemy.moveProgress;

        // Если достигнуто расстояние движения, меняем направление
        if (enemy.moveProgress >= MOVE_DISTANCE) {
            enemy.moveState = (enemy.moveState + 1) % 4;
            enemy.moveProgress = 0;
            enemy.initialX = enemy.x;
            enemy.initialY = enemy.y;
        }

        // Проверяем возможность выстрела
        const now = Date.now();
        if (now - enemy.lastShot >= ENEMY_SHOOT_DELAY && Math.random() < 0.05) {
            state.enemyBullets.push({
                x: enemy.x + enemy.width / 2,
                y: enemy.y + enemy.height,
                width: 4,
                height: 10
            });
            enemy.lastShot = now;
        }
    }

    // Проверка столкновения двух прямоугольников
    function isColliding(rect1, rect2) {
        return rect1.x < rect2.x + rect2.width &&
            rect1.x + rect1.width > rect2.x &&
            rect1.y < rect2.y + rect2.height &&
            rect1.y + rect1.height > rect2.y;
    }

    // Проверка всех столкновений в игре
    function checkCollisions() {
        const { bullets, enemyBullets, enemies, player } = state;

        // Проверяем попадания пуль игрока во врагов
        for (let bi = bullets.length - 1; bi >= 0; bi--) {
            for (let ei = enemies.length - 1; ei >= 0; ei--) {
                if (isColliding(bullets[bi], enemies[ei])) {
                    bullets.splice(bi, 1);
                    enemies.splice(ei, 1);
                    state.score += 100;

                    // Если все враги уничтожены, создаем новую волну
                    if (enemies.length === 0) {
                        state.wave++;
                        state.enemies = createEnemies();
                    }
                    break;
                }
            }
        }

        // Проверяем попадания вражеских пуль в игрока
        if (!state.playerRespawning) {  // Проверяем только если игрок не возрождается
            for (let i = enemyBullets.length - 1; i >= 0; i--) {
                if (isColliding(enemyBullets[i], player)) {
                    enemyBullets.splice(i, 1);
                    state.lives--;

                    if (state.lives <= 0) {
                        endGame('lives');
                    } else {
                        startRespawn();
                    }
                    break;
                }
            }
        }
    }

    // Добавляем функцию обновления таймера
    function updateTimer() {
        const now = Date.now();
        const deltaTime = (now - state.lastTimerUpdate) / 1000;

        if (deltaTime >= 1) {
            state.timeLeft = Math.max(0, state.timeLeft - 1);
            state.lastTimerUpdate = now;

            if (state.timeLeft === 0 && state.enemies.length > 0) {
                endGame('time');
            }
        }
    }

    // Изменяем функцию отрисовки таймера
    function renderTimer() {
        // Располагаем таймер вверху по центру
        const timerX = (canvas.width - TIMER_WIDTH) / 2;  // Центрируем по горизонтали
        const timerY = 15;  // Отступ сверху

        // Рисуем контур шкалы
        ctx.strokeStyle = '#FFFFFF';
        ctx.lineWidth = 2;
        ctx.strokeRect(timerX, timerY, TIMER_WIDTH, TIMER_HEIGHT);

        // Рисуем заполнение шкалы
        const fillWidth = (state.timeLeft / GAME_DURATION) * TIMER_WIDTH;

        // Градиент для шкалы времени (теперь горизонтальный)
        const gradient = ctx.createLinearGradient(timerX, timerY, timerX + TIMER_WIDTH, timerY);
        gradient.addColorStop(0, '#FF0000');    // Зеленый слева
        gradient.addColorStop(0.5, '#FFFF00');  // Желтый в середине
        gradient.addColorStop(1, '#00FF00');    // Красный справа

        ctx.fillStyle = gradient;
        ctx.fillRect(
            timerX,
            timerY,
            fillWidth,
            TIMER_HEIGHT
        );
    }

    // Отрисовка всей игры
    function render() {
        // Очищаем экран
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Отрисовка всех игровых объектов
        renderObject(state.player, COLORS.player);
        state.enemies.forEach(enemy => renderObject(enemy, COLORS.enemy));
        state.bullets.forEach(bullet => renderObject(bullet, COLORS.bullet));
        state.enemyBullets.forEach(bullet => renderObject(bullet, COLORS.enemyBullet));

        renderLives();   // Отрисовка жизней
        renderTimer();   // Отрисовка таймера
        updateUI();      // Обновление интерфейса
    }

    // Отрисовка отдельного объекта
    function renderObject(obj, color) {
        ctx.fillStyle = color;
        // Не рисуем игрока если он в процессе возрождения и еще не начал появляться
        if (obj === state.player && state.playerRespawning) {
            const currentTime = Date.now();
            // Рисуем только если началась анимация появления
            if (currentTime >= state.respawnStartTime) {
                ctx.globalAlpha = 0.5;
                ctx.fillRect(obj.x, obj.y, obj.width, obj.height);
                ctx.globalAlpha = 1.0;
            }
        } else {
            ctx.fillRect(obj.x, obj.y, obj.width, obj.height);
        }
    }

    // Отрисовка индикаторов жизней
    function renderLives() {
        const size = 20;     // Размер квадрата жизни
        const gap = 5;       // Отступ между жизнями
        // Вычисляем общую высоту всех жизней
        const totalHeight = (size + gap) * state.lives - gap;
        // Центрируем по вертикали
        const startY = (canvas.height - totalHeight) / 2;

        ctx.fillStyle = COLORS.player;
        for (let i = 0; i < state.lives; i++) {
            ctx.fillRect(10, startY + (size + gap) * i, size, size);
        }
    }

    // Обновление текстового интерфейса
    function updateUI() {
        document.getElementById('scoreValue').textContent = state.score;
        document.getElementById('livesValue').textContent = state.lives;
        document.getElementById('waveValue').textContent = state.wave;
    }

    // Основной игровой цикл
    function gameLoop() {
        if (state.isRunning) {
            handleInput();
            updateBullets();
            state.enemies.forEach(moveEnemy);
            updateRespawn();  // Добавляем обновление возрождения
            checkCollisions();
            updateTimer();
            render();
            state.animationFrame = requestAnimationFrame(gameLoop);
        }
    }

    // Инициализация управления
    document.addEventListener('keydown', e => state.keys[e.code] = true);
    document.addEventListener('keyup', e => state.keys[e.code] = false);
    document.addEventListener('keypress', handleShoot);

    // Добавляем обработчики для кнопок
    startButton.addEventListener('click', startGame);
    stopButton.addEventListener('click', stopGame);

    // Инициализация начального состояния
    render(); // Отрисовываем начальное состояние

    // Добавляем функцию начала возрождения
    function startRespawn() {
        state.playerRespawning = true;
        // Сохраняем текущую X позицию игрока
        const playerX = state.player.x;
        // Сбрасываем позицию игрока за пределы экрана
        state.player.y = SPAWN_START_Y;
        state.player.x = playerX; // Сохраняем горизонтальную позицию
        state.respawnStartTime = Date.now() + RESPAWN_DELAY;
    }

    // Добавляем функцию обновления возрождения
    function updateRespawn() {
        if (state.playerRespawning) {
            const currentTime = Date.now();

            if (currentTime >= state.respawnStartTime) {
                // Вычисляем прогресс анимации
                const elapsed = currentTime - state.respawnStartTime;
                state.respawnProgress = Math.min(elapsed / RESPAWN_DURATION, 1);

                // Плавно перемещаем игрока на стартовую позицию
                const targetY = canvas.height - 50;
                const startY = SPAWN_START_Y;
                state.player.y = startY + (targetY - startY) * state.respawnProgress;

                // Завершаем возрождение
                if (state.respawnProgress >= 1) {
                    state.playerRespawning = false;
                    state.respawnProgress = 0;
                }
            }
        }
    }

    // Добавляем функцию окончания игры
    function endGame(reason) {
        state.isRunning = false;
        cancelAnimationFrame(state.animationFrame);

        const gameOver = document.getElementById('gameOver');
        const finalScore = document.getElementById('finalScore');
        const restartButton = document.getElementById('restartButton');

        finalScore.textContent = state.score;
        gameOver.classList.remove('hidden');

        // Обработчик для кнопки перезапуска
        restartButton.onclick = () => location.reload();
    }
}

// Запуск игры после загрузки страницы
window.onload = createGame; 