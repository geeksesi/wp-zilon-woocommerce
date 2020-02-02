<?php

class WP_ZILON_WOOCOMMERCE_Api
{
    public function __construct()
    {
    }

    public function create_payment($_api_key, $_amount, $_currency, $_back_url, $_name = null, $_email = null)
    {
        if (!isset($_api_key) || !isset($_amount) || !isset($_currency) || !isset($_back_url)) {
            return false;
        }

        if (!is_string($_api_key) || !is_string($_amount) || !is_string($_currency) || !is_string($_back_url)) {
            return false;
        }
        $url = "https://api.zilon.io/v1/payments";
        $data = [
            "email"     => $_email,
            "name"      => $_name,
            "backUrl"   => $_back_url,
            "currency"  => $_currency,
            "amount"    => $_amount,
            "apiKey"   => $_api_key,
        ];

        $result = wp_remote_post($url, [
            'body'    => wp_json_encode($data),
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $array = json_decode($result["body"], true);

        return (isset($array["data"]["link"])) ? (string) esc_url_raw($array["data"]["link"]) : false;
    }


    public function check_payment( $_payment_id)
    {
        if (!isset($_payment_id)) {
            return false;
        }

        if (!is_string($_payment_id)) {
            return false;
        }
        $url   = "https://api.zilon.io/v1/payments/".$_payment_id;
        $json  = file_get_contents($url);
        $array = json_decode($json, true);
        if (is_array($array) && isset($array["data"]) && isset($array["data"]["status"])) {
            return (string) sanitize_text_field($array["data"]["status"]);
        }
        return false;
    }



    public function payment_info($_payment_id)
    {
        if (!isset($_payment_id)) {
            return false;
        }

        if (!is_string($_payment_id)) {
            return false;
        }
        $url   = "https://api.zilon.io/v1/payments/".$_payment_id;
        $json  = file_get_contents($url);
        $array = json_decode($json, true);
        return $array;
    }
}
