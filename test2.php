<?php

$env = parse_ini_file('.env');

$db_connection = mysqli_connect($env['DB_HOST'], $env['DB_USERNAME'], $env['DB_PASSWORD'], $env['DB_DATABASE'], $env['DB_PORT']);

if ($db_connection == false){
    die("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

function sql_query($db_connection, $sql){
    if ($db_connection->query($sql) !== true) {
        die('Ошибка MySql: '.$db_connection->error);
    }
}

/*
 * создаем новую таблицу с данными билета по заказу
 */
sql_query($db_connection, "CREATE TABLE `tickets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `order_id` INT UNSIGNED NOT NULL,
    `ticket_type` ENUM('adult','kid','discount','group') NOT NULL ,
    `ticket_price` INT UNSIGNED NOT NULL ,
    `ticket_quantity` SMALLINT UNSIGNED NOT NULL
);");
/*
 * создаем связь между таблицами для целостности данных
 * */
sql_query($db_connection, "ALTER TABLE `tickets` ADD CONSTRAINT `tickets_order_id_foreign_key` 
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;");


/*
 * создаем новую таблицу с данными билета по заказу
 */
sql_query($db_connection, "CREATE TABLE `barcodes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `ticket_id` INT UNSIGNED NOT NULL,
    `barcode` VARCHAR (120) NOT NULL,
    `number` SMALLINT UNSIGNED NOT NULL
);");
sql_query($db_connection, "ALTER TABLE `barcodes` ADD CONSTRAINT `barcodes_ticket_id_foreign_key` 
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;");


/*
 *  перед удалением при необходимости переносим данные из старой таблицы, затем удаляем
 * */
sql_query($db_connection, "ALTER TABLE `orders`
  DROP `ticket_adult_price`,
  DROP `ticket_adult_quantity`,
  DROP `ticket_kid_price`,
  DROP `ticket_kid_quantity`,
  DROP `barcode`;");


/*
 * поскольку типы билетов могут добавляться, то лучше использовать отдельную таблицу, где будет указано
 * количество забронированных билетов для каждого типа (новый тип можно будет легко добавить в поле ticket_type)
 *
 * barcode должен быть отдельный у каждого билета, поэтому тоже создаем отдельную таблицу с привязкой
 * к типу билета и с порядковым номером
 *
 * в конечном итоге получилось 3 таблицы:
 *
 orders - заказы
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `event_date` datetime NOT NULL,
  `equal_price` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL

 tickets - билеты по типам
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `ticket_type` enum('adult','kid','discount','group') NOT NULL,
  `ticket_price` int(10) UNSIGNED NOT NULL,
  `ticket_quantity` smallint(5) UNSIGNED NOT NULL,

 barcodes - коды по каждому билету
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `barcode` varchar(120) NOT NULL,
  `number` smallint(5) UNSIGNED NOT NULL
 */





