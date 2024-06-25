<?php
// Подключаем библиотеку для работы с API RetailCRM
require_once 'vendor/autoload.php';

use RetailCrm\Api\Client\Client;

// Создаем клиента для работы с API
$client = new Client(
    'https://dimm.retailcrm.ru',
    'wj0uMxwdUVX7PBEIjTB6h0GQ1lyJYkkg'
);

// Получаем все заказы
$orders = [];
$page = 1;
do {
    $response = $client->request->ordersList([], $page, 100);
    if ($response->isSuccessful() && !empty($response['orders'])) {
        $orders = array_merge($orders, $response['orders']);
        $page++;
    } else {
        break;
    }
} while (true);

// Анализируем данные заказов
$itemsCount = [];
$itemsSum = [];

foreach ($orders as $order) {
    if (!empty($order['items'])) {
        foreach ($order['items'] as $item) {
            $name = $item['offer']['name'];
            $quantity = $item['quantity'];
            $sum = $item['initialPrice'] * $quantity;

            if (!isset($itemsCount[$name])) {
                $itemsCount[$name] = 0;
            }
            if (!isset($itemsSum[$name])) {
                $itemsSum[$name] = 0;
            }

            $itemsCount[$name] += $quantity;
            $itemsSum[$name] += $sum;
        }
    }
}

// Находим топ товар по количеству и сумме
$topItemByCount = array_keys($itemsCount, max($itemsCount))[0];
$topItemBySum = array_keys($itemsSum, max($itemsSum))[0];

// Выводим результаты
echo "Топ товар по количеству в заказах: $topItemByCount - " . $itemsCount[$topItemByCount] . " штук\n";
echo "Топ товар по сумме в заказах: $topItemBySum - " . $itemsSum[$topItemBySum] . " руб.\n";

// Бонусное задание: создаем задачу для менеджера
$taskResponse = $client->request->tasksCreate([
    'text' => "Проверить тестовое задание\nФамилия Имя исполнителя\nСсылка на код",
    'performerId' => 6,
    'date' => date('Y-m-d H:i:s'),
    'time' => date('H:i')
]);

if ($taskResponse->isSuccessful()) {
    echo "Задача успешно создана\n";
} else {
    echo "Ошибка при создании задачи\n";
}

?>

