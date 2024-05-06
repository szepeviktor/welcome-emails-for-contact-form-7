<?php

namespace Automation;

defined('ABSPATH') || exit;

$vendor_url = plugins_url('../vendor', __FILE__);

?>
<div id="atm-menu">
    <ul>
        <li id="cmp-logo"><a href="?page=automation_dashboard">Welcome Emails</a></li>
        <li><a href="?page=automation_types"><?php esc_html_e('When...', 'automation')?></a></li>
        <li><a href="?page=automation_settings"><?php esc_html_e('Settings', 'automation')?></a></li>
        <li><a href="?page=automation_events"><?php esc_html_e('History', 'automation')?></a></li>
    </ul>

    <div id="atm-contact">
        Write me at <strong style="color: var(--atm-violet-light);">stefano@satollo.net</strong> for requests, ideas, bugs.
    </div>
</div>



<!-- usare https://sentry.io/answers/how-to-get-values-from-urls-in-javascript/ per evidenziare la voce di menu -->
<script>
    jQuery(function ($) {
        $('#atm-menu a').each(function () {
            if (location.href.indexOf(this.href) >= 0) {
                $(this).addClass('atm-menu-active')
            }
        });
    });

    const Automation = {
        error_message: '<?php echo esc_js(__('An error occurred see the logs on wp-content/logs/automation', 'automation'))?>',
        nonce: '<?php echo esc_js(wp_create_nonce('automation')) ?>',
        toast: function (message) {
            Toastify({
                text: message?message:'<?php echo esc_js(__('Saved', 'automation'))?>',
                duration: 2000,
                destination: "https://github.com/apvarun/toastify-js",
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "linear-gradient(to right, #f79a25, #ea7826)",
                },
                onClick: function () {} // Callback after click
            }).showToast();
        }
    }
</script>