//Задание 1 
let user = {
    name: "Миша",
    age: 87
}
user.sayHi = function () {
    alert("Привет!");
}
user.sayHi();

//Задание 2 
let customer = {
    Name: "Криштиану",
    surname: "Роналду",
    address: "ул. Арбат, 1",
    phone: "+7 977 777 77 77",
    mail: "ronaldo@mail.ru"
};

alert("Покупатель с именем " + customer.Name + ", фамилией " + customer.surname + " и почтой " + customer.mail);

//Задание 3
let newCustomer = {
    name: "Лионель",
    age: 13,
};
let order = {
    components: ["Крылья ", "Кола"],
    status: "Ожидание"
}
let restaurant = {
    name: "КФС",
    location: "Челябинске",

};
if (order.status === "Ожидание") {
    alert(`Заказ ${order.components} от пользователя ${newCustomer.name} в ресторане ${restaurant.name} в ${restaurant.location} ждет приготовления.`);
}

//Задание 4
function userName(name) {
    this.name = name;
    this.isAdmin = false;
}

let userNameFirst = new userName("Миша");
let userNameSecond = new userName("Матвей");
let userNameThird = new userName("Даша");

alert("Некоторые имена группы: " + userNameFirst.name + ", " + userNameSecond.name + ", " + userNameThird.name);

//Задание 5
function student(name, group, cours) {
    this.name = name;
    this.group = group;
    this.cours = cours;

    this.sayHi = function () {
        alert(`Меня зовут ${this.name}, я учусь в группе ${this.group} на ${this.cours} курсе`);
    };
}

let misha = new student("Миша", "ИСП-310", "3");

misha.sayHi();

