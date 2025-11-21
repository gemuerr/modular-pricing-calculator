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
            'User Configurations',
            'User Configurations',
            'manage_options',
            'pricing-configurations',
            array($this, 'configurations_page')
        );
    }

    public function admin_page() {
        $settings = Modular_Pricing_Settings::get_settings();
        ?>
        <div class="wrap">
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
                    .pricing-table {
                        border-collapse: collapse;
                    }
                    .pricing-table td {
                        padding: 5px 10px;
                    }
                </style>

                <?php submit_button(); ?>
            </form>

            <div style="margin-top: 40px; padding: 20px; background: #f0f0f1; border-left: 4px solid #2271b1;">
                <h3>How to Use</h3>
                <p>Add the pricing calculator to any page or post using this shortcode:</p>
                <code style="background: white; padding: 10px; display: block; margin-top: 10px;">[pricing_calculator]</code>

                <h4 style="margin-top: 20px;">Variable Pricing:</h4>
                <p>The pricing is variable based on the number of days selected. Configure different prices per day for 1-5 days per week. The final monthly price is calculated as: (price per day × number of days per week × 4 weeks).</p>

                <h4 style="margin-top: 20px;">Setting up reCAPTCHA v2:</h4>
                <ol>
                    <li>Go to <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a></li>
                    <li>Register a new site with reCAPTCHA v2 (checkbox type)</li>
                    <li>Add your domain(s)</li>
                    <li>Copy the Site Key and Secret Key to the fields above</li>
                    <li>Save changes</li>
                </ol>
            </div>

            <?php
            // Email diagnostic section
            $last_email_status = get_transient('modular_pricing_last_email_status');
            if ($last_email_status) {
                ?>
                <div style="margin-top: 40px; padding: 20px; background: <?php echo $last_email_status['success'] ? '#d4edda' : '#f8d7da'; ?>; border-left: 4px solid <?php echo $last_email_status['success'] ? '#28a745' : '#dc3545'; ?>;">
                    <h3>Email Status Diagnostic</h3>
                    <p><strong>Last Email Attempt:</strong> <?php echo esc_html($last_email_status['timestamp']); ?></p>
                    <p><strong>Recipient:</strong> <?php echo esc_html($last_email_status['to']); ?></p>
                    <p><strong>Status:</strong> 
                        <?php if ($last_email_status['success']): ?>
                            <span style="color: #155724; font-weight: bold;">✓ Success</span>
                        <?php else: ?>
                            <span style="color: #721c24; font-weight: bold;">✗ Failed</span>
                        <?php endif; ?>
                    </p>
                    <?php if (!$last_email_status['success'] && !empty($last_email_status['error'])): ?>
                        <p><strong>Error:</strong> <code><?php echo esc_html($last_email_status['error']); ?></code></p>
                    <?php endif; ?>
                    <?php if (!$last_email_status['success']): ?>
                        <div style="margin-top: 15px; padding: 15px; background: #fff; border-radius: 4px;">
                            <h4 style="margin-top: 0;">Troubleshooting Email Issues:</h4>
                            <ul>
                                <li><strong>Check WordPress mail configuration:</strong> WordPress may not be configured to send emails. Consider installing an SMTP plugin like "WP Mail SMTP" or "Easy WP SMTP".</li>
                                <li><strong>Check server mail() function:</strong> Some hosting providers disable PHP's mail() function. Contact your hosting provider or use an SMTP plugin.</li>
                                <li><strong>Check spam folder:</strong> Emails might be going to spam. Check the recipient's spam/junk folder.</li>
                                <li><strong>Enable WP_DEBUG:</strong> Add <code>define('WP_DEBUG', true);</code> and <code>define('WP_DEBUG_LOG', true);</code> to your wp-config.php to see detailed error logs in <code>wp-content/debug.log</code>.</li>
                                <li><strong>Test WordPress mail:</strong> Try sending a test email from WordPress Settings → General (if available) or use a plugin to test email functionality.</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public function configurations_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';

        $configurations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 100");
        ?>
        <div class="wrap">
            <h1>User Pricing Configurations</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Dog Name</th>
                        <th>Subscription Model</th>
                        <th>Duration</th>
                        <th>Selected Days</th>
                        <th>Monthly Price</th>
                        <th>Notes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($configurations)): ?>
                        <tr>
                            <td colspan="11">No configurations yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($configurations as $config): ?>
                            <tr>
                                <td><?php echo esc_html($config->id); ?></td>
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
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

