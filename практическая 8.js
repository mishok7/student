// Задание 1
var emptyArrayFirst = []; // Пустой массив для хранения случайных чисел
for (var i = 0; i < 15; i++) {

    // 15 случайных чисел в интервале [-10, 30]
    emptyArrayFirst.push(Math.floor(Math.random() * (30 - (-10) + 1)) + (-10));
}
console.log("Массив:", emptyArrayFirst); // Вывод сгенерированного массив

var plusSumma = 0; // Переменная для суммы положительных элементов
var plusCalculate = 0; // Переменная для подсчета положительных элементов
for (var j = 0; j < emptyArrayFirst.length; j++) {
    if (emptyArrayFirst[j] > 0) { // Проверяем, является ли элемент положительным
        plusSumma += emptyArrayFirst[j]; // Добавляем положительный элемент к сумме
        plusCalculate++; // Увеличиваем счетчик положительных элементов
    }
}

var averagePlus = plusCalculate > 0 ? plusSumma / plusCalculate : 0; // Вычисление среднего арифметического положительных элементов
console.log("Среднее арифметическое положительных элементов:", averagePlus); // Вывод результата

// Задание 2
var n = 15;
var emptyArraySecond = []; // Новый массив
for (var i = 0; i < n; i++) {
    // Новые 15 случайных чисел в интервале [-10, 30]
    emptyArraySecond.push(Math.floor(Math.random() * (30 - (-10) + 1)) + (-10));
}
console.log("Исходный массив:", emptyArraySecond); // Вывод исходного массива

for (var j = 0; j < emptyArraySecond.length; j++) {
    if (emptyArraySecond[j] < 0) { // Проверка, является ли элемент отрицательным
        emptyArraySecond[j] = emptyArraySecond[j] * emptyArraySecond[j]; // Если отрицательный, то заменяем на его квадрат
    }
}

console.log("Массив после замены отрицательных элементов:", emptyArraySecond); // Вывод измененного массива

// Задание 3
var emptyArrayThird = []; // Новый массив
for (var i = 0; i < n; i++) {
    // Новые 15 случайные числа в интервале [-10, 30]
    emptyArrayThird.push(Math.floor(Math.random() * (30 - (-10) + 1)) + (-10));
}
console.log("Массив:", emptyArrayThird); // Выводим массив

// Переменные для сумм и счетчиков
var plusSummaSecond = 0, minusSummaSecond = 0;
var plusCalculateSecond = 0, minusCalculateSecond = 0, zeroCalculate = 0;

for (var j = 0; j < emptyArrayThird.length; j++) {

    if (emptyArrayThird[j] > 0) { // Проверка, является ли элемент положительным
        plusSummaSecond += emptyArrayThird[j]; // Добавление к сумме, если положительный
        plusCalculateSecond++; // Увеличение счетчика положительных

    } else if (emptyArrayThird[j] < 0) { // Проверка, является ли элемент отрицательным
        minusSummaSecond += emptyArrayThird[j]; // Добавление к сумме, если отрицательный
        minusCalculateSecond++; // Увеличение счетчика отрицательных

    } else {
        zeroCalculate++; // Увеличение счетчика нулей
    }
}

// Вычисление средних значений
var averagePlusSecond = plusCalculateSecond > 0 ? plusSummaSecond / plusCalculateSecond : 0;
var averageMinusSecond = minusCalculateSecond > 0 ? minusSummaSecond / minusCalculateSecond : 0;

// Вывод результатов
console.log("Среднее арифметическое положительных чисел:", averagePlusSecond);
console.log("Среднее арифметическое отрицательных чисел:", averageMinusSecond);
console.log("Количество нулей:", zeroCalculate);

// Задание 4
var redCalculate = 0; // Счетчик для красных значений
var quantityTrials = 1000000; // Количество испытаний

for (var i = 0; i < quantityTrials; i++) {
    var value = Math.floor(Math.random() * 3); // Генерация случайного числа от 0 до 2 (красный, черный, белый)
    if (value === 0) { // Если выпало красное значение (0)
        redCalculate++; // Увеличение счетчика красных значений
    }
}
console.log("Максимальное количество раз, когда выпадало красное значение:", redCalculate); // Вывод результата