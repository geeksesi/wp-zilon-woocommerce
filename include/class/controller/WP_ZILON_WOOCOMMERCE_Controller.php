<?php

include WP_ZILON_WOOCOMMERCE_INC_DIR."class/view/WP_ZILON_WOOCOMMERCE_View.php";
include WP_ZILON_WOOCOMMERCE_INC_DIR."class/controller/WP_ZILON_WOOCOMMERCE_Api.php";

class WP_ZILON_WOOCOMMERCE_Controller
{
    private $view;
    private $api;
    public function __construct()
    {
        $this->view  = new WP_ZILON_WOOCOMMERCE_View();
        $this->api   = new WP_ZILON_WOOCOMMERCE_Api();
        add_filter('woocommerce_payment_gateways', [$this, "zilon_gateway_init"]);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('rest_api_init', [$this, "make_back_url"]);
        add_shortcode('wp-zilon-woocommerce', [$this, "manage_redirect_url"]);
    }


    public function zilon_gateway_init($methods)
    {
        $methods[] = 'WC_Zilon';
        return $methods;
    }

    public function init()
    {
        include WP_ZILON_WOOCOMMERCE_INC_DIR."class/controller/WC_Zilon.php";
    }


    public function make_back_url()
    {
        register_rest_route('zilon/v1', 'back_url', [
            "methods"   => "POST",
            "callback"  => [$this, "manage_back_url"]
        ]);
    }


    private function make_redirect_url($_url, $_payment_id)
    {
        if (!isset($_url) || !isset($_payment_id)) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!is_string($_url) || !is_string($_payment_id)) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            return $output;
        }
        if (!strpos($_url, "?")) {
            $url = $_url."?payment_id=".$_payment_id."";
            return $url;
        }
        $url = $_url."&payment_id=".$_payment_id."";
        return $url;
    }


    /**
     * this function will manage a wordpress rest api route to answer Zilon.io backUrl
     * Method : POST (include a query string)
     * @return null :: but will echo json of user request.
     *
    **/
    public function manage_back_url()
    {
        if (!isset($_GET["o_id"]) || !isset($_GET["r_url"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            echo json_encode($output);
            return null;
        }
        if (!is_string($_GET["o_id"]) || !is_string($_GET["r_url"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            echo json_encode($output);
            return null;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["paymentId"]) || !isset($data["status"])) {
            $output["ok"] = false;
            $output["redirectUrl"] = null;
            echo json_encode($output);
            return null;
        }
        $output["ok"] = true;
        $output["redirectUrl"] = esc_url($this->make_redirect_url(esc_url_raw($_GET["r_url"]), $data["paymentId"]));
        echo json_encode($output);
        if ($data["status"] != "confirmed") {
            return null;
        }

        $check_payment = $this->api->check_payment(sanitize_text_field($data["paymentId"]));
        if (!$check_payment || $check_payment != "confirmed") {
            return null;
        }

        $customer_order = new WC_Order(sanitize_text_field($_GET["o_id"]));
        $customer_order->payment_complete();
        return null;
    }


    public function manage_redirect_url()
    {
        if (!isset($_GET["payment_id"])) {
            return null;
        }

        if (!is_string($_GET["payment_id"])) {
            return false;
        }

        $payment_data = $this->api->payment_info($_GET["payment_id"]);
        $payment_data["data"]["p_id"] = sanitize_text_field($_GET["payment_id"]);
        if (!$payment_data) {
            return false;
        }

        if ($payment_data["data"]["status"] == "confirmed") {
            WC()->cart->empty_cart();
        }
        if ($payment_data["data"]["status"] == "confirmed") {
            $this->view->redirect_ok_page($payment_data["data"]);
        } else {
            $this->view->redirect_fail_page($payment_data["data"]);
        }
    }
}
