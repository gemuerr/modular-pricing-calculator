<?php
/**
 * Plugin Name: Modular Pricing Calculator
 * Plugin URI: https://example.com
 * Description: Configure modular pricing based on subscription model, day duration, and number of days with accordion toggle, variable pricing, and reCAPTCHA protection
 * Version: 0.9.2
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: modular-pricing-calculator
 * 
 * Changelog:
 * 0.1.0 - Initial release with basic pricing configuration
 * 0.2.0 - Added subscription model A and B with half/full day options
 * 0.3.0 - Added number of days selection (1-30)
 * 0.4.0 - Implemented weekday selector (Mon-Fri) and German translations
 * 0.5.0 - Added reCAPTCHA v2 spam protection and email notifications
 * 0.6.0 - Configurable subscription model names and variable pricing (1-5 days/week)
 * 0.7.0 - Added phone number and notes fields, Figtree font, configurable colors
 * 0.8.0 - Fixed monthly calculation (×4 weeks), added accordion toggle for form
 * 0.9.0 - Added inline validation, optional reCAPTCHA, improved emails, Gutenberg block support
 * 0.9.1 - Fixed Gmail email compatibility with proper line breaks and table-based HTML layout
 * 0.9.2 - Simplified email to plain text only, removed all HTML/styling for universal compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Plugin {
    
    private $option_name = 'modular_pricing_settings';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_shortcode('pricing_calculator', array($this, 'pricing_calculator_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_ajax_save_pricing_configuration', array($this, 'save_pricing_configuration'));
        add_action('wp_ajax_nopriv_save_pricing_configuration', array($this, 'save_pricing_configuration'));
        add_action('init', array($this, 'register_block'));
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
    
    public function register_settings() {
        register_setting('modular_pricing_group', $this->option_name);
    }
    
    public function register_block() {
        // Register the block only if Gutenberg is available
        if (!function_exists('register_block_type')) {
            return;
        }
        
        // Register block
        register_block_type('modular-pricing/calculator', array(
            'editor_script' => 'modular-pricing-block-editor',
            'editor_style' => 'modular-pricing-block-editor-style',
            'render_callback' => array($this, 'pricing_calculator_shortcode'),
        ));
        
        // Enqueue block editor assets
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }
    
    public function enqueue_block_editor_assets() {
        // Inline the block JavaScript
        wp_register_script(
            'modular-pricing-block-editor',
            '',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            '0.9.2',
            true
        );
        
        wp_enqueue_script('modular-pricing-block-editor');
        
        // Add inline script
        $block_script = "
        (function(blocks, element, blockEditor, components) {
            var el = element.createElement;
            var InspectorControls = blockEditor.InspectorControls;
            var PanelBody = components.PanelBody;
            
            blocks.registerBlockType('modular-pricing/calculator', {
                title: 'Modular Pricing Calculator',
                icon: 'money-alt',
                category: 'widgets',
                description: 'Add the pricing calculator form to your page',
                attributes: {},
                
                edit: function(props) {
                    return el('div', {
                        style: {
                            padding: '40px',
                            background: '#f0f0f1',
                            borderRadius: '8px',
                            textAlign: 'center',
                            border: '2px dashed #8c8f94'
                        }
                    },
                        el('span', {
                            className: 'dashicons dashicons-money-alt',
                            style: {
                                fontSize: '48px',
                                width: '48px',
                                height: '48px',
                                color: '#2271b1'
                            }
                        }),
                        el('h3', {
                            style: {
                                marginTop: '10px',
                                color: '#1e1e1e'
                            }
                        }, 'Modular Pricing Calculator'),
                        el('p', {
                            style: {
                                color: '#757575',
                                marginBottom: '0'
                            }
                        }, 'The pricing form will appear here on the frontend.')
                    );
                },
                
                save: function() {
                    return null; // Dynamic block, rendered server-side
                }
            });
        })(
            window.wp.blocks,
            window.wp.element,
            window.wp.blockEditor,
            window.wp.components
        );
        ";
        
        wp_add_inline_script('modular-pricing-block-editor', $block_script);
        
        // Add editor styles
        wp_register_style(
            'modular-pricing-block-editor-style',
            '',
            array(),
            '0.9.2'
        );
        wp_enqueue_style('modular-pricing-block-editor-style');
        
        $editor_css = "
        .wp-block-modular-pricing-calculator {
            margin: 20px 0;
        }
        ";
        wp_add_inline_style('modular-pricing-block-editor-style', $editor_css);
    }
    
    public function admin_page() {
        $settings = get_option($this->option_name, $this->get_default_settings());
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
    
    private function get_default_settings() {
        return array(
            'model_a_name' => 'Fix-Abo',
            'model_b_name' => 'Flex-Abo',
            'model_a_half_1' => 30,
            'model_a_half_2' => 28,
            'model_a_half_3' => 26,
            'model_a_half_4' => 24,
            'model_a_half_5' => 22,
            'model_a_full_1' => 50,
            'model_a_full_2' => 48,
            'model_a_full_3' => 46,
            'model_a_full_4' => 44,
            'model_a_full_5' => 42,
            'model_b_half_1' => 35,
            'model_b_half_2' => 33,
            'model_b_half_3' => 31,
            'model_b_half_4' => 29,
            'model_b_half_5' => 27,
            'model_b_full_1' => 55,
            'model_b_full_2' => 53,
            'model_b_full_3' => 51,
            'model_b_full_4' => 49,
            'model_b_full_5' => 47,
            'currency' => '€',
            'form_display_mode' => 'accordion',
            'notification_email' => get_option('admin_email'),
            'recaptcha_enabled' => 0,
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => '',
            'primary_color' => '#4a90e2',
            'primary_hover_color' => '#357abd'
        );
    }
    
    public function enqueue_frontend_scripts() {
        if (has_shortcode(get_post()->post_content, 'pricing_calculator')) {
            $settings = get_option($this->option_name, $this->get_default_settings());
            if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key'])) {
                wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
            }
        }
    }
    
    private function verify_recaptcha($response) {
        $settings = get_option($this->option_name, $this->get_default_settings());
        $secret_key = $settings['recaptcha_secret_key'];
        
        if (empty($secret_key)) {
            return true;
        }
        
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        $response = wp_remote_post($verify_url, array(
            'body' => array(
                'secret' => $secret_key,
                'response' => $response
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $result = json_decode($response_body);
        
        return isset($result->success) && $result->success === true;
    }
    
    public function save_pricing_configuration() {
        $settings = get_option($this->option_name, $this->get_default_settings());
        
        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key'])) {
            $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
            
            if (empty($recaptcha_response) || !$this->verify_recaptcha($recaptcha_response)) {
                wp_send_json_error(array('message' => 'reCAPTCHA verification failed. Please try again.'));
                return;
            }
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';
        
        $customer_name = sanitize_text_field($_POST['customer_name']);
        $customer_email = sanitize_email($_POST['customer_email']);
        $customer_phone = sanitize_text_field($_POST['customer_phone']);
        $dog_name = sanitize_text_field($_POST['dog_name']);
        $subscription_model = sanitize_text_field($_POST['subscription_model']);
        $duration = sanitize_text_field($_POST['duration']);
        $selected_days = sanitize_text_field($_POST['selected_days']);
        $monthly_price = sanitize_text_field($_POST['monthly_price']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $wpdb->insert(
            $table_name,
            array(
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'dog_name' => $dog_name,
                'subscription_model' => $subscription_model,
                'duration' => $duration,
                'selected_days' => $selected_days,
                'monthly_price' => $monthly_price,
                'notes' => $notes,
                'created_at' => current_time('mysql')
            )
        );
        
        $notification_email = $settings['notification_email'];
        
        if (!empty($notification_email)) {
            $subject = 'Neue Betreuungsanfrage von ' . $customer_name;
            $received_date = current_time('d.m.Y');
            $received_time = current_time('H:i');
            
            $message_lines = array(
                'Neue Betreuungsanfrage',
                '',
                'Kontaktdaten',
                'Name: ' . $customer_name,
                'E-Mail: ' . $customer_email
            );
            
            if (!empty($customer_phone)) {
                $message_lines[] = 'Telefon: ' . $customer_phone;
            }
            
            $message_lines[] = 'Hundename: ' . $dog_name;
            $message_lines[] = '';
            $message_lines[] = 'Betreuungsdetails';
            $message_lines[] = 'Abo-Modell: ' . $subscription_model;
            $message_lines[] = 'Betreuungsdauer: ' . ($duration == 'half' ? 'Halbtags' : 'Ganztags');
            $message_lines[] = 'Wochentage: ' . $selected_days;
            $message_lines[] = 'Monatlicher Preis: ' . $monthly_price;
            
            if (!empty($notes)) {
                $message_lines[] = '';
                $message_lines[] = 'Anmerkungen';
                $message_lines[] = $notes;
            }
            
            $message_lines[] = '';
            $message_lines[] = 'Eingegangen am ' . $received_date . ' um ' . $received_time . ' Uhr';
            
            $message_plain = implode("\n", $message_lines);
            
            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
            );
            
            wp_mail($notification_email, $subject, $message_plain, $headers);
        }
        
        wp_send_json_success(array('message' => 'Configuration saved successfully!'));
    }
    
    public function pricing_calculator_shortcode($atts) {
        $settings = get_option($this->option_name, $this->get_default_settings());
        $recaptcha_enabled = !empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key']);
        $form_display_mode = isset($settings['form_display_mode']) ? $settings['form_display_mode'] : 'accordion';
        $is_accordion = ($form_display_mode === 'accordion');
        
        $primary_color = esc_attr($settings['primary_color']);
        $primary_hover = esc_attr($settings['primary_hover_color']);
        
        // Convert hex to RGB for shadow effects
        $rgb = sscanf($primary_color, "#%02x%02x%02x");
        $primary_rgb = implode(', ', $rgb);
        
        ob_start();
        ?>
        <div class="modular-pricing-calculator <?php echo $is_accordion ? 'mode-accordion' : 'mode-always-open'; ?>">
            <?php if ($is_accordion): ?>
            <button type="button" class="toggle-form-button" id="toggle-form-btn">
                <span class="toggle-text">Jetzt Betreuung konfigurieren</span>
                <span class="toggle-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </button>
            <?php endif; ?>
            
            <div class="form-wrapper <?php echo !$is_accordion ? 'open' : ''; ?>" id="form-wrapper">
                <h3>Deine maßgeschneiderte Betreuung</h3>
                <form id="pricing-form">
                <div class="pricing-field">
                    <label for="customer-name">Dein Name: <span class="required">*</span></label>
                    <input type="text" id="customer-name" name="customer_name" required />
                    <span class="field-error" id="error-customer-name"></span>
                </div>
                
                <div class="pricing-field">
                    <label for="customer-email">Deine E-Mail: <span class="required">*</span></label>
                    <input type="email" id="customer-email" name="customer_email" required />
                    <span class="field-error" id="error-customer-email"></span>
                </div>
                
                <div class="pricing-field">
                    <label for="customer-phone">Deine Telefonnummer:</label>
                    <input type="tel" id="customer-phone" name="customer_phone" pattern="[0-9+\s\-()]*" placeholder="+49 123 456789" />
                    <span class="help-text">Nur Zahlen, +, Leerzeichen, - und Klammern erlaubt</span>
                    <span class="field-error" id="error-customer-phone"></span>
                </div>
                
                <div class="pricing-field">
                    <label for="dog-name">Name deines Hundes: <span class="required">*</span></label>
                    <input type="text" id="dog-name" name="dog_name" required />
                    <span class="field-error" id="error-dog-name"></span>
                </div>
                
                <div class="pricing-field">
                    <label>Abo-Modell:</label>
                    <div class="radio-selector">
                        <label class="radio-option">
                            <input type="radio" name="subscription_model" value="a" class="radio-input" checked />
                            <span class="radio-label"><?php echo esc_html($settings['model_a_name']); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="subscription_model" value="b" class="radio-input" />
                            <span class="radio-label"><?php echo esc_html($settings['model_b_name']); ?></span>
                        </label>
                    </div>
                </div>
                
                <div class="pricing-field">
                    <label>Betreuungsdauer:</label>
                    <div class="radio-selector">
                        <label class="radio-option">
                            <input type="radio" name="day_duration" value="half" class="radio-input" checked />
                            <span class="radio-label">Halbtags</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="day_duration" value="full" class="radio-input" />
                            <span class="radio-label">Ganztags</span>
                        </label>
                    </div>
                </div>
                
                <div class="pricing-field">
                    <label>Wochentage auswählen:</label>
                    <div class="weekday-selector">
                        <label class="weekday-option">
                            <input type="checkbox" name="weekdays" value="monday" class="weekday-checkbox" />
                            <span class="weekday-label">Mo</span>
                        </label>
                        <label class="weekday-option">
                            <input type="checkbox" name="weekdays" value="tuesday" class="weekday-checkbox" />
                            <span class="weekday-label">Di</span>
                        </label>
                        <label class="weekday-option">
                            <input type="checkbox" name="weekdays" value="wednesday" class="weekday-checkbox" />
                            <span class="weekday-label">Mi</span>
                        </label>
                        <label class="weekday-option">
                            <input type="checkbox" name="weekdays" value="thursday" class="weekday-checkbox" />
                            <span class="weekday-label">Do</span>
                        </label>
                        <label class="weekday-option">
                            <input type="checkbox" name="weekdays" value="friday" class="weekday-checkbox" />
                            <span class="weekday-label">Fr</span>
                        </label>
                    </div>
                    <span class="help-text">Wähle die Tage aus, an denen du die Betreuung nutzen möchtest</span>
                    <span class="field-error" id="error-weekdays"></span>
                </div>
                
                <div class="pricing-field">
                    <label for="notes">Anmerkungen:</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Hier kannst du uns zusätzliche Informationen mitteilen..."></textarea>
                </div>
                
                <div class="pricing-summary">
                    <div class="summary-row">
                        <span>Preis pro Tag:</span>
                        <span id="price-per-day"><?php echo $settings['currency']; ?>0,00</span>
                    </div>
                    <div class="summary-row">
                        <span>Anzahl Tage pro Woche:</span>
                        <span id="days-display">0</span>
                    </div>
                    <div class="summary-row">
                        <span>Monatlicher Preis (ca. 4 Wochen):</span>
                        <span id="calculated-price"><?php echo $settings['currency']; ?>0,00</span>
                    </div>
                </div>
                
                <?php if ($recaptcha_enabled): ?>
                <div class="pricing-field">
                    <div class="recaptcha-wrapper">
                        <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($settings['recaptcha_site_key']); ?>"></div>
                    </div>
                    <span class="field-error" id="error-recaptcha"></span>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="submit-button">Unverbindlich anfragen</button>
                <div id="form-message"></div>
            </form>
            </div>
        </div>
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap');
            
            .modular-pricing-calculator {
                max-width: 650px;
                padding: 0;
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                margin: 40px auto;
                font-family: 'Figtree', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                overflow: hidden;
            }
            .modular-pricing-calculator * {
                font-family: 'Figtree', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            
            .toggle-form-button {
                width: 100%;
                padding: 24px 40px;
                background: <?php echo $primary_color; ?>;
                color: white;
                border: none;
                border-radius: 20px;
                font-size: 18px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 4px 12px rgba(<?php echo $primary_rgb; ?>, 0.3);
            }
            
            .mode-always-open .toggle-form-button {
                display: none;
            }
            
            .toggle-form-button:hover {
                background: <?php echo $primary_hover; ?>;
                box-shadow: 0 6px 20px rgba(<?php echo $primary_rgb; ?>, 0.4);
                transform: translateY(-1px);
            }
            
            .toggle-form-button:active {
                transform: translateY(0);
            }
            
            .toggle-icon {
                transition: transform 0.3s ease;
                display: flex;
                align-items: center;
            }
            
            .toggle-form-button.active .toggle-icon {
                transform: rotate(180deg);
            }
            
            .form-wrapper {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 0 40px;
            }
            
            .mode-always-open .form-wrapper {
                max-height: none;
                overflow: visible;
                padding: 40px;
                transition: none;
            }
            
            .form-wrapper.open {
                max-height: 5000px;
                padding: 40px;
                padding-top: 30px;
            }
            
            .modular-pricing-calculator h3 {
                margin-top: 0;
                margin-bottom: 30px;
                color: #2c3e50;
                text-align: center;
                font-size: 28px;
                font-weight: 600;
                letter-spacing: -0.5px;
            }
            .pricing-field {
                margin-bottom: 24px;
            }
            .pricing-field label {
                display: block;
                margin-bottom: 10px;
                font-weight: 500;
                color: #2c3e50;
                font-size: 15px;
            }
            .pricing-field input[type="text"],
            .pricing-field input[type="email"],
            .pricing-field input[type="tel"],
            .pricing-field textarea {
                width: 100%;
                padding: 14px 16px;
                border: 2px solid #e8eef3;
                border-radius: 12px;
                font-size: 16px;
                box-sizing: border-box;
                transition: all 0.3s ease;
                background: #f8fafb;
                color: #2c3e50;
            }
            .pricing-field input[type="text"]:focus,
            .pricing-field input[type="email"]:focus,
            .pricing-field input[type="tel"]:focus,
            .pricing-field textarea:focus {
                outline: none;
                border-color: <?php echo $primary_color; ?>;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(<?php echo $primary_rgb; ?>, 0.1);
            }
            .pricing-field input.error,
            .pricing-field textarea.error {
                border-color: #e74c3c;
                background: #fff5f5;
            }
            .pricing-field input.error:focus,
            .pricing-field textarea.error:focus {
                box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            }
            .field-error {
                display: none;
                color: #e74c3c;
                font-size: 13px;
                margin-top: 6px;
                font-weight: 500;
            }
            .field-error.show {
                display: block;
            }
            .pricing-field textarea {
                resize: vertical;
                min-height: 100px;
            }
            .radio-selector,
            .weekday-selector {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .radio-option,
            .weekday-option {
                flex: 1;
                min-width: 100px;
                cursor: pointer;
            }
            .weekday-option {
                min-width: 60px;
            }
            .radio-option input[type="radio"],
            .weekday-option input[type="checkbox"] {
                display: none;
            }
            .radio-input,
            .radio-label,
            .weekday-label {
                display: block;
                padding: 14px 10px;
                text-align: center;
                background: #f8fafb;
                border: 2px solid #e8eef3;
                border-radius: 12px;
                font-weight: 500;
                color: #2c3e50;
                transition: all 0.3s ease;
                font-size: 15px;
            }
            .radio-option input[type="radio"]:checked + .radio-label,
            .weekday-option input[type="checkbox"]:checked + .weekday-label {
                background: <?php echo $primary_color; ?>;
                color: white;
                border-color: <?php echo $primary_color; ?>;
                box-shadow: 0 4px 12px rgba(<?php echo $primary_rgb; ?>, 0.3);
            }
            .radio-label:hover,
            .weekday-label:hover {
                border-color: <?php echo $primary_color; ?>;
                background: #ffffff;
            }
            .help-text {
                display: block;
                margin-top: 6px;
                font-size: 13px;
                color: #7f8c9a;
            }
            .required {
                color: #e74c3c;
            }
            .pricing-summary {
                margin: 35px 0;
                padding: 28px;
                background: #f8fafb;
                border-radius: 16px;
                border: 2px solid #e8eef3;
            }
            .summary-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                color: #2c3e50;
                font-size: 15px;
            }
            .summary-row:not(:last-child) {
                border-bottom: 1px solid #e8eef3;
            }
            #price-per-day,
            #days-display,
            #calculated-price {
                color: #2c3e50;
                font-weight: 600;
                font-size: 15px;
            }
            .recaptcha-wrapper {
                margin: 0;
                display: flex;
                justify-content: center;
            }
            .submit-button {
                width: 100%;
                padding: 16px;
                background: <?php echo $primary_color; ?>;
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 17px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(<?php echo $primary_rgb; ?>, 0.3);
                margin-top: 10px;
            }
            .submit-button:hover {
                background: <?php echo $primary_hover; ?>;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(<?php echo $primary_rgb; ?>, 0.4);
            }
            .submit-button:active {
                transform: translateY(0);
            }
            .submit-button:disabled {
                background: #bdc3c7;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }
            #form-message {
                margin-top: 20px;
                padding: 16px 20px;
                border-radius: 12px;
                display: none;
                font-weight: 500;
            }
            #form-message.success {
                display: block;
                background: #d4edda;
                color: #155724;
                border: 2px solid #c3e6cb;
            }
            #form-message.error {
                display: block;
                background: #f8d7da;
                color: #721c24;
                border: 2px solid #f5c6cb;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .toggle-form-button {
                    padding: 20px 30px;
                    font-size: 16px;
                    border-radius: 20px 20px 0 0;
                }
                .form-wrapper.open {
                    padding: 30px 20px;
                }
                .mode-always-open .form-wrapper {
                    padding: 30px 20px;
                }
                .modular-pricing-calculator h3 {
                    font-size: 24px;
                }
                .radio-option,
                .weekday-option {
                    min-width: 50px;
                }
            }
        </style>
        
        <script>
            (function() {
                const prices = <?php echo json_encode($settings); ?>;
                const form = document.getElementById('pricing-form');
                const priceDisplay = document.getElementById('calculated-price');
                const pricePerDayDisplay = document.getElementById('price-per-day');
                const daysDisplay = document.getElementById('days-display');
                const checkboxes = document.querySelectorAll('.weekday-checkbox');
                const radioButtons = document.querySelectorAll('.radio-input');
                const submitButton = form.querySelector('.submit-button');
                const formMessage = document.getElementById('form-message');
                const recaptchaEnabled = <?php echo $recaptcha_enabled ? 'true' : 'false'; ?>;
                const isAccordion = <?php echo $is_accordion ? 'true' : 'false'; ?>;
                
                // Toggle form functionality (only for accordion mode)
                if (isAccordion) {
                    const toggleButton = document.getElementById('toggle-form-btn');
                    const formWrapper = document.getElementById('form-wrapper');
                    let recaptchaLoaded = false;
                    
                    toggleButton.addEventListener('click', function() {
                        const isOpen = formWrapper.classList.contains('open');
                        
                        if (isOpen) {
                            formWrapper.classList.remove('open');
                            toggleButton.classList.remove('active');
                            toggleButton.querySelector('.toggle-text').textContent = 'Jetzt Betreuung konfigurieren';
                        } else {
                            formWrapper.classList.add('open');
                            toggleButton.classList.add('active');
                            toggleButton.querySelector('.toggle-text').textContent = 'Formular schließen';
                            
                            // Render reCAPTCHA when form is first opened
                            if (recaptchaEnabled && !recaptchaLoaded && typeof grecaptcha !== 'undefined') {
                                setTimeout(function() {
                                    try {
                                        grecaptcha.render(document.querySelector('.g-recaptcha'));
                                        recaptchaLoaded = true;
                                    } catch(e) {
                                        // reCAPTCHA already rendered
                                    }
                                }, 100);
                            }
                        }
                    });
                }
                
                const weekdayNames = {
                    'monday': 'Montag',
                    'tuesday': 'Dienstag',
                    'wednesday': 'Mittwoch',
                    'thursday': 'Donnerstag',
                    'friday': 'Freitag'
                };
                
                // Phone number validation - only numbers, +, spaces, dashes, parentheses
                const phoneInput = document.getElementById('customer-phone');
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    // Remove any characters that aren't numbers, +, spaces, dashes, or parentheses
                    value = value.replace(/[^0-9+\s\-()]/g, '');
                    e.target.value = value;
                });
                
                // Clear all errors
                function clearErrors() {
                    document.querySelectorAll('.field-error').forEach(function(error) {
                        error.classList.remove('show');
                        error.textContent = '';
                    });
                    document.querySelectorAll('.error').forEach(function(field) {
                        field.classList.remove('error');
                    });
                }
                
                // Show error for specific field
                function showError(fieldId, message) {
                    const field = document.getElementById(fieldId);
                    const errorSpan = document.getElementById('error-' + fieldId);
                    
                    if (field) {
                        field.classList.add('error');
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    if (errorSpan) {
                        errorSpan.textContent = message;
                        errorSpan.classList.add('show');
                    }
                }
                
                // Clear error on input
                document.querySelectorAll('input, textarea').forEach(function(input) {
                    input.addEventListener('input', function() {
                        this.classList.remove('error');
                        const errorId = 'error-' + this.id;
                        const errorSpan = document.getElementById(errorId);
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                    });
                });
                
                // Clear weekday errors when selecting
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        const errorSpan = document.getElementById('error-weekdays');
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                    });
                });
                
                function calculatePrice() {
                    const model = document.querySelector('input[name="subscription_model"]:checked').value;
                    const duration = document.querySelector('input[name="day_duration"]:checked').value;
                    const checkedDays = Array.from(checkboxes).filter(cb => cb.checked);
                    const numDays = checkedDays.length;
                    
                    if (numDays === 0) {
                        pricePerDayDisplay.textContent = prices.currency + '0,00';
                        daysDisplay.textContent = '0';
                        priceDisplay.textContent = prices.currency + '0,00';
                        return;
                    }
                    
                    // Get price per day based on model, duration and number of days
                    const key = 'model_' + model + '_' + duration + '_' + numDays;
                    const pricePerDay = parseFloat(prices[key]) || 0;
                    
                    // Calculate monthly price: price per day × days per week × 4 weeks
                    const monthlyPrice = pricePerDay * numDays * 4;
                    
                    pricePerDayDisplay.textContent = prices.currency + pricePerDay.toFixed(2).replace('.', ',');
                    daysDisplay.textContent = numDays;
                    priceDisplay.textContent = prices.currency + monthlyPrice.toFixed(2).replace('.', ',');
                }
                
                radioButtons.forEach(rb => rb.addEventListener('change', calculatePrice));
                checkboxes.forEach(cb => cb.addEventListener('change', calculatePrice));
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    clearErrors();
                    let hasErrors = false;
                    
                    // Validate name
                    const customerName = document.getElementById('customer-name').value.trim();
                    if (!customerName) {
                        showError('customer-name', 'Bitte gib deinen Namen ein.');
                        hasErrors = true;
                    }
                    
                    // Validate email
                    const customerEmail = document.getElementById('customer-email').value.trim();
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!customerEmail) {
                        showError('customer-email', 'Bitte gib deine E-Mail-Adresse ein.');
                        hasErrors = true;
                    } else if (!emailPattern.test(customerEmail)) {
                        showError('customer-email', 'Bitte gib eine gültige E-Mail-Adresse ein.');
                        hasErrors = true;
                    }
                    
                    // Validate phone (if filled)
                    const customerPhone = document.getElementById('customer-phone').value.trim();
                    const phonePattern = /^[0-9+\s\-()]+$/;
                    if (customerPhone && !phonePattern.test(customerPhone)) {
                        showError('customer-phone', 'Bitte gib eine gültige Telefonnummer ein (nur Zahlen, +, -, Leerzeichen und Klammern).');
                        hasErrors = true;
                    }
                    
                    // Validate dog name
                    const dogName = document.getElementById('dog-name').value.trim();
                    if (!dogName) {
                        showError('dog-name', 'Bitte gib den Namen deines Hundes ein.');
                        hasErrors = true;
                    }
                    
                    // Validate weekdays
                    const checkedDays = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => weekdayNames[cb.value]);
                    
                    if (checkedDays.length === 0) {
                        showError('weekdays', 'Bitte wähle mindestens einen Wochentag aus.');
                        hasErrors = true;
                    }
                    
                    // Validate reCAPTCHA
                    if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                        const recaptchaResponse = grecaptcha.getResponse();
                        if (!recaptchaResponse) {
                            showError('recaptcha', 'Bitte bestätige, dass du kein Roboter bist.');
                            hasErrors = true;
                        }
                    }
                    
                    if (hasErrors) {
                        return;
                    }
                    
                    const model = document.querySelector('input[name="subscription_model"]:checked').value;
                    const duration = document.querySelector('input[name="day_duration"]:checked').value;
                    const numDays = checkedDays.length;
                    const key = 'model_' + model + '_' + duration + '_' + numDays;
                    const pricePerDay = parseFloat(prices[key]) || 0;
                    
                    // Calculate monthly price correctly
                    const monthlyPrice = pricePerDay * numDays * 4;
                    
                    const modelName = model === 'a' ? prices.model_a_name : prices.model_b_name;
                    
                    const formData = new FormData();
                    formData.append('action', 'save_pricing_configuration');
                    formData.append('customer_name', document.getElementById('customer-name').value);
                    formData.append('customer_email', document.getElementById('customer-email').value);
                    formData.append('customer_phone', document.getElementById('customer-phone').value);
                    formData.append('dog_name', document.getElementById('dog-name').value);
                    formData.append('subscription_model', modelName);
                    formData.append('duration', duration);
                    formData.append('selected_days', checkedDays.join(', '));
                    formData.append('monthly_price', prices.currency + monthlyPrice.toFixed(2).replace('.', ','));
                    formData.append('notes', document.getElementById('notes').value);
                    
                    if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                        formData.append('g-recaptcha-response', grecaptcha.getResponse());
                    }
                    
                    submitButton.disabled = true;
                    submitButton.textContent = 'Wird gesendet...';
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            formMessage.className = 'success';
                            formMessage.textContent = 'Vielen Dank! Wir haben deine Anfrage erhalten und melden uns bald bei dir.';
                            form.reset();
                            if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                                grecaptcha.reset();
                            }
                            calculatePrice();
                        } else {
                            formMessage.className = 'error';
                            formMessage.textContent = data.data && data.data.message ? data.data.message : 'Fehler beim Senden. Bitte versuche es erneut.';
                            if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                                grecaptcha.reset();
                            }
                        }
                    })
                    .catch(error => {
                        formMessage.className = 'error';
                        formMessage.textContent = 'Fehler beim Senden. Bitte versuche es erneut.';
                        if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                            grecaptcha.reset();
                        }
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Unverbindlich anfragen';
                    });
                });
                
                calculatePrice();
            })();
        </script>
        <?php
        return ob_get_clean();
    }
}

// Create database table on plugin activation
register_activation_hook(__FILE__, 'modular_pricing_create_table');
function modular_pricing_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pricing_configurations';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        customer_name varchar(255) NOT NULL,
        customer_email varchar(255) NOT NULL,
        customer_phone varchar(50) DEFAULT '',
        dog_name varchar(255) NOT NULL,
        subscription_model varchar(50) NOT NULL,
        duration varchar(10) NOT NULL,
        selected_days text NOT NULL,
        monthly_price varchar(50) NOT NULL,
        notes text DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

new Modular_Pricing_Plugin();
?>