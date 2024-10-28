<?php


class Api{

    private $service_url;

    public function __construct(){
        $env = parse_ini_file('.env');
        $this->service_url = $env['API_URL'];
    }

    /**
     * @param $data
     * @return bool
     */
    public function book($data){
        try {
            $result = $this->request('book', $data);
            return is_object($result) && !$result->error;
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    public function approve($data){
        try {
            $result = $this->request('approve', $data);
            return is_object($result) && !$result->error;
        } catch (\Exception $e){
            return false;
        }
    }

    /**
     * @param $api_method
     * @param $data
     * @return mixed
     */
    private function request($api_method, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->service_url.$api_method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output);

    }
}