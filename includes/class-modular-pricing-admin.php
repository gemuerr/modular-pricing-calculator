<?php

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Admin {
    private $option_name;

    public function __construct() {
        $this->option_name = Modular_Pricing_Settings::get_option_name();
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Modular Pricing',
            'Modular Pricing',
            'manage_options',
            'modular-pricing',
            array($this, 'admin_page'),
            'dashicons-money-alt',
            30
        );

        add_submenu_page(
            'modular-pricing',
            'Submitted Forms',
            'Submitted Forms',
            'manage_options',
            'pricing-configurations',
            array($this, 'configurations_page')
        );
    }

    public function admin_page() {
        $settings = Modular_Pricing_Settings::get_settings();
        ?>
        <div class="wrap modular-pricing-admin">
            <h1>Modular Pricing Configuration</h1>
            <form method="post" action="options.php">
                <?php settings_fields('modular_pricing_group'); ?>
                <table class="form-table">
                    <tr>
                        <th colspan="2"><h2>Subscription Model Names</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Model A Name</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[model_a_name]"
                                   value="<?php echo esc_attr($settings['model_a_name']); ?>"
                                   placeholder="Fix-Abo" style="width: 300px;" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Model B Name</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[model_b_name]"
                                   value="<?php echo esc_attr($settings['model_b_name']); ?>"
                                   placeholder="Flex-Abo" style="width: 300px;" />
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><h2>Subscription Model A Pricing (€ per day)</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Half Day Pricing</th>
                        <td>
                            <table class="pricing-table">
                                <tr>
                                    <td>1 day/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_half_1]" value="<?php echo esc_attr($settings['model_a_half_1']); ?>" style="width: 100px;" /></td>
                                    <td>2 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_half_2]" value="<?php echo esc_attr($settings['model_a_half_2']); ?>" style="width: 100px;" /></td>
                                    <td>3 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_half_3]" value="<?php echo esc_attr($settings['model_a_half_3']); ?>" style="width: 100px;" /></td>
                                </tr>
                                <tr>
                                    <td>4 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_half_4]" value="<?php echo esc_attr($settings['model_a_half_4']); ?>" style="width: 100px;" /></td>
                                    <td>5 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_half_5]" value="<?php echo esc_attr($settings['model_a_half_5']); ?>" style="width: 100px;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Full Day Pricing</th>
                        <td>
                            <table class="pricing-table">
                                <tr>
                                    <td>1 day/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_full_1]" value="<?php echo esc_attr($settings['model_a_full_1']); ?>" style="width: 100px;" /></td>
                                    <td>2 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_full_2]" value="<?php echo esc_attr($settings['model_a_full_2']); ?>" style="width: 100px;" /></td>
                                    <td>3 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_full_3]" value="<?php echo esc_attr($settings['model_a_full_3']); ?>" style="width: 100px;" /></td>
                                </tr>
                                <tr>
                                    <td>4 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_full_4]" value="<?php echo esc_attr($settings['model_a_full_4']); ?>" style="width: 100px;" /></td>
                                    <td>5 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_a_full_5]" value="<?php echo esc_attr($settings['model_a_full_5']); ?>" style="width: 100px;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><h2>Subscription Model B Pricing (€ per day)</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Half Day Pricing</th>
                        <td>
                            <table class="pricing-table">
                                <tr>
                                    <td>1 day/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_half_1]" value="<?php echo esc_attr($settings['model_b_half_1']); ?>" style="width: 100px;" /></td>
                                    <td>2 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_half_2]" value="<?php echo esc_attr($settings['model_b_half_2']); ?>" style="width: 100px;" /></td>
                                    <td>3 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_half_3]" value="<?php echo esc_attr($settings['model_b_half_3']); ?>" style="width: 100px;" /></td>
                                </tr>
                                <tr>
                                    <td>4 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_half_4]" value="<?php echo esc_attr($settings['model_b_half_4']); ?>" style="width: 100px;" /></td>
                                    <td>5 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_half_5]" value="<?php echo esc_attr($settings['model_b_half_5']); ?>" style="width: 100px;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Full Day Pricing</th>
                        <td>
                            <table class="pricing-table">
                                <tr>
                                    <td>1 day/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_full_1]" value="<?php echo esc_attr($settings['model_b_full_1']); ?>" style="width: 100px;" /></td>
                                    <td>2 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_full_2]" value="<?php echo esc_attr($settings['model_b_full_2']); ?>" style="width: 100px;" /></td>
                                    <td>3 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_full_3]" value="<?php echo esc_attr($settings['model_b_full_3']); ?>" style="width: 100px;" /></td>
                                </tr>
                                <tr>
                                    <td>4 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_full_4]" value="<?php echo esc_attr($settings['model_b_full_4']); ?>" style="width: 100px;" /></td>
                                    <td>5 days/week:</td>
                                    <td><input type="number" step="0.01" name="<?php echo $this->option_name; ?>[model_b_full_5]" value="<?php echo esc_attr($settings['model_b_full_5']); ?>" style="width: 100px;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><h2>Color Settings</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Primary Color</th>
                        <td>
                            <input type="color" name="<?php echo $this->option_name; ?>[primary_color]"
                                   value="<?php echo esc_attr($settings['primary_color']); ?>" />
                            <span style="margin-left: 10px;"><?php echo esc_html($settings['primary_color']); ?></span>
                            <p class="description">Used for selected buttons and submit button</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Primary Hover Color</th>
                        <td>
                            <input type="color" name="<?php echo $this->option_name; ?>[primary_hover_color]"
                                   value="<?php echo esc_attr($settings['primary_hover_color']); ?>" />
                            <span style="margin-left: 10px;"><?php echo esc_html($settings['primary_hover_color']); ?></span>
                            <p class="description">Darker shade for hover effects</p>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><h2>Display Settings</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Currency Symbol</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[currency]"
                                   value="<?php echo esc_attr($settings['currency']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Form Display Mode</th>
                        <td>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[form_display_mode]"
                                       value="accordion" <?php checked($settings['form_display_mode'], 'accordion'); ?> />
                                Accordion (collapsible with toggle button)
                            </label>
                            <br><br>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[form_display_mode]"
                                       value="always_open" <?php checked($settings['form_display_mode'], 'always_open'); ?> />
                                Always Open (no toggle button, form always visible)
                            </label>
                            <p class="description">Choose whether the form should be collapsible or always visible</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Form Step Mode</th>
                        <td>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[form_step_mode]"
                                       value="combined" <?php checked($settings['form_step_mode'], 'combined'); ?> />
                                Single step (inputs and user data together)
                            </label>
                            <br><br>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[form_step_mode]"
                                       value="separate" <?php checked($settings['form_step_mode'], 'separate'); ?> />
                                Two steps (Step A pricing ➝ Step B contact data)
                            </label>
                            <p class="description">Separate the calculator into Step A (plan selection) and Step B (contact form)</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Pricing Calculation</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Weeks Multiplier</th>
                        <td>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[weeks_multiplier]"
                                       value="4" <?php checked($settings['weeks_multiplier'], '4'); ?> />
                                4 weeks
                            </label>
                            <br><br>
                            <label>
                                <input type="radio" name="<?php echo $this->option_name; ?>[weeks_multiplier]"
                                       value="4.33" <?php checked($settings['weeks_multiplier'], '4.33'); ?> />
                                4.33 weeks (more accurate monthly calculation)
                            </label>
                            <p class="description">Choose the multiplier for calculating monthly prices. 4.33 is more accurate (52 weeks ÷ 12 months).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Round Prices Up</th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo $this->option_name; ?>[round_prices]"
                                       value="1" <?php checked($settings['round_prices'], 1); ?> />
                                Round monthly prices up to the nearest whole number
                            </label>
                            <p class="description">When enabled, monthly prices will be rounded up (e.g., €123.45 becomes €124.00).</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Consent Checkbox</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Require Consent Checkbox</th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo $this->option_name; ?>[require_consent_checkbox]"
                                       value="1" <?php checked($settings['require_consent_checkbox'], 1); ?> />
                                Require users to check a consent checkbox before submitting the form
                            </label>
                            <p class="description">When enabled, users must check a consent checkbox before the submit button becomes active.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Checkbox Label</th>
                        <td>
                            <textarea name="<?php echo $this->option_name; ?>[consent_checkbox_label]" rows="3" style="width: 100%; max-width: 600px;"><?php echo esc_textarea($settings['consent_checkbox_label']); ?></textarea>
                            <p class="description">Label text for the consent checkbox. HTML is allowed (e.g., links to terms and conditions).</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Email Notification</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Notification Email</th>
                        <td>
                            <input type="email" name="<?php echo $this->option_name; ?>[notification_email]"
                                   value="<?php echo esc_attr($settings['notification_email']); ?>"
                                   placeholder="info@michelmeute.de" style="width: 300px;" />
                            <p class="description">Email address to receive notifications when new configurations are submitted</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>reCAPTCHA v2 Settings</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Enable reCAPTCHA</th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo $this->option_name; ?>[recaptcha_enabled]"
                                       value="1" <?php checked($settings['recaptcha_enabled'], 1); ?> />
                                Enable reCAPTCHA v2 spam protection
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Site Key</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[recaptcha_site_key]"
                                   value="<?php echo esc_attr($settings['recaptcha_site_key']); ?>"
                                   style="width: 400px;" />
                            <p class="description">Get your keys from <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Secret Key</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_name; ?>[recaptcha_secret_key]"
                                   value="<?php echo esc_attr($settings['recaptcha_secret_key']); ?>"
                                   style="width: 400px;" />
                        </td>
                    </tr>
                </table>

                <style>
                    .modular-pricing-admin {
                        max-width: 1200px;
                    }
                    .modular-pricing-admin .form-table {
                        background: #fff;
                        border: 1px solid #c3c4c7;
                        border-radius: 8px;
                        box-shadow: 0 1px 1px rgba(0,0,0,.04);
                        margin-top: 20px;
                    }
                    .modular-pricing-admin .form-table th[colspan="2"] {
                        padding: 20px 20px 15px;
                        border-bottom: 2px solid #f0f0f1;
                        background: #f6f7f7;
                    }
                    .modular-pricing-admin .form-table th[colspan="2"] h2 {
                        margin: 0;
                        font-size: 18px;
                        font-weight: 600;
                        color: #1d2327;
                    }
                    .modular-pricing-admin .form-table th[scope="row"] {
                        padding: 15px 20px;
                        font-weight: 500;
                        color: #1d2327;
                        width: 200px;
                    }
                    .modular-pricing-admin .form-table td {
                        padding: 15px 20px;
                    }
                    .modular-pricing-admin .form-table tr:not(:last-child) {
                        border-bottom: 1px solid #f0f0f1;
                    }
                    .modular-pricing-admin input[type="text"],
                    .modular-pricing-admin input[type="email"],
                    .modular-pricing-admin input[type="number"] {
                        padding: 8px 12px;
                        border: 1px solid #8c8f94;
                        border-radius: 4px;
                        font-size: 14px;
                        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                    }
                    .modular-pricing-admin input[type="text"]:focus,
                    .modular-pricing-admin input[type="email"]:focus,
                    .modular-pricing-admin input[type="number"]:focus {
                        border-color: #2271b1;
                        box-shadow: 0 0 0 1px #2271b1;
                        outline: 2px solid transparent;
                    }
                    .modular-pricing-admin input[type="color"] {
                        width: 60px;
                        height: 40px;
                        border: 1px solid #8c8f94;
                        border-radius: 4px;
                        cursor: pointer;
                        padding: 2px;
                    }
                    .modular-pricing-admin .pricing-table {
                        border-collapse: separate;
                        border-spacing: 8px;
                        width: 100%;
                        max-width: 600px;
                    }
                    .modular-pricing-admin .pricing-table td {
                        padding: 8px 12px;
                        vertical-align: middle;
                    }
                    .modular-pricing-admin .pricing-table td:first-child {
                        color: #646970;
                        font-size: 13px;
                        white-space: nowrap;
                        padding-right: 8px;
                    }
                    .modular-pricing-admin .pricing-table input[type="number"] {
                        width: 100%;
                        max-width: 120px;
                    }
                    .modular-pricing-admin label {
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        margin-bottom: 8px;
                        cursor: pointer;
                    }
                    .modular-pricing-admin input[type="radio"],
                    .modular-pricing-admin input[type="checkbox"] {
                        margin: 0;
                        cursor: pointer;
                    }
                    .modular-pricing-admin .description {
                        color: #646970;
                        font-size: 13px;
                        margin-top: 8px;
                        line-height: 1.5;
                    }
                    .modular-pricing-admin .description a {
                        color: #2271b1;
                        text-decoration: none;
                    }
                    .modular-pricing-admin .description a:hover {
                        text-decoration: underline;
                    }
                    .modular-pricing-admin .help-section {
                        margin-top: 30px;
                        padding: 24px;
                        background: #f6f7f7;
                        border: 1px solid #c3c4c7;
                        border-left: 4px solid #2271b1;
                        border-radius: 4px;
                    }
                    .modular-pricing-admin .help-section h3 {
                        margin-top: 0;
                        margin-bottom: 16px;
                        font-size: 16px;
                        font-weight: 600;
                        color: #1d2327;
                    }
                    .modular-pricing-admin .help-section h4 {
                        margin-top: 20px;
                        margin-bottom: 10px;
                        font-size: 14px;
                        font-weight: 600;
                        color: #1d2327;
                    }
                    .modular-pricing-admin .help-section code {
                        background: #fff;
                        padding: 12px 16px;
                        display: block;
                        margin-top: 10px;
                        border: 1px solid #c3c4c7;
                        border-radius: 4px;
                        font-family: 'Courier New', monospace;
                        font-size: 13px;
                        color: #1d2327;
                    }
                    .modular-pricing-admin .help-section ol {
                        margin-left: 20px;
                        line-height: 1.8;
                    }
                    .modular-pricing-admin .help-section ol li {
                        margin-bottom: 8px;
                    }
                    .modular-pricing-admin .help-section a {
                        color: #2271b1;
                        text-decoration: none;
                    }
                    .modular-pricing-admin .help-section a:hover {
                        text-decoration: underline;
                    }
                    .modular-pricing-admin .submit {
                        margin-top: 20px;
                    }
                    .modular-pricing-admin .submit .button-primary {
                        padding: 10px 20px;
                        font-size: 14px;
                        font-weight: 500;
                        border-radius: 4px;
                        box-shadow: 0 1px 0 #2271b1;
                    }
                </style>

                <?php submit_button(); ?>
            </form>

            <div class="help-section">
                <h3>How to Use</h3>
                <p>Add the pricing calculator to any page or post using this shortcode:</p>
                <code>[pricing_calculator]</code>

                <h4>Variable Pricing</h4>
                <p>The pricing is variable based on the number of days selected. Configure different prices per day for 1-5 days per week (Model B is limited to 4 days maximum). The final monthly price is calculated as: (price per day × number of days per week × weeks multiplier). You can configure the weeks multiplier (4 or 4.33) and enable price rounding in the Pricing Calculation section above.</p>

                <h4>Setting up reCAPTCHA v2</h4>
                <ol>
                    <li>Go to <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a></li>
                    <li>Register a new site with reCAPTCHA v2 (checkbox type)</li>
                    <li>Add your domain(s)</li>
                    <li>Copy the Site Key and Secret Key to the fields above</li>
                    <li>Save changes</li>
                </ol>
            </div>
        </div>
        <?php
    }

    public function configurations_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';

        // Get sort parameters
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        // Validate orderby column
        $allowed_columns = array('id', 'customer_name', 'customer_email', 'customer_phone', 'dog_name', 
                                 'subscription_model', 'duration', 'monthly_price', 'status', 'created_at');
        if (!in_array($orderby, $allowed_columns, true)) {
            $orderby = 'created_at';
        }
        
        // Sanitize orderby and order for SQL
        $orderby = esc_sql($orderby);
        $order = esc_sql($order);
        
        $configurations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT 100");
        
        // Group configurations by customer email
        $grouped_configs = array();
        foreach ($configurations as $config) {
            $email = strtolower(trim($config->customer_email));
            if (!isset($grouped_configs[$email])) {
                $grouped_configs[$email] = array(
                    'customer' => $config,
                    'submissions' => array()
                );
            }
            $grouped_configs[$email]['submissions'][] = $config;
        }
        
        // Sort groups by customer name if sorting by customer-related columns
        if (in_array($orderby, array('customer_name', 'customer_email'), true)) {
            uasort($grouped_configs, function($a, $b) use ($orderby, $order) {
                $val_a = strtolower($a['customer']->$orderby);
                $val_b = strtolower($b['customer']->$orderby);
                $result = strcmp($val_a, $val_b);
                return $order === 'ASC' ? $result : -$result;
            });
        }
        
        $statuses = array(
            'Neu' => 'Neu',
            'Kontaktiert' => 'Kontaktiert',
            'nicht erreicht' => 'nicht erreicht',
            'Nicht interessiert' => 'Nicht interessiert',
            'Vertrag geschlossen' => 'Vertrag geschlossen',
            'Gekündigt' => 'Gekündigt'
        );
        
        // Build sort URL helper
        $sort_url = function($column) use ($orderby, $order) {
            $new_order = ($orderby === $column && $order === 'ASC') ? 'DESC' : 'ASC';
            $url = add_query_arg(array('orderby' => $column, 'order' => $new_order));
            return esc_url($url);
        };
        
        $sort_icon = function($column) use ($orderby, $order) {
            if ($orderby !== $column) {
                return '<span class="sort-indicator" style="opacity: 0.3;">⇅</span>';
            }
            return $order === 'ASC' ? '<span class="sort-indicator">↑</span>' : '<span class="sort-indicator">↓</span>';
        };
        ?>
        <div class="wrap modular-pricing-admin">
            <h1>Submitted Forms</h1>
            <style>
                .modular-pricing-admin .wp-list-table {
                    border: 1px solid #c3c4c7;
                    border-radius: 8px;
                    box-shadow: 0 1px 1px rgba(0,0,0,.04);
                    overflow: hidden;
                }
                .modular-pricing-admin .wp-list-table thead th {
                    background: #f6f7f7;
                    border-bottom: 2px solid #c3c4c7;
                    font-weight: 600;
                    color: #1d2327;
                    padding: 12px 15px;
                }
                .modular-pricing-admin .wp-list-table tbody td {
                    padding: 12px 15px;
                    vertical-align: middle;
                }
                .modular-pricing-admin .wp-list-table tbody tr:hover {
                    background: #f6f7f7;
                }
                .modular-pricing-admin .bulk-actions {
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .modular-pricing-admin .bulk-actions select,
                .modular-pricing-admin .bulk-actions button {
                    padding: 6px 12px;
                    font-size: 13px;
                }
                .modular-pricing-admin .status-select {
                    padding: 4px 8px;
                    font-size: 13px;
                    border: 1px solid #8c8f94;
                    border-radius: 4px;
                }
                .modular-pricing-admin .status-select:focus {
                    border-color: #2271b1;
                    box-shadow: 0 0 0 1px #2271b1;
                    outline: 2px solid transparent;
                }
                .modular-pricing-admin .sortable {
                    cursor: pointer;
                    user-select: none;
                    position: relative;
                    padding-right: 25px;
                }
                .modular-pricing-admin .sortable:hover {
                    background: #e5e5e5;
                }
                .modular-pricing-admin .sort-indicator {
                    position: absolute;
                    right: 8px;
                    font-size: 12px;
                    color: #2271b1;
                }
                .modular-pricing-admin .customer-group-header {
                    background: #e8f4f8;
                    border-top: 2px solid #2271b1;
                    font-weight: 600;
                }
                .modular-pricing-admin .customer-group-header td {
                    padding: 10px 15px;
                    color: #1d2327;
                }
                .modular-pricing-admin .customer-group-header .customer-info {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .modular-pricing-admin .customer-group-header .customer-name {
                    font-size: 14px;
                }
                .modular-pricing-admin .customer-group-header .customer-email {
                    font-size: 13px;
                    color: #646970;
                    font-weight: normal;
                }
                .modular-pricing-admin .customer-group-header .submission-count {
                    font-size: 12px;
                    color: #646970;
                    font-weight: normal;
                    margin-left: auto;
                }
                .modular-pricing-admin .grouped-row {
                    background: #fafafa;
                }
                .modular-pricing-admin .grouped-row:not(:last-child) {
                    border-bottom: 1px solid #e0e0e0;
                }
            </style>
            
            <form id="pricing-configurations-form" method="post">
                <div class="bulk-actions">
                    <select name="bulk_action" id="bulk-action-selector">
                        <option value="">Bulk Actions</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="button" class="button action" id="do-bulk-action">Apply</button>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="check-column"><input type="checkbox" id="cb-select-all"></th>
                            <th class="sortable" data-column="id">
                                <a href="<?php echo $sort_url('id'); ?>" style="text-decoration: none; color: inherit;">
                                    ID <?php echo $sort_icon('id'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="status">
                                <a href="<?php echo $sort_url('status'); ?>" style="text-decoration: none; color: inherit;">
                                    Status <?php echo $sort_icon('status'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="customer_name">
                                <a href="<?php echo $sort_url('customer_name'); ?>" style="text-decoration: none; color: inherit;">
                                    Name <?php echo $sort_icon('customer_name'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="customer_email">
                                <a href="<?php echo $sort_url('customer_email'); ?>" style="text-decoration: none; color: inherit;">
                                    Email <?php echo $sort_icon('customer_email'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="customer_phone">
                                <a href="<?php echo $sort_url('customer_phone'); ?>" style="text-decoration: none; color: inherit;">
                                    Phone <?php echo $sort_icon('customer_phone'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="dog_name">
                                <a href="<?php echo $sort_url('dog_name'); ?>" style="text-decoration: none; color: inherit;">
                                    Dog Name <?php echo $sort_icon('dog_name'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="subscription_model">
                                <a href="<?php echo $sort_url('subscription_model'); ?>" style="text-decoration: none; color: inherit;">
                                    Subscription Model <?php echo $sort_icon('subscription_model'); ?>
                                </a>
                            </th>
                            <th class="sortable" data-column="duration">
                                <a href="<?php echo $sort_url('duration'); ?>" style="text-decoration: none; color: inherit;">
                                    Duration <?php echo $sort_icon('duration'); ?>
                                </a>
                            </th>
                            <th>Selected Days</th>
                            <th class="sortable" data-column="monthly_price">
                                <a href="<?php echo $sort_url('monthly_price'); ?>" style="text-decoration: none; color: inherit;">
                                    Monthly Price <?php echo $sort_icon('monthly_price'); ?>
                                </a>
                            </th>
                            <th>Notes</th>
                            <th class="sortable" data-column="created_at">
                                <a href="<?php echo $sort_url('created_at'); ?>" style="text-decoration: none; color: inherit;">
                                    Date <?php echo $sort_icon('created_at'); ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($grouped_configs)): ?>
                            <tr>
                                <td colspan="13">No configurations yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($grouped_configs as $email => $group): 
                                $customer = $group['customer'];
                                $submissions = $group['submissions'];
                                $submission_count = count($submissions);
                            ?>
                                <tr class="customer-group-header">
                                    <td colspan="13">
                                        <div class="customer-info">
                                            <span class="customer-name"><?php echo esc_html($customer->customer_name); ?></span>
                                            <span class="customer-email"><?php echo esc_html($customer->customer_email); ?></span>
                                            <?php if (!empty($customer->customer_phone)): ?>
                                                <span class="customer-phone" style="font-size: 13px; color: #646970;"><?php echo esc_html($customer->customer_phone); ?></span>
                                            <?php endif; ?>
                                            <span class="submission-count"><?php echo $submission_count; ?> submission<?php echo $submission_count > 1 ? 's' : ''; ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php foreach ($submissions as $index => $config): 
                                    $current_status = isset($config->status) && !empty($config->status) ? $config->status : 'Neu';
                                    $row_class = $submission_count > 1 ? 'grouped-row' : '';
                                ?>
                                    <tr class="<?php echo $row_class; ?>" data-id="<?php echo esc_attr($config->id); ?>">
                                        <th scope="row" class="check-column">
                                            <input type="checkbox" name="config_ids[]" value="<?php echo esc_attr($config->id); ?>" class="config-checkbox">
                                        </th>
                                        <td><?php echo esc_html($config->id); ?></td>
                                        <td>
                                            <select class="status-select" data-id="<?php echo esc_attr($config->id); ?>">
                                                <?php foreach ($statuses as $value => $label): ?>
                                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($current_status, $value); ?>>
                                                        <?php echo esc_html($label); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><?php echo esc_html($config->customer_name); ?></td>
                                        <td><?php echo esc_html($config->customer_email); ?></td>
                                        <td><?php echo esc_html($config->customer_phone); ?></td>
                                        <td><?php echo esc_html($config->dog_name); ?></td>
                                        <td><?php echo esc_html($config->subscription_model); ?></td>
                                        <td><?php echo esc_html(ucfirst($config->duration)); ?></td>
                                        <td><?php echo esc_html($config->selected_days); ?></td>
                                        <td><?php echo esc_html($config->monthly_price); ?></td>
                                        <td><?php echo esc_html($config->notes); ?></td>
                                        <td><?php echo esc_html($config->created_at); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
            
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Select all checkbox
                $('#cb-select-all').on('change', function() {
                    $('.config-checkbox').prop('checked', $(this).prop('checked'));
                });
                
                // Status change handler
                $('.status-select').on('change', function() {
                    var $select = $(this);
                    var configId = $select.data('id');
                    var newStatus = $select.val();
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'update_pricing_status',
                            config_id: configId,
                            status: newStatus,
                            nonce: '<?php echo wp_create_nonce('update_pricing_status'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Status updated successfully
                            } else {
                                alert('Error updating status: ' + (response.data || 'Unknown error'));
                                // Revert the select
                                location.reload();
                            }
                        },
                        error: function() {
                            alert('Error updating status. Please try again.');
                            location.reload();
                        }
                    });
                });
                
                // Bulk action handler
                $('#do-bulk-action').on('click', function() {
                    var action = $('#bulk-action-selector').val();
                    var selectedIds = [];
                    
                    $('.config-checkbox:checked').each(function() {
                        selectedIds.push($(this).val());
                    });
                    
                    if (!action) {
                        alert('Please select a bulk action.');
                        return;
                    }
                    
                    if (selectedIds.length === 0) {
                        alert('Please select at least one item.');
                        return;
                    }
                    
                    if (action === 'delete') {
                        if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' item(s)?')) {
                            return;
                        }
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'bulk_delete_pricing_configs',
                                config_ids: selectedIds,
                                nonce: '<?php echo wp_create_nonce('bulk_delete_pricing_configs'); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    alert('Error deleting items: ' + (response.data || 'Unknown error'));
                                }
                            },
                            error: function() {
                                alert('Error deleting items. Please try again.');
                            }
                        });
                    }
                });
            });
            </script>
        </div>
        <?php
    }
}

