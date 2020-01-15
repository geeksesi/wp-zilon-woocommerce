<?php

include ZILON_WOOCOMMERCE_INC_DIR."class/view/ZILON_WOOCOMMERCE_View.php";
include ZILON_WOOCOMMERCE_INC_DIR."class/controller/ZILON_WOOCOMMERCE_Api.php";

class ZILON_WOOCOMMERCE_Controller
{
    private $view;
    private $api;
    public function __construct()
    {
        $this->view  = new ZILON_WOOCOMMERCE_View();
        $this->api   = new ZILON_WOOCOMMERCE_Api();
        add_filter('woocommerce_payment_gateways', [$this, "zilon_gateway_init"]);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('rest_api_init', [$this, "make_back_url"]);
        add_shortcode('Zilon_io', [$this, "manage_redirect_url"]);
    }


    public function zilon_gateway_init($methods)
    {
        $methods[] = 'WC_Zilon';
        return $methods;
    }

    public function init()
    {
        include ZILON_WOOCOMMERCE_INC_DIR."class/controller/WC_Zilon.php";
    }


    public function make_back_url()
    {
        register_rest_route('zilon/v1', 'back_url', [
            "methods"   => "POST",
            "callback"  => [$this, "manage_back_url"]
        ]);
    }


    private function make_redirect_url($_url, $_payment_id, $_true)
    {
        if (!isset($_url) || !isset($_payment_id) || !isset($_true)) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!is_string($_url) || !is_string($_payment_id) || !is_string($_true)) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!strpos($_url, "?")) {
            $url = $_url."?payment_id=".$_payment_id."&ok=".$_true;
            return $url;
        }
        $url = $_url."&payment_id=".$_payment_id."&ok=".$_true;
        return $url;
    }


    public function manage_back_url()
    {
        if (!isset($_GET["o_id"]) || !isset($_GET["r_url"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!is_string($_GET["o_id"]) || !is_string($_GET["r_url"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["paymentId"]) || !isset($data["status"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if ($data["status"] != "confirmed") {
            $output["ok"] = true;
            $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $data["paymentId"], "false");
            return $output;
        }

        $check_payment = $this->api->check_payment($data["paymentId"]);
        if (!$check_payment || $check_payment != "confirmed") {
            $output["ok"] = true;
            $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $data["paymentId"], "false");
            return $output;
        }

        $customer_order = new WC_Order($_GET["o_id"]);
        $customer_order->payment_complete();

        $output["ok"] = true;
        $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $data["paymentId"], "true");
        return json_encode($output);
    }


    public function manage_redirect_url()
    {
        if (!isset($_GET["payment_id"]) || !isset($_GET["ok"])) {
            return null;
        }

        if (!is_string($_GET["payment_id"]) || !is_bool($_GET["ok"])) {
            return false;
        }

        $payment_data = $this->api->payment_info($_GET["payment_id"]);
        $payment_data["data"]["p_id"] = $_GET["payment_id"];
        if (!$payment_data) {
            return false;
        }
        if ($_GET["ok"]) {
            WC()->cart->empty_cart();
        }
        if ($payment_data["data"]["status"] == "confirmed") {
            $this->view->redirect_ok_page($payment_data["data"]);
        } else {
            $this->view->redirect_fail_page($payment_data["data"]);
        }
    }
}
