<?php

class Api {

    public function __construct(){
    }

    public function create_payment(string $_api_key, string $_amount, string $_currency, string $_back_url, string $_name = null, string $_email = null){
        $url = "https://api.zilon.io/v1/payments";
        $data = [
            "email"     => $_email,
            "name"      => $_name,
            "backUrl"   => $_back_url,
            "currency"  => $_currency,
            "amount"    => $_amount,
            "apiKey"   => $_api_key,
        ];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        curl_close ($ch);
        return (isset($array["link"])) ? (string) $array["link"] : false;
    }

    public function check_payment(string $_payment_id){
        $url   = "https://api.zilon.io/v1/payments/".$_payment_id;
        $json  = file_get_contents($url);
        $array = json_decode($json, true);
        if(isset($array["status"])){
            return (string)$array["status"];
        }
        return false;

    }
}
