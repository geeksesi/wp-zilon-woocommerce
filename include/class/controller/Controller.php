<?php

include ZILONIO_INC_DIR."class/view/View.php";
include ZILONIO_INC_DIR."class/controller/Api.php";

class Controller
{
    private $view;
    private $api;
    public function __construct()
    {
        $this->view  = new View();
        $this->api   = new Api();
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
        include ZILONIO_INC_DIR."class/controller/WC_Zilon.php";
    }


    public function make_back_url()
    {
        register_rest_route('zilon/v1', 'back_url', [
            "methods"   => "POST",
            "callback"  => [$this, "manage_back_url"]
        ]);
    }


    private function make_redirect_url(string $_url, string $_payment_id, bool $_true)
    {
        if (!strpos($_url, "?")) {
            $url = $_url."?payment_id=".$_payment_id."&ok=".$_true;
            return $url;
        }
        $url = $_url."&payment_id=".$_payment_id."&ok=".$_true;
        return $url;
    }


    public function manage_back_url()
    {
        global $woocommerce;
        if (!isset($_GET["o_id"]) || !isset($_GET["r_url"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!isset($_POST["paymentId"]) || !isset($_POST["status"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if ($_POST["status"] != "confirmed") {
            $output["ok"] = true;
            $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $_POST["paymentId"], false);
            return $output;
        }

        $check_payment = $this->api->confirmed($_POST["paymentId"]);
        if (!$check_payment || $check_payment != "confirmed") {
            $output["ok"] = true;
            $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $_POST["paymentId"], false);
            return $output;
        }

        $customer_order = new WC_Order($_GET["o_id"]);
        $customer_order->payment_complete();

        $output["ok"] = true;
        $output["redirectUrl"] = $this->make_redirect_url($_GET["r_url"], $_POST["paymentId"], true);
        return $output;
    }


    public function manage_redirect_url()
    {
        if (!isset($_GET["payment_id"]) || !isset($_GET["ok"])) {
            return null;
        }

        $payment_data = $this->api->payment_info($_GET["payment_id"]);
        $payment_date["p_id"] = $_GET["payment_id"];
        if (!$payment_data) {
            return false;
        }
        if ($_GET["ok"]) {
            WC()->cart->empty_cart();
        }
        if ($payment_data["status"] == "confirmed") {
            $this->view->redirect_ok_page($payment_data);
        } else {
            $this->view->redirect_fail_page($payment_data);
        }
    }
}
