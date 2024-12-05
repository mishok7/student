//Задание 1
let bankClient = {
    name: "Василий Василин",
    clientId: Symbol("client-id"),
};

let clinicWorker = {
    name: "Иван Иванов",
    workerId: Symbol("worker-id"),
};

let national = {
    name: "Петр Петров",
    nationalId: Symbol("national-id"),
};

document.write("Bank Client: " + bankClient.name + "<br>");
document.write("Clinic Worker: " + clinicWorker.name + "<br>");
document.write("national: " + national.name + "<br>");

console.log("Bank Client ID:", bankClient.clientId);
console.log("Clinic Worker ID:", clinicWorker.workerId);

//Задание 2 
const number = +prompt('Загадайте цифру до 9', '');
switch (number) {
    case 1:
        alert('Вы ввели число 1');
        break;
    case 2:
        alert('Вы ввели число 2');
        break;
    case 3:
        alert('Вы ввели число 3');
        break;
    case 4:
        alert('Вы ввели число 4');
        break;
    case 5:
        alert('Вы ввели число 5');
        break;
    case 6:
        alert('Вы ввели число 6');
        break;
    case 7:
        alert('Вы ввели число 7');
        break;
    case 8:
    case 9:
        alert('Вы ввели число 8, а может и 9');
        break;
    default:
        alert('Некорректный ввод');
}

//Задание 3
const min = 7;
let quarter;
switch (true) {
    case (min >= 0 && min <= 14):
        quarter = "Первую четверть";
        break;
    case (min >= 15 && min <= 29):
        quarter = "Вторую четверть";
        break;
    case (min >= 30 && min <= 44):
        quarter = "Третью четверть";
        break;
    case (min >= 45 && min <= 59):
        quarter = "Четвертую четверть";
        break;
    default:
        quarter = "Некорректное значение";
}
console.log(`Минута ${min} попадает в ${quarter}`);

//Задание 4
let string = prompt('Введите строку с цифрами: ');
let phoneNumber = prompt('Введите ваш номер телефона :');
if (string.length > 0) {
    let first = string.charAt(0);
    if (first === '1' || first === '2' || first === '3') {
        console.log('Первая цифра 1, 2 или 3');
    } else {
        console.log('нет');
    }
} else {
    console.log('Строка пустая');
}

if (string.includes(phoneNumber)) {
    console.log('Номер телефона найден в строке');
} else {
    console.log('Номер телефона не найден в строке');
}