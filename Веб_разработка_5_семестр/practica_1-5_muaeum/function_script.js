// Функция для загрузки контента
function loadAllContent() {
    fetch('response_single.php') // Запрос к PHP-скрипту
        .then(response => {
            if (!response.ok) {
                throw new Error('Сеть ответила с ошибкой: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            displayAllContent(data); // Вызов функции для отображения контента
        })
        .catch(error => {
            console.error('Ошибка:', error.message);
        });
}

// Функция для отображения всего контента
function displayAllContent(data) {
    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = ''; // Очистка предыдущего контента
    data.forEach(item => {
        const article = document.createElement('div');
        article.innerHTML = `
            <h2>${item.title}</h2>
            <p>${item.text}</p>
            <img src="${item.image}" alt="${item.title}">
            <a href="${item.link}">Читать далее</a>
        `;
        contentDiv.appendChild(article);
    });
}

// Функция для загрузки сингл-записи по ID
function loadSingleContent(id) {
    fetch(`load_single_content.php?id=${id}`) // Запрос к PHP-скрипту с ID
        .then(response => {
            if (!response.ok) {
                throw new Error('Сеть ответила с ошибкой: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Сингл-запись загружена, но не отображается
            console.log('Сингл-запись загружена:', data);
        })
        .catch(error => {
            console.error('Ошибка:', error.message);
        });
}

// Вызов функций при загрузке страницы
window.onload = function () {
    loadAllContent(); // Загружаем весь контент
    loadSingleContent(); // Загружаем сингл-запись 
};