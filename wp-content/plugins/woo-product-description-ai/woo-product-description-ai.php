<?php
/**
 * Plugin Name: WooCommerce AI Copilot
 * Description: Independent AI-powered product descriptions for WooCommerce.
 * Version: 1.1
 * Author: Your Name
 * License: GPL2
 */

if ( !defined( 'ABSPATH' ) ) exit;

// 1. REGISTER THE SETTINGS MENU
add_action( 'admin_menu', 'waic_register_menu' );
function waic_register_menu() {
    add_submenu_page(
        'woocommerce',
        'AI Copilot Settings',
        'AI Copilot',
        'manage_options',
        'waic-settings',
        'waic_settings_html'
    );
}

function waic_settings_html() {
    $key = get_option('waic_api_key', '');
    ?>
    <div class="wrap">
        <h1>WooCommerce AI Copilot Settings</h1>
        <div class="card" style="max-width: 600px; padding: 20px;">
            <label><strong>Gemini API Key:</strong></label><br>
            <input type="password" id="waic_api_key" value="<?php echo esc_attr($key); ?>" style="width:100%; margin: 10px 0;">
            <button id="waic_save_key" class="button button-primary">Save API Key</button>
        </div>
    </div>
    <script>
    jQuery('#waic_save_key').on('click', function() {
        var key = jQuery('#waic_api_key').val();
        jQuery.post(ajaxurl, {
            action: 'waic_save_key',
            api_key: key
        }, function() { alert('Settings Saved Successfully!'); });
    });
    </script>
    <?php
}

// 2. AJAX HANDLER FOR SAVING SETTINGS
add_action('wp_ajax_waic_save_key', function() {
    if (!current_user_can('manage_options')) wp_send_json_error();
    update_option('waic_api_key', sanitize_text_field($_POST['api_key']));
    wp_send_json_success();
});

// 3. INJECT UI INTO PRODUCT EDIT SIDEBAR
add_action( 'add_meta_boxes_product', function() {
    add_meta_box('waic_box', 'AI Product Assistant', 'waic_render_box', 'product', 'side', 'high');
});

function waic_render_box() {
    ?>
    <div id="waic-app">
        <p><strong>Focus:</strong></p>
        <select id="waic_goal" style="width:100%;">
            <option value="persuasive">Sales & Persuasion</option>
            <option value="technical">Technical Specs</option>
            <option value="seo">SEO Optimization</option>
        </select>

        <p><strong>Target Editor:</strong></p>
        <select id="waic_target" style="width:100%;">
            <option value="content">Main Description</option>
            <option value="excerpt">Short Description</option>
        </select>

        <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
        
        <button type="button" id="waic_run" class="button button-primary" style="width:100%;">
            Generate Copy
        </button>
        
        <p id="waic_msg" style="display:none; text-align:center; margin-top: 10px;">
            Writing...
        </p>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#waic_run').on('click', function(e) {
            e.preventDefault();
            var productName = $('#title').val();
            
            if(!productName) {
                alert('Please enter a Product Name first.');
                return;
            }

            var btn = $(this).prop('disabled', true);
            $('#waic_msg').show();

            $.post(ajaxurl, {
                action: 'waic_generate',
                product_name: productName,
                goal: $('#waic_goal').val()
            }, function(res) {
                if(res.success) {
                    var targetId = $('#waic_target').val();
                    if (typeof tinyMCE !== 'undefined' && tinyMCE.get(targetId)) {
                        tinyMCE.get(targetId).insertContent(res.data);
                    } else {
                        $('#' + targetId).val($('#' + targetId).val() + res.data);
                    }
                } else {
                    alert('Error: ' + res.data);
                }
                btn.prop('disabled', false);
                $('#waic_msg').hide();
            });
        });
    });
    </script>
    <?php
}

// 4. THE AI LOGIC
add_action('wp_ajax_waic_generate', function() {
    $name = isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '';
    $goal = isset($_POST['goal']) ? sanitize_text_field($_POST['goal']) : 'general';
    $key  = get_option('waic_api_key');

    if (!$key) {
        wp_send_json_error('API Key is missing.');
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $key;

    $prompt = "Write a product description for " . $name . " focusing on " . $goal . ". Use HTML. No markdown.";

    $payload = array(
        'contents' => array(
            array(
                'parts' => array(
                    array('text' => $prompt)
                )
            )
        )
    );

    $response = wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode($payload),
        'timeout' => 30
    ));

    if (is_wp_error($response)) {
        wp_send_json_error($response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($data['error'])) {
        wp_send_json_error($data['error']['message']);
    }

    $ai_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
    wp_send_json_success(wpautop($ai_text));
});