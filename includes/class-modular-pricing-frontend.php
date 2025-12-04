<?php

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Frontend {
    public function __construct() {
        add_shortcode('pricing_calculator', array($this, 'pricing_calculator_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('init', array($this, 'register_block'));
    }

    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('modular-pricing/calculator', array(
            'editor_script' => 'modular-pricing-block-editor',
            'editor_style' => 'modular-pricing-block-editor-style',
            'render_callback' => array($this, 'pricing_calculator_shortcode'),
        ));

        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    public function enqueue_block_editor_assets() {
        wp_register_script(
            'modular-pricing-block-editor',
            '',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            MODULAR_PRICING_PLUGIN_VERSION,
            true
        );

        wp_enqueue_script('modular-pricing-block-editor');

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
                    return null;
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

        wp_register_style(
            'modular-pricing-block-editor-style',
            '',
            array(),
            MODULAR_PRICING_PLUGIN_VERSION
        );
        wp_enqueue_style('modular-pricing-block-editor-style');

        $editor_css = "
        .wp-block-modular-pricing-calculator {
            margin: 20px 0;
        }
        ";
        wp_add_inline_style('modular-pricing-block-editor-style', $editor_css);
    }

    public function enqueue_frontend_scripts() {
        if (!function_exists('has_shortcode')) {
            return;
        }

        $post = get_post();
        if (!$post || !has_shortcode($post->post_content, 'pricing_calculator')) {
            return;
        }

        $settings = Modular_Pricing_Settings::get_settings();
        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key'])) {
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
        }
    }

    public function pricing_calculator_shortcode($atts) {
        $settings = Modular_Pricing_Settings::get_settings();
        $recaptcha_enabled = !empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key']);
        $form_display_mode = isset($settings['form_display_mode']) ? $settings['form_display_mode'] : 'accordion';
        $is_accordion = ($form_display_mode === 'accordion');
        $form_step_mode = isset($settings['form_step_mode']) ? $settings['form_step_mode'] : 'combined';
        $is_separate_steps = ($form_step_mode === 'separate');

        $primary_color = esc_attr($settings['primary_color']);
        $primary_hover = esc_attr($settings['primary_hover_color']);
        $rgb = sscanf($primary_color, "#%02x%02x%02x");
        $primary_rgb = implode(', ', $rgb);

        ob_start();
        ?>
        <div class="modular-pricing-calculator <?php echo $is_accordion ? 'mode-accordion' : 'mode-always-open'; ?> <?php echo $is_separate_steps ? 'step-mode-separated' : 'step-mode-combined'; ?>">
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
                <form id="pricing-form" class="<?php echo $is_separate_steps ? 'two-step-enabled' : 'single-step'; ?>">
                <div class="form-step step-pricing <?php echo $is_separate_steps ? 'active' : ''; ?>">
                    <?php if ($is_separate_steps): ?>
                    <div class="step-heading">
                        <span class="step-badge">Schritt A</span>
                        <div class="step-heading-text">
                            <h4>Preis konfigurieren</h4>
                            <p>Wähle Modell, Dauer und Tage, um deinen Preis zu berechnen.</p>
                        </div>
                    </div>
                    <?php endif; ?>

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

                    <div class="pricing-field" id="weekday-field-model-a">
                        <label>Wochentage auswählen:</label>
                        
                        <!-- Model A: Specific weekday checkboxes -->
                        <div class="weekday-selector model-a-selector" id="weekdays-model-a">
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
                        
                        <!-- Model B: Number of days selector -->
                        <div class="model-b-selector" id="weekdays-model-b" style="display: none;">
                            <select id="model-b-days-select" class="model-b-days-select">
                                <option value="">Anzahl Tage wählen...</option>
                                <option value="1">1 Tag pro Woche</option>
                                <option value="2">2 Tage pro Woche</option>
                                <option value="3">3 Tage pro Woche</option>
                                <option value="4">4 Tage pro Woche</option>
                            </select>
                        </div>
                        
                        <span class="help-text model-a-help">Wähle die Tage aus, an denen du die Betreuung nutzen möchtest</span>
                        <span class="help-text model-b-help" style="display: none;">Wähle die Anzahl der Tage pro Woche</span>
                        <span class="field-error" id="error-weekdays"></span>
                    </div>

                    <div class="pricing-field" id="days-field-model-b" style="display: none;">
                        <label>Anzahl Tage pro Woche:</label>
                        <div class="radio-selector" id="days-selector">
                            <label class="radio-option">
                                <input type="radio" name="num_days" value="1" class="num-days-radio" />
                                <span class="radio-label">1 Tag</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="num_days" value="2" class="num-days-radio" />
                                <span class="radio-label">2 Tage</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="num_days" value="3" class="num-days-radio" />
                                <span class="radio-label">3 Tage</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="num_days" value="4" class="num-days-radio" />
                                <span class="radio-label">4 Tage</span>
                            </label>
                        </div>
                        <span class="help-text">Wähle aus, wie viele Tage pro Woche du die Betreuung nutzen möchtest</span>
                        <span class="field-error" id="error-num-days"></span>
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
                            <span>Monatlicher Preis (ca. <?php echo esc_html($settings['weeks_multiplier'] ?? '4.33'); ?> Wochen):</span>
                            <span id="calculated-price"><?php echo $settings['currency']; ?>0,00</span>
                        </div>
                    </div>

                    <?php if ($is_separate_steps): ?>
                    <div class="step-navigation">
                        <button type="button" class="step-button primary" id="step-next-btn">Weiter</button>
                        <a href="#" class="skip-to-contact-link" id="skip-to-contact-btn">Nur Kontaktanfrage senden</a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="form-step step-user <?php echo $is_separate_steps ? '' : 'active'; ?>">
                    <?php if ($is_separate_steps): ?>
                    <div class="step-heading">
                        <span class="step-badge">Schritt B</span>
                        <div class="step-heading-text">
                            <h4>Kontaktdaten</h4>
                            <p>Teile uns mit, wie wir dich erreichen dürfen.</p>
                        </div>
                    </div>

                    <div class="selection-recap">
                        <div class="selection-row">
                            <span>Modell</span>
                            <strong id="recap-model">-</strong>
                        </div>
                        <div class="selection-row">
                            <span>Betreuungsdauer</span>
                            <strong id="recap-duration">-</strong>
                        </div>
                        <div class="selection-row">
                            <span>Gewählte Tage</span>
                            <strong id="recap-days">Noch keine Auswahl</strong>
                        </div>
                        <div class="selection-row">
                            <span>Monatlicher Preis</span>
                            <strong id="recap-price"><?php echo $settings['currency']; ?>0,00</strong>
                        </div>
                    </div>
                    <?php endif; ?>

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
                        <input type="tel" id="customer-phone" name="customer_phone" pattern="[-0-9()+ ]*" placeholder="+49 123 456789" />
                        <span class="help-text">Nur Zahlen, +, Leerzeichen, - und Klammern erlaubt</span>
                        <span class="field-error" id="error-customer-phone"></span>
                    </div>

                    <div class="pricing-field">
                        <label for="dog-name">Name deines Hundes: <span class="required">*</span></label>
                        <input type="text" id="dog-name" name="dog_name" required />
                        <span class="field-error" id="error-dog-name"></span>
                    </div>

                    <div class="pricing-field">
                        <label for="notes">Anmerkungen:</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Hier kannst du uns zusätzliche Informationen mitteilen..."></textarea>
                    </div>

                    <?php if ($recaptcha_enabled): ?>
                    <div class="pricing-field">
                        <div class="recaptcha-wrapper">
                            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($settings['recaptcha_site_key']); ?>"></div>
                        </div>
                        <span class="field-error" id="error-recaptcha"></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($settings['require_consent_checkbox'])): ?>
                    <div class="pricing-field">
                        <label class="consent-checkbox-label">
                            <input type="checkbox" id="consent-checkbox" name="consent_checkbox" required />
                            <span class="consent-checkbox-text"><?php echo wp_kses_post($settings['consent_checkbox_label']); ?></span>
                        </label>
                        <span class="field-error" id="error-consent-checkbox"></span>
                    </div>
                    <?php endif; ?>

                    <div class="step-navigation <?php echo $is_separate_steps ? 'has-secondary' : ''; ?>">
                        <?php if ($is_separate_steps): ?>
                        <button type="button" class="step-button ghost" id="step-prev-btn">Zurück</button>
                        <?php endif; ?>
                        <button type="submit" class="submit-button">Unverbindlich anfragen</button>
                    </div>
                    <div id="form-message"></div>
                </div>
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
                padding: 16px 24px;
                background: <?php echo $primary_color; ?>;
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s ease;
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
                transition: transform 0.15s ease;
                display: flex;
                align-items: center;
            }

            .toggle-form-button.active .toggle-icon {
                transform: rotate(180deg);
            }

            .form-wrapper {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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
            form.two-step-enabled .form-step {
                display: none;
            }
            form.two-step-enabled .form-step.active {
                display: block;
            }
            .form-step + .form-step {
                margin-top: 30px;
            }
            .step-heading {
                display: flex;
                align-items: center;
                gap: 14px;
                margin-bottom: 24px;
            }
            .step-heading-text h4 {
                margin: 0;
                font-size: 22px;
                color: #2c3e50;
            }
            .step-heading-text p {
                margin: 4px 0 0;
                font-size: 14px;
                color: #7f8c9a;
            }
            .step-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 80px;
                padding: 9px 16px;
                border-radius: 999px;
                background: rgba(<?php echo $primary_rgb; ?>, 0.15);
                color: <?php echo $primary_color; ?>;
                font-weight: 600;
                font-size: 13px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
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
                transition: all 0.15s ease;
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
                transition: all 0.15s ease;
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
            .model-b-selector {
                width: 100%;
            }
            .model-b-days-select {
                width: 100%;
                padding: 14px 16px;
                border: 2px solid #e8eef3;
                border-radius: 12px;
                font-size: 16px;
                box-sizing: border-box;
                transition: all 0.3s ease;
                background: #f8fafb;
                color: #2c3e50;
                cursor: pointer;
                font-family: 'Figtree', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-weight: 500;
            }
            .model-b-days-select:focus {
                outline: none;
                border-color: <?php echo $primary_color; ?>;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(<?php echo $primary_rgb; ?>, 0.1);
            }
            .model-b-days-select option {
                padding: 10px;
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
            .selection-recap {
                border: 2px solid #e8eef3;
                border-radius: 16px;
                padding: 24px;
                background: #f8fafb;
                margin-bottom: 30px;
            }
            .selection-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                font-size: 14px;
                color: #2c3e50;
                padding: 8px 0;
            }
            .selection-row + .selection-row {
                border-top: 1px solid #e8eef3;
            }
            .selection-row strong {
                font-size: 15px;
                color: #2c3e50;
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
                padding: 16px 24px;
                background: <?php echo $primary_color; ?>;
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s ease;
                box-shadow: 0 4px 12px rgba(<?php echo $primary_rgb; ?>, 0.3);
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
            .consent-checkbox-label {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                cursor: pointer;
                margin-bottom: 0;
            }
            .consent-checkbox-label input[type="checkbox"] {
                margin: 0;
                margin-top: 3px;
                flex-shrink: 0;
                width: 18px;
                height: 18px;
                cursor: pointer;
            }
            .consent-checkbox-text {
                flex: 1;
                font-size: 14px;
                line-height: 1.5;
                color: #2c3e50;
            }
            .consent-checkbox-text a {
                color: <?php echo $primary_color; ?>;
                text-decoration: underline;
            }
            .consent-checkbox-text a:hover {
                color: <?php echo $primary_hover; ?>;
            }
            .step-navigation {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 20px;
            }
            form.single-step .step-navigation {
                display: block;
                margin-top: 30px;
            }
            form.single-step .step-navigation .submit-button {
                width: 100%;
            }
            .step-navigation.has-secondary {
                justify-content: space-between;
                align-items: center;
            }
            form.two-step-enabled .step-pricing .step-navigation {
                flex-direction: column;
                align-items: center;
            }
            form.two-step-enabled .step-pricing .step-navigation .step-button {
                width: 100%;
            }
            .step-button {
                border: none;
                border-radius: 12px;
                padding: 16px 24px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s ease;
            }
            .step-button.primary {
                background: <?php echo $primary_color; ?>;
                color: #ffffff;
                box-shadow: 0 4px 12px rgba(<?php echo $primary_rgb; ?>, 0.3);
            }
            .step-button.primary:hover {
                background: <?php echo $primary_hover; ?>;
            }
            .step-button.ghost {
                background: transparent;
                color: <?php echo $primary_color; ?>;
                border: 2px solid rgba(<?php echo $primary_rgb; ?>, 0.4);
            }
            .step-button.ghost:hover {
                color: <?php echo $primary_hover; ?>;
                border-color: <?php echo $primary_hover; ?>;
            }
            form.two-step-enabled .step-navigation .submit-button {
                width: auto;
                min-width: 220px;
                flex: 1;
                height: 52px;
            }
            form.two-step-enabled .step-navigation .step-button {
                height: 52px;
            }
            .skip-to-contact-link {
                display: inline-block;
                margin-top: 12px;
                color: <?php echo $primary_color; ?>;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                transition: color 0.15s ease;
                cursor: pointer;
            }
            .skip-to-contact-link:hover {
                color: <?php echo $primary_hover; ?>;
                text-decoration: underline;
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

            @media (max-width: 768px) {
                .toggle-form-button {
                    padding: 16px 24px;
                    font-size: 16px;
                    border-radius: 12px 12px 0 0;
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
                form.two-step-enabled .step-navigation {
                    flex-direction: column;
                    align-items: stretch;
                }
                form.two-step-enabled .step-navigation .submit-button,
                form.two-step-enabled .step-navigation .step-button {
                    width: 100%;
                    min-width: 0;
                }
            }
        </style>

        <script>
            (function() {
                const prices = <?php echo wp_json_encode($settings); ?>;
                const form = document.getElementById('pricing-form');
                const formWrapper = document.getElementById('form-wrapper');
                const priceDisplay = document.getElementById('calculated-price');
                const pricePerDayDisplay = document.getElementById('price-per-day');
                const daysDisplay = document.getElementById('days-display');
                const checkboxes = document.querySelectorAll('.weekday-checkbox');
                const modelASelector = document.getElementById('weekdays-model-a');
                const modelBSelector = document.getElementById('weekdays-model-b');
                const modelBDaysSelect = document.getElementById('model-b-days-select');
                const modelAHelp = document.querySelector('.model-a-help');
                const modelBHelp = document.querySelector('.model-b-help');
                const radioButtons = document.querySelectorAll('.radio-input');
                const submitButton = form.querySelector('.submit-button');
                const formMessage = document.getElementById('form-message');
                const recaptchaEnabled = <?php echo $recaptcha_enabled ? 'true' : 'false'; ?>;
                const isAccordion = <?php echo $is_accordion ? 'true' : 'false'; ?>;
                const stepMode = '<?php echo esc_js($form_step_mode); ?>';
                const isTwoStep = stepMode === 'separate';
                const pricingStep = document.querySelector('.form-step.step-pricing');
                const userStep = document.querySelector('.form-step.step-user');
                const nextStepBtn = document.getElementById('step-next-btn');
                const prevStepBtn = document.getElementById('step-prev-btn');
                const skipToContactBtn = document.getElementById('skip-to-contact-btn');
                const recapModel = document.getElementById('recap-model');
                const recapDuration = document.getElementById('recap-duration');
                const recapDays = document.getElementById('recap-days');
                const recapPrice = document.getElementById('recap-price');
                const consentCheckbox = document.getElementById('consent-checkbox');
                const weeksMultiplier = parseFloat(prices.weeks_multiplier) || 4.33;
                const roundPrices = prices.round_prices == 1;
                const requireConsent = prices.require_consent_checkbox == 1;

                const weekdayNames = {
                    'monday': 'Montag',
                    'tuesday': 'Dienstag',
                    'wednesday': 'Mittwoch',
                    'thursday': 'Donnerstag',
                    'friday': 'Freitag'
                };

                // Handle consent checkbox and submit button state
                if (consentCheckbox && requireConsent) {
                    submitButton.disabled = !consentCheckbox.checked;
                    consentCheckbox.addEventListener('change', function() {
                        submitButton.disabled = !this.checked;
                    });
                }

                // Show/hide appropriate field based on model selection
                function updateModelFields() {
                    const selectedModel = document.querySelector('input[name="subscription_model"]:checked').value;
                    const weekdayField = document.getElementById('weekday-field-model-a');
                    const daysField = document.getElementById('days-field-model-b');
                    
                    if (selectedModel === 'b') {
                        // Model B: show number of days selector, hide weekday selector
                        if (weekdayField) weekdayField.style.display = 'none';
                        if (daysField) daysField.style.display = 'block';
                        // Uncheck all weekday checkboxes
                        checkboxes.forEach(function(checkbox) {
                            checkbox.checked = false;
                        });
                    } else {
                        // Model A: show weekday selector, hide number of days selector
                        if (weekdayField) weekdayField.style.display = 'block';
                        if (daysField) daysField.style.display = 'none';
                        // Uncheck number of days selection
                        const numDaysRadios = document.querySelectorAll('.num-days-radio');
                        numDaysRadios.forEach(function(radio) {
                            radio.checked = false;
                        });
                    }
                    calculatePrice();
                }

                // Listen for model changes
                document.querySelectorAll('input[name="subscription_model"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        updateModelFields();
                    });
                });

                // Listen for Model B dropdown changes
                if (modelBDaysSelect) {
                    modelBDaysSelect.addEventListener('change', function() {
                        const errorSpan = document.getElementById('error-weekdays');
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                        calculatePrice();
                    });
                }

                // Listen for Model A checkbox changes
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const errorSpan = document.getElementById('error-weekdays');
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                        calculatePrice();
                    });
                });

                // Listen for number of days radio changes (Model B)
                const numDaysRadios = document.querySelectorAll('.num-days-radio');
                numDaysRadios.forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        // Clear error if present
                        const errorSpan = document.getElementById('error-num-days');
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                        calculatePrice();
                    });
                });

                // Initialize model fields
                updateModelFields();

                if (isAccordion) {
                    const toggleButton = document.getElementById('toggle-form-btn');
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

                            if (recaptchaEnabled && !recaptchaLoaded && typeof grecaptcha !== 'undefined') {
                                setTimeout(function() {
                                    try {
                                        grecaptcha.render(document.querySelector('.g-recaptcha'));
                                        recaptchaLoaded = true;
                                    } catch(e) {}
                                }, 100);
                            }
                        }
                    });
                }

                if (isTwoStep && pricingStep && userStep) {
                    const focusFormTop = function() {
                        if (formWrapper) {
                            formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    };

                    const showStep = function(step) {
                        if (step === 'pricing') {
                            pricingStep.classList.add('active');
                            userStep.classList.remove('active');
                        } else {
                            pricingStep.classList.remove('active');
                            userStep.classList.add('active');
                        }
                        focusFormTop();
                    };

                    if (nextStepBtn) {
                        nextStepBtn.addEventListener('click', function() {
                            if (validateStepA()) {
                                showStep('user');
                            }
                        });
                    }

                    if (prevStepBtn) {
                        prevStepBtn.addEventListener('click', function() {
                            showStep('pricing');
                        });
                    }

                    if (skipToContactBtn) {
                        skipToContactBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const numDays = getSelectedDaysCount();
                            if (numDays === 0) {
                                if (recapModel) recapModel.textContent = '-';
                                if (recapDuration) recapDuration.textContent = '-';
                                if (recapDays) recapDays.textContent = 'Keine Auswahl';
                                if (recapPrice) recapPrice.textContent = prices.currency + '0,00';
                            }
                            showStep('user');
                        });
                    }
                }

                const phoneInput = document.getElementById('customer-phone');
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/[^0-9()+\s-]/g, '');
                    e.target.value = value;
                });

                function clearErrors() {
                    document.querySelectorAll('.field-error').forEach(function(error) {
                        error.classList.remove('show');
                        error.textContent = '';
                    });
                    document.querySelectorAll('.error').forEach(function(field) {
                        field.classList.remove('error');
                    });
                }

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

                if (consentCheckbox) {
                    consentCheckbox.addEventListener('change', function() {
                        const errorSpan = document.getElementById('error-consent-checkbox');
                        if (errorSpan) {
                            errorSpan.classList.remove('show');
                        }
                    });
                }

                function validateStepA() {
                    const model = document.querySelector('input[name="subscription_model"]:checked').value;
                    
                    if (model === 'a') {
                        // Model A: validate weekdays
                        const checkedDays = Array.from(checkboxes).filter(cb => cb.checked);
                        if (checkedDays.length === 0) {
                            showError('weekdays', 'Bitte wähle mindestens einen Wochentag aus.');
                            if (isTwoStep && pricingStep && userStep) {
                                pricingStep.classList.add('active');
                                userStep.classList.remove('active');
                                if (formWrapper) {
                                    formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            }
                            return false;
                        }
                    } else {
                        // Model B: validate number of days
                        const selectedNumDaysRadio = document.querySelector('input[name="num_days"]:checked');
                        if (!selectedNumDaysRadio) {
                            showError('num-days', 'Bitte wähle die Anzahl der Tage pro Woche aus.');
                            if (isTwoStep && pricingStep && userStep) {
                                pricingStep.classList.add('active');
                                userStep.classList.remove('active');
                                if (formWrapper) {
                                    formWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            }
                            return false;
                        }
                    }
                    return true;
                }

                function calculatePrice() {
                    const model = document.querySelector('input[name="subscription_model"]:checked').value;
                    const duration = document.querySelector('input[name="day_duration"]:checked').value;
                    const modelName = model === 'a' ? prices.model_a_name : prices.model_b_name;
                    const durationLabel = duration === 'half' ? 'Halbtags' : 'Ganztags';
                    
                    let numDays = 0;
                    let daysDisplayText = 'Noch keine Auswahl';
                    
                    if (model === 'a') {
                        // Model A: use weekday checkboxes
                        const checkedDays = Array.from(checkboxes).filter(cb => cb.checked);
                        numDays = checkedDays.length;
                        const weekdayLabels = checkedDays.map(cb => weekdayNames[cb.value]);
                        daysDisplayText = weekdayLabels.length ? weekdayLabels.join(', ') : 'Noch keine Auswahl';
                    } else {
                        // Model B: use number of days radio
                        const selectedNumDaysRadio = document.querySelector('input[name="num_days"]:checked');
                        if (selectedNumDaysRadio) {
                            numDays = parseInt(selectedNumDaysRadio.value);
                            daysDisplayText = numDays + ' Tag' + (numDays > 1 ? 'e' : '') + ' pro Woche';
                        }
                    }

                    if (recapModel) {
                        recapModel.textContent = modelName;
                    }
                    if (recapDuration) {
                        recapDuration.textContent = durationLabel;
                    }
                    if (recapDays) {
                        recapDays.textContent = daysDisplayText;
                    }

                    if (numDays === 0) {
                        const zeroText = prices.currency + '0,00';
                        pricePerDayDisplay.textContent = zeroText;
                        daysDisplay.textContent = '0';
                        priceDisplay.textContent = zeroText;
                        if (recapPrice) {
                            recapPrice.textContent = zeroText;
                        }
                        return;
                    }

                    const key = 'model_' + model + '_' + duration + '_' + numDays;
                    const pricePerDay = parseFloat(prices[key]) || 0;
                    let monthlyPrice = pricePerDay * numDays * weeksMultiplier;
                    
                    if (roundPrices) {
                        monthlyPrice = Math.ceil(monthlyPrice);
                    }

                    const pricePerDayText = prices.currency + pricePerDay.toFixed(2).replace('.', ',');
                    const monthlyPriceText = prices.currency + monthlyPrice.toFixed(2).replace('.', ',');

                    pricePerDayDisplay.textContent = pricePerDayText;
                    daysDisplay.textContent = numDays;
                    priceDisplay.textContent = monthlyPriceText;
                    if (recapPrice) {
                        recapPrice.textContent = monthlyPriceText;
                    }
                }

                radioButtons.forEach(rb => rb.addEventListener('change', calculatePrice));

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    clearErrors();
                    let hasErrors = false;

                    const customerName = document.getElementById('customer-name').value.trim();
                    if (!customerName) {
                        showError('customer-name', 'Bitte gib deinen Namen ein.');
                        hasErrors = true;
                    }

                    const customerEmail = document.getElementById('customer-email').value.trim();
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!customerEmail) {
                        showError('customer-email', 'Bitte gib deine E-Mail-Adresse ein.');
                        hasErrors = true;
                    } else if (!emailPattern.test(customerEmail)) {
                        showError('customer-email', 'Bitte gib eine gültige E-Mail-Adresse ein.');
                        hasErrors = true;
                    }

                    const customerPhone = document.getElementById('customer-phone').value.trim();
                    const phonePattern = /^[-0-9()+ ]+$/;
                    if (customerPhone && !phonePattern.test(customerPhone)) {
                        showError('customer-phone', 'Bitte gib eine gültige Telefonnummer ein (nur Zahlen, +, -, Leerzeichen und Klammern).');
                        hasErrors = true;
                    }

                    const dogName = document.getElementById('dog-name').value.trim();
                    if (!dogName) {
                        showError('dog-name', 'Bitte gib den Namen deines Hundes ein.');
                        hasErrors = true;
                    }

                    let model = document.querySelector('input[name="subscription_model"]:checked').value;
                    let checkedDays = [];
                    let numDays = 0;
                    
                    if (model === 'a') {
                        // Model A: get selected weekdays
                        checkedDays = Array.from(checkboxes)
                            .filter(cb => cb.checked)
                            .map(cb => weekdayNames[cb.value]);
                        numDays = checkedDays.length;
                    } else {
                        // Model B: get number of days
                        const selectedNumDaysRadio = document.querySelector('input[name="num_days"]:checked');
                        if (selectedNumDaysRadio) {
                            numDays = parseInt(selectedNumDaysRadio.value);
                            checkedDays = [numDays + ' Tag' + (numDays > 1 ? 'e' : '') + ' pro Woche'];
                        }
                    }

                    // Only validate if we're in single-step mode or if we came from step 1 normally
                    // If user skipped to step 2, selection is optional
                    const isSkipped = numDays === 0 && isTwoStep;
                    if (numDays === 0 && !isSkipped) {
                        if (model === 'a') {
                            showError('weekdays', 'Bitte wähle mindestens einen Wochentag aus.');
                        } else {
                            showError('num-days', 'Bitte wähle die Anzahl der Tage pro Woche aus.');
                        }
                        hasErrors = true;
                    }

                    if (recaptchaEnabled && typeof grecaptcha !== 'undefined') {
                        const recaptchaResponse = grecaptcha.getResponse();
                        if (!recaptchaResponse) {
                            showError('recaptcha', 'Bitte bestätige, dass du kein Roboter bist.');
                            hasErrors = true;
                        }
                    }

                    if (requireConsent && consentCheckbox && !consentCheckbox.checked) {
                        showError('consent-checkbox', 'Bitte bestätige die Einverständniserklärung.');
                        hasErrors = true;
                    }

                    if (hasErrors) {
                        return;
                    }

                    // Handle skipped pricing step
                    let duration, modelName, monthlyPrice, pricePerDay;
                    if (numDays === 0 && isTwoStep) {
                        // User skipped pricing step - use default/empty values
                        model = 'a';
                        duration = 'half';
                        modelName = 'Nicht ausgewählt';
                        pricePerDay = 0;
                        monthlyPrice = 0;
                        selectedDaysForSubmission = 'Keine Auswahl';
                    } else {
                        // model is already set from line 1262
                        duration = document.querySelector('input[name="day_duration"]:checked').value;
                        const key = 'model_' + model + '_' + duration + '_' + numDays;
                        pricePerDay = parseFloat(prices[key]) || 0;
                        monthlyPrice = pricePerDay * numDays * weeksMultiplier;
                        
                        if (roundPrices) {
                            monthlyPrice = Math.ceil(monthlyPrice);
                        }

                        modelName = model === 'a' ? prices.model_a_name : prices.model_b_name;
                    }

                    const formData = new FormData();
                    formData.append('action', 'save_pricing_configuration');
                    formData.append('customer_name', document.getElementById('customer-name').value);
                    formData.append('customer_email', document.getElementById('customer-email').value);
                    formData.append('customer_phone', document.getElementById('customer-phone').value);
                    formData.append('dog_name', document.getElementById('dog-name').value);
                    formData.append('subscription_model', modelName);
                    formData.append('duration', duration);
                    formData.append('selected_days', checkedDays.length > 0 ? checkedDays.join(', ') : 'Keine Auswahl');
                    formData.append('num_days', numDays);
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
                            updateModelFields();
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
