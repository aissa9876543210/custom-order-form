<?php
class Custom_Order_Form_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_save_form_settings', array($this, 'save_form_settings'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'إعدادات فورم الطلب',
            'فورم الطلب',
            'manage_options',
            'custom-order-form',
            array($this, 'display_admin_page'),
            'dashicons-format-aside',
            30
        );
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_custom-order-form' !== $hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('custom-order-form-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version);
        wp_enqueue_script('custom-order-form-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
        
        wp_localize_script('custom-order-form-admin', 'customOrderFormAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custom_order_form_admin_nonce')
        ));
    }

    public function display_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $field_labels = get_option('custom_order_form_field_labels', array(
            'fullName' => 'الاسم بالكامل',
            'phone' => 'رقم الهاتف',
            'address' => 'العنوان بالتفصيل'
        ));

        $shipping_prices = get_option('custom_order_form_shipping_prices', array());
        $design_settings = get_option('custom_order_form_design', array(
            'primaryColor' => '#2563eb',
            'buttonColor' => '#2563eb',
            'fontFamily' => 'IBM Plex Sans Arabic'
        ));
        ?>
        <div class="wrap custom-order-form-settings">
            <h1>إعدادات فورم الطلب</h1>
            
            <form id="custom-order-form-settings" method="post">
                <?php wp_nonce_field('custom_order_form_settings', 'custom_order_form_nonce'); ?>
                
                <div class="settings-tabs">
                    <button type="button" class="settings-tab active" data-tab="fields">الحقول</button>
                    <button type="button" class="settings-tab" data-tab="shipping">أسعار التوصيل</button>
                    <button type="button" class="settings-tab" data-tab="design">التصميم</button>
                </div>

                <div class="settings-panel active" id="fields-panel">
                    <h2>تسميات الحقول</h2>
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="field_labels[fullName]" 
                               value="<?php echo esc_attr($field_labels['fullName']); ?>" class="regular-text">
                    </div>
                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="text" name="field_labels[phone]" 
                               value="<?php echo esc_attr($field_labels['phone']); ?>" class="regular-text">
                    </div>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="field_labels[address]" 
                               value="<?php echo esc_attr($field_labels['address']); ?>" class="regular-text">
                    </div>
                </div>

                <div class="settings-panel" id="shipping-panel">
                    <h2>أسعار التوصيل</h2>
                    <?php
                    $states = array(
                        "01: ولاية أدرار" => array('home' => 1300, 'office' => 1040),
                        "02: ولاية الشلف" => array('home' => 600, 'office' => 480),
                        "03: ولاية الأغواط" => array('home' => 700, 'office' => 560)
                    );
                    
                    foreach ($states as $state => $default_prices) {
                        $prices = isset($shipping_prices[$state]) ? $shipping_prices[$state] : $default_prices;
                        ?>
                        <div class="shipping-price-group">
                            <h3><?php echo esc_html($state); ?></h3>
                            <div class="form-group">
                                <label>سعر التوصيل للمنزل</label>
                                <input type="number" name="shipping_prices[<?php echo esc_attr($state); ?>][home]" 
                                       value="<?php echo esc_attr($prices['home']); ?>">
                            </div>
                            <div class="form-group">
                                <label>سعر التوصيل للمكتب</label>
                                <input type="number" name="shipping_prices[<?php echo esc_attr($state); ?>][office]" 
                                       value="<?php echo esc_attr($prices['office']); ?>">
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="settings-panel" id="design-panel">
                    <h2>إعدادات التصميم</h2>
                    <div class="form-group">
                        <label>اللون الرئيسي</label>
                        <input type="color" name="design[primaryColor]" 
                               value="<?php echo esc_attr($design_settings['primaryColor']); ?>" class="color-picker">
                    </div>
                    <div class="form-group">
                        <label>لون الأزرار</label>
                        <input type="color" name="design[buttonColor]" 
                               value="<?php echo esc_attr($design_settings['buttonColor']); ?>" class="color-picker">
                    </div>
                    <div class="form-group">
                        <label>نوع الخط</label>
                        <select name="design[fontFamily]">
                            <option value="IBM Plex Sans Arabic" <?php selected($design_settings['fontFamily'], 'IBM Plex Sans Arabic'); ?>>IBM Plex Sans Arabic</option>
                            <option value="Tajawal" <?php selected($design_settings['fontFamily'], 'Tajawal'); ?>>Tajawal</option>
                            <option value="Cairo" <?php selected($design_settings['fontFamily'], 'Cairo'); ?>>Cairo</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="button button-primary">حفظ الإعدادات</button>
            </form>
        </div>
        <?php
    }

    public function save_form_settings() {
        check_ajax_referer('custom_order_form_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('غير مصرح لك بتنفيذ هذا الإجراء');
        }

        $field_labels = isset($_POST['field_labels']) ? $_POST['field_labels'] : array();
        $shipping_prices = isset($_POST['shipping_prices']) ? $_POST['shipping_prices'] : array();
        $design_settings = isset($_POST['design']) ? $_POST['design'] : array();

        update_option('custom_order_form_field_labels', $field_labels);
        update_option('custom_order_form_shipping_prices', $shipping_prices);
        update_option('custom_order_form_design', $design_settings);

        wp_send_json_success('تم حفظ الإعدادات بنجاح');
    }
}