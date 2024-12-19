<?php
/**
 * Plugin Name: Custom Order Form for WooCommerce
 * Description: Adds a custom order form to WooCommerce product pages.
 * Version: 1.0
 * Author: pexlat
 * Text Domain: custom-order-form
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include admin class
require_once plugin_dir_path(__FILE__) . 'admin/class-custom-order-form-admin.php';

function custom_order_form_init() {
    $plugin = new Custom_Order_Form_Admin('custom-order-form', '1.0');
}
add_action('plugins_loaded', 'custom_order_form_init');

function custom_order_form_shortcode() {
    ob_start();
    custom_order_form_assets();
    add_custom_order_form();
    return ob_get_clean();
}
add_shortcode('custom-order-form', 'custom_order_form_shortcode');

function place_custom_order() {
   
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $product = wc_get_product($product_id);
        $price = wc_get_product($product_id)->get_price(); 
        $full_name = sanitize_text_field($_POST['full_name']);
        $phone = sanitize_text_field($_POST['phone']);
        $country = sanitize_text_field($_POST['country']);
        $city = sanitize_text_field($_POST['city']);
        $address = sanitize_text_field($_POST['address']);
        $delivery_type = sanitize_text_field($_POST['delivery_type']);
        $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

        // تجميع المتغيرات
    
        $variations = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') !== false) {
               $variations[$key] = sanitize_text_field($value);
            }
        }

        $order = wc_create_order();
        if (!$order) {
            wp_send_json_error('Failed to create order');
            return;
        }

        // إضافة المنتج مع المتغيرات إلى الطلب
        $order->add_product($product, $quantity, array('variation' => $variations));

        // تجميع العنوان الكامل مع نوع التوصيل
        $full_address = $address . ' (' . ($delivery_type == 'home' ? 'توصيل للمنزل' : 'توصيل للمكتب') . ')';

        // إعداد عناوين الدفع والشحن
        $order->set_address(array(
            'first_name' => $full_name,
            'phone' => $phone,
            'country' => $country,
            'state' => $city,
            'address_1' => $full_address,
        ), 'billing');

        $order->set_address(array(
            'first_name' => $full_name,
            'phone' => $phone,
            'country' => $country,
            'state' => $city,
            'address_1' => $full_address,
        ), 'shipping');

        // تحديث بيانات الطلب وحساب الإجمالي
        $order->update_meta_data('delivery_type', $delivery_type );
        $order->calculate_totals();
        $order->update_status('processing');
        $order->save();

        // إعادة توجيه المستخدم بعد نجاح الطلب
        wp_send_json_success(array(
            'redirect_url' => $order->get_checkout_order_received_url()
        ));
    } else {
        wp_send_json_error('Product ID not set');
    }
}

add_action('wp_ajax_place_custom_order', 'place_custom_order');
add_action('wp_ajax_nopriv_place_custom_order', 'place_custom_order');

function add_custom_order_form() {
    if (is_product()) {
        global $product;
        
        $has_variations = $product->is_type('variable');
        $variations = [];

        if ($has_variations) {
            $attributes = $product->get_variation_attributes();
            foreach ($attributes as $attribute => $values) {
                $variations[$attribute] = $values;
            }
        }

        echo '<div class="custom-order-form-container">';
        include 'order-form-template.php';
        echo '</div>';
    }
}

// تغيير موضع ظهور النموذج إلى مكان الوصف القصير
remove_action('woocommerce_before_single_product_summary', 'add_custom_order_form', 10);
add_action('woocommerce_single_product_summary', 'add_custom_order_form', 20);

function custom_order_form_assets() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
    wp_enqueue_style('custom-order-form-styles', plugin_dir_url(__FILE__) . 'styles.css');
    wp_enqueue_script('custom-order-form-scripts', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
    wp_enqueue_script('algeria-cities', plugin_dir_url(__FILE__) . 'algeria-cities.js', array(), null, true);

    wp_localize_script('custom-order-form-scripts', 'woocommerce_params', array(
        'product_id' => get_the_ID(),
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'custom_order_form_assets');
