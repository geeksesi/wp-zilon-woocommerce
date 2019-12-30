<?php

class View
{
    public function redirect_ok_page($_payment_data)
    {
        error_log("hello");
        echo "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".ZILONIO_URL.'assets/style.css'."\">
        <div id=\"Zilon\" class=\"p-relative h-100\">
            <div class=\"child-v-h-center card-container\">
            <div class=\"card\">
            <div class=\"container column w-100\">
            <div>
            <img src=\"".ZILONIO_IMG_URL.'verified.png'."\"
            class=\"mx-auto d-block verified-image\"
                width=\"60px\"
                height=\"60px\"
                alt=\"\">
                </div>
                <h1 class=\"title my-0\">Successful transaction</h1>
                <p class=\"text my-0\">You are transaction was successful</p>
                <ul class=\"list\">
                <li>
                <div class=\"container row\">
                <span class=\"float-left list-label item-1\">Payment ID</span>
                <span class=\"float-right text-right list-text item-2\">".$_payment_data['p_id']."</span>
                </div>
                </li>
                <li>
                <div class=\"container row\">
                <span class=\"float-left list-label item-1\">Tx hash</span>
                <div class=\"float-right text-right list-text item-2\">
                <span style=\"font-size:small\">".$_payment_data['txnHash']."</span>
                <div class=\"tooltip\">
                <button type=\"button\"
                onclick=\"onCopyText('".$_payment_data['txnHash']."', 'txTooltip')\"
                onmouseout=\"onHandleTooltip('txTooltip')\"
                class=\"copy-btn\">
                    <span class=\"tooltiptext\" id=\"txTooltip\">Copy</span>
                    <img src=\"".ZILONIO_IMG_URL.'copy.png'."\" width=\"15px\" height=\"15px\" alt=\"\">
                    </button>
                    </div>
                    </div>
                    </div>
                    </li>
                    <li>
                    <div class=\"container row\">
                    <span class=\"float-left list-label item-1\">Confirm At</span>
                    <span class=\"float-right text-right list-text item-2\">".$_payment_data['confirmedAt']."</span>
                    </div>
                    </li>
                    <li>
                    <div class=\"container row\">
                    <span class=\"float-left list-label item-1\">Payer name</span>
                    <span class=\"float-right text-right list-text item-2\">".$_payment_data['payerName']."</span>
                    </div>
                    </li>
                    <li>
                    <div class=\"container row\">
                    <span class=\"float-left list-label item-1\">Payer email</span>
                    <span class=\"float-right text-right list-text item-2\">".$_payment_data['payerEmail']."</span>
                    </div>
                    </li>
                    </ul>
                    </div>
                    </div>
                    </div>
                    </div>

                    <script>
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
</script>
    ";
    }


    public function redirect_fail_page($_payment_data)
    {
    }
}
