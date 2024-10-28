<?php

require_once('Booking.php');

$env = parse_ini_file('.env');

$db_connection = mysqli_connect($env['DB_HOST'], $env['DB_USERNAME'], $env['DB_PASSWORD'], $env['DB_DATABASE'], $env['DB_PORT']);

if ($db_connection == false){
    die("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

/*
 * раскомментировать при необходимости создать таблицу данных
 * */
/*$sql = "CREATE TABLE `orders` (
`id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `event_date` datetime NOT NULL,
  `ticket_adult_price` int(10) UNSIGNED NOT NULL,
  `ticket_adult_quantity` int(10) UNSIGNED NOT NULL,
  `ticket_kid_price` int(10) UNSIGNED NOT NULL,
  `ticket_kid_quantity` int(10) UNSIGNED NOT NULL,
  `barcode` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equal_price` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL
);";
if ($db_connection->query($sql) !== true) {
    die('Ошибка MySql: '.$db_connection->error);
}*/


$booking = new Booking($db_connection);

$result = $booking->create_booking(3, '2021-08-21 13:00:00', 700, 1, 450, 0);
var_dump($result);

$result = $booking->create_booking(6, '2021-07-29 18:00:00', 1000, 0, 800, 2);
var_dump($result);

$result = $booking->create_booking(3, '2021-08-15 17:00:00', 700, 4, 450, 3);
var_dump($result);