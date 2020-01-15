<?php

class WC_Zilon extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = "zilon_payment_gateway";
        $this->method_title = __("zilon payment", 'zilon-woocommerce');
        $this->method_description = __("Zilon for wordpress", 'zilon-woocommerce');
        $this->title = __("Zilon", 'zilon-woocommerce');
        $this->description = __("Successfully payment through zilon.io.", 'zilon-woocommerce');
        $this->icon = ZILON_WOOCOMMERCE_IMG_URL."logo.png";
        $this->has_fields = true;
        $this->init_form_fields();
        $this->init_settings();

        // Turn these settings into variables we can use
        foreach ($this->settings as $setting_key => $value) {
            $this->$setting_key = $value;
        }


        // Save settings
        if (is_admin()) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        }
    }


    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'    => __('Enable / Disable', 'zilon-woocommerce'),
                'label'    => __('Enable this payment gateway', 'zilon-woocommerce'),
                'type'    => 'checkbox',
                'default'  => 'no',
            ),
            'api_key' => array(
                'title'    => __('apiKey of zilon.io', 'zilon-woocommerce'),
                'type'    => 'text',
                'desc_tip'  => __('This is the apiKey provided by zilon.io when you signed up for an account.', 'zilon-woocommerce'),
            ),
            'redirect_url' => array(
                'title'    => __('put [zilon-woocommerce] shortcode on a page and set here that page address', 'zilon-woocommerce'),
                'type'    => 'text',
                'desc_tip'  => __('it\'s your landing page when user\'s payment will done: [zilon-woocommerce]', 'zilon-woocommerce'),
            ),
        );
    }

    private function make_back_url($_order_id)
    {
        $site_url = get_site_url();
        $string =  $site_url."?rest_route=/zilon/v1/back_url&o_id=".$_order_id."&r_url=".$this->redirect_url;
        return $string;
    }



    public function process_payment($_order_id)
    {
        $customer_order = new WC_Order($_order_id);
        $api = new ZILON_WOOCOMMERCE_Api();
        $url = $api->create_payment(
            (string) $this->api_key,
            (string) $customer_order->get_total(),
            (string) $customer_order->get_currency(),
            (string) $this->make_back_url($_order_id),
            (string) $customer_order->get_billing_first_name() .' '.$customer_order->get_billing_last_name(),
            (string) $customer_order->get_billing_email()
        );

        if ($url === false) {
            wc_add_notice("problem", 'error');
        } else {
            return [
                'result'   => 'success',
                'redirect' => $url,
            ];
        }
    }
}
