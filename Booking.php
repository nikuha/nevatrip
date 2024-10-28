<?php

require_once('Api.php');

class Booking{

    private $db_connection;
    private $attempts_count = 5;

    /**
     * @param $db_connection
     */
    public function __construct($db_connection){
        $this->db_connection = $db_connection;
    }

    /**
     * @param $event_id
     * @param $event_date
     * @param $ticket_adult_price
     * @param $ticket_adult_quantity
     * @param $ticket_kid_price
     * @param $ticket_kid_quantity
     * @return array
     */
    public function create_booking($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity,
                                       $ticket_kid_price, $ticket_kid_quantity){

        if(!$event_id || (int) $event_id != $event_id){
            return $this->error_message('Параметр event_id должен быть целым числом');
        }

        if(DateTime::createFromFormat('Y-m-d G:i:s', $event_date) === false){
            return $this->error_message('Неверный формат event_date');
        }

        foreach (['ticket_adult_price', 'ticket_adult_quantity', 'ticket_kid_price', 'ticket_kid_quantity'] as $p){
            if(!is_int(${$p})){
                return $this->error_message('Параметр '.$p.' должен быть целым числом');
            }
        }

        $params = [
            'event_id'=>$event_id,
            'event_date'=>$event_date,
            'ticket_adult_price'=>$ticket_adult_price,
            'ticket_adult_quantity'=>$ticket_adult_quantity,
            'ticket_kid_price'=>$ticket_kid_price,
            'ticket_kid_quantity'=>$ticket_kid_quantity,
        ];

        $api = new Api;

        for($attempt=1;$attempt<=$this->attempts_count;$attempt++){
            mt_srand(date("Y-m-d H:i:s").join('', $params));
            $barcode = mt_rand(10000000, 99999999);

            $booking_result = $api->book(array_merge($params, ['barcode'=>$barcode]));

            if($booking_result) break;
        }

        if(!$booking_result){
            return $this->error_message('Ошибка бронирования');
        }
        if(!$api->approve(['barcode' => $barcode])){
            return $this->error_message('Ошибка подтверждения бронирования');
        }

        $fields_names = ''; $fields_values = '';
        foreach ($params as $k=>$v){
            $fields_names .= "`".$k."`, ";
            $fields_values .= "'".$v."', ";
        }

        $equal_price = $ticket_adult_price * $ticket_adult_quantity + $ticket_kid_price * $ticket_kid_quantity;

        $fields_names .= '`barcode`, `equal_price`, `created`';
        $fields_values .= "'".$barcode."', '".$equal_price."', NOW()";

        $sql_query = "INSERT INTO `orders` (".$fields_names.")  VALUES (".$fields_values.");";

        if ($this->db_connection->query($sql_query) !== true) {
            return $this->error_message('Ошибка запроса к базе данных: '.$this->db_connection->error);
        }

        return $this->success_message('Заявка успешно добавлена');
    }

    /**
     * @param $error
     * @return array
     */
    private function error_message($error){
        return ['error'=>$error];
    }

    /**
     * @param $message
     * @return array
     */
    private function success_message($message){
        return ['message'=>$message];
    }
}