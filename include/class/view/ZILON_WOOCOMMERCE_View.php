<?php

class ZILON_WOOCOMMERCE_View
{

    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'redirect_styles']);
        add_action('wp_enqueue_scripts', [$this, 'redirect_scripts']);
    }
    public function redirect_scripts()
    {
        $copy_handle_script = "

            function onCopyText(inputText, tooltipText) {
                var input = document.createElement('input');
                input.type = 'text';
                input.setAttribute('value', inputText);
                document.body.appendChild(input);
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(input);

                var tooltip = document.getElementById(tooltipText);
                tooltip.innerHTML = 'Copied';
            }

            function onHandleTooltip(tooltipText) {
                var tooltip = document.getElementById(tooltipText);
                tooltip.innerHTML = 'Copy';
            }

        ";
        
        $hash_handle_script = "
            
            function shorter(hash) {
                var first = hash.slice(0,10);
                var end   = hash.slice(-10);
                return  first+\"...\"+end;
            }
            var hash_text = document.getElementById(\"hash\").innerHTML;
            document.getElementById(\"hash\").innerHTML = shorter(hash_text); 

        ";
        
        $time_handle_script = "

            var time = document.getElementById(\"time\").innerHTML;
            var show_time = moment.parseZone(time).local().format('YYYY-MM-DD, h:mm:ss a'); 
            document.getElementById(\"time\").innerHTML = show_time; 

        ";


        $inline_script = $hash_handle_script ."\n". $time_handle_script ."\n". $copy_handle_script;
        wp_register_script('ZILON_WOOCOMMERCE_moment_js', ZILON_WOOCOMMERCE_URL.'assets/moment-with-locales.min.js', array(), '2.24.0');
        wp_add_inline_script('ZILON_WOOCOMMERCE_moment_js', $inline_script);
    }



    public function redirect_styles()
    {
        wp_register_style('ZILON_WOOCOMMERCE_redirect_style', ZILON_WOOCOMMERCE_URL.'assets/style.css');
        wp_register_style('ZILON_WOOCOMMERCE_roboto_google_font', 'https://fonts.googleapis.com/css?family=Roboto&display=swap');
    }


    public function redirect_ok_page($_payment_data)
    {
        if (!isset($_payment_data) || !is_array($_payment_data)) {
            return false;
        }
        if (!isset($_payment_data["p_id"]) || !isset($_payment_data["confirmedAt"]) || !isset($_payment_data["id"]) || !isset($_payment_data["hash"]) || !isset($_payment_data["payerName"]) || !isset($_payment_data["payerEmail"])) {
            return false;
        }
        wp_enqueue_style( 'ZILON_WOOCOMMERCE_redirect_style' );
        wp_enqueue_style( 'ZILON_WOOCOMMERCE_roboto_google_font' );
        $time_string = str_replace("T", " ", $_payment_data['confirmedAt']);
        $time_string = str_replace("Z", "", $time_string);
        echo "
        <div id=\"Zilon\" class=\"p-relative h-100\">
            <div class=\"child-v-h-center card-container\">
                <div class=\"card\">
                    <div class=\"container column w-100\">
                        <div>
                            <img src=\"".ZILON_WOOCOMMERCE_IMG_URL.'verified.png'."\" class=\"mx-auto d-block verified-image\" width=\"60px\" height=\"60px\" alt=\"\">
                        </div>
                        <h1 class=\"title my-0\">".__("Successful transaction", "zilon-woocommerce")."</h1>
                        <p class=\"text my-0\">".__("You are transaction was successful", "zilon-woocommerce")."</p>
                        <ul class=\"list\">
                            <li>
                                <div class=\"container row\">
                                    <span class=\"float-left list-label item-1\">".__("Payment ID", "zilon-woocommerce")."</span>
                                    <span class=\"float-right text-right list-text item-2\">".esc_html($_payment_data['id'])."</span>
                                </div>
                            </li>
                            <li>
                                <div class=\"container row\">
                                    <span class=\"float-left list-label item-1\">".__("Tx hash", "zilon-woocommerce")."</span>
                                    <div class=\"float-right text-right list-text item-2\">
                                        <span id=\"hash\" style=\"font-size:small\">".esc_html($_payment_data['hash'])."</span>
                                        <div class=\"tooltip\">
                                            <button type=\"button\" onclick=\"onCopyText('".esc_html($_payment_data['hash'])."', 'txTooltip')\" onmouseout=\"onHandleTooltip('txTooltip')\" class=\"copy-btn\">
                                                <span class=\"tooltiptext\" id=\"txTooltip\">".__("Copy", "zilon-woocommerce")."</span>
                                                <img src=\"".ZILON_WOOCOMMERCE_IMG_URL.'copy.png'."\" width=\"15px\" height=\"15px\" alt=\"\">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class=\"container row\">
                                    <span class=\"float-left list-label item-1\">".__("Confirm At", "zilon-woocommerce")."</span>
                                    <span id=\"time\" class=\"float-right text-right list-text item-2\">".esc_html($time_string)."</span>
                                </div>
                            </li>
                            <li>
                                <div class=\"container row\">
                                    <span class=\"float-left list-label item-1\">".__("Payer name", "zilon-woocommerce")."</span>
                                    <span class=\"float-right text-right list-text item-2\">".esc_html($_payment_data['payerName'])."</span>
                                </div>
                            </li>
                            <li>
                                <div class=\"container row\">
                                    <span class=\"float-left list-label item-1\">".__("Payer email", "zilon-woocommerce")."</span>
                                    <span class=\"float-right text-right list-text item-2\">".esc_html($_payment_data['payerEmail'])."</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    ";

        wp_enqueue_script('ZILON_WOOCOMMERCE_moment_js');
    }


    public function redirect_fail_page($_payment_data)
    {
        if (!isset($_payment_data) || !is_array($_payment_data)) {
            return false;
        }
        if (!isset($_payment_data["p_id"])) {
            return false;
        }
        wp_enqueue_style( 'ZILON_WOOCOMMERCE_redirect_style' );
        wp_enqueue_style( 'ZILON_WOOCOMMERCE_roboto_google_font' );

        echo "

        <div id=\"Zilon\"class=\"p-relative h-100\">
            <div class=\"child-v-h-center card-container\">
                <div class=\"card\" style=\"height: 100%\">
                    <div class=\"container column w-100\">
                        <div>
                            <img src=\"".ZILON_WOOCOMMERCE_IMG_URL.'exclamation.png'."\"
                            class=\"mx-auto d-block verified-image\"
                             width=\"60px\"
                            height=\"60px\"
                            alt=\"\">
                        </div>
                        <h1 class=\"title title-fail my-0\" style=\"color : #a82323 !important\">".__("Failed transaction", "zilon-woocommerce")."</h1>
                        <p class=\"text my-0\">".__("You are transaction was failed", "zilon-woocommerce")."</p>
                        <div class=\"container row\" style=\"margin-top: 44px;padding-bottom: 35px\">
                            <span class=\"float-left list-label item-1\">".__("Payment ID", "zilon-woocommerce")."</span>
                            <span class=\"float-right text-right list-text item-2\">".esc_html($_payment_data['p_id'])."</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }
}
