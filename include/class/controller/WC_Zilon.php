<?php

class WC_Zilon extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = "zilon_payment_gateway";
        $this->method_title = __("zilon payment", 'Zilon_io');
        $this->method_description = __("Zilon.io for wordpress", 'Zilon_io');
        $this->title = __("Zilon.io WC", 'Zilon_io');
        $this->description = __("Successfully payment through zilon.io.", 'Zilon_io');
        $this->icon = ZILONIO_IMG_URL."logo.png";
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
                'title'    => __('Enable / Disable', 'Zilon_io'),
                'label'    => __('Enable this payment gateway', 'Zilon_io'),
                'type'    => 'checkbox',
                'default'  => 'no',
            ),
            'api_key' => array(
                'title'    => __('apiKey of zilon.io', 'Zilon_io'),
                'type'    => 'text',
                'desc_tip'  => __('This is the apiKey provided by zilon.io when you signed up for an account.', 'Zilon_io'),
            ),
            'redirect_url' => array(
                'title'    => __('address of where u want to user get there after finished pay page', 'Zilon_io'),
                'type'    => 'text',
                'desc_tip'  => __('you must put this shortcut on a page and paste the url here : [Zilon_io]', 'Zilon_io'),
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
        $api = new Api();
        $url = $api->create_payment(
            (string) $this->api_key,
            (string) $customer_order->get_total(),
            (string) $customer_order->get_currency(),
            (string) $this->make_back_url($_order_id),
            (string) $customer_order->get_billing_first_name(),
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

    public function validate_fields()
    {
        return true;
    }

    public function get_api_key()
    {
        return $this->api_key;
    }
}
