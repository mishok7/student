// Функция для загрузки контента из базы данных
function loadContent() {
    fetch('response.php') // Запрос к PHP-скрипту
        .then(response => {
            if (!response.ok) {
                throw new Error('Сеть ответила с ошибкой: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            displayContent(data); // Вызов функции для отображения контента
        })
        .catch(error => {
            console.error('Ошибка:', error.message);
        });
}

// Функция для отображения полученного контента
function displayContent(data) {
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