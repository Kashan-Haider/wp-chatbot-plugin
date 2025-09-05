<?php
/**
 * Plugin Name: FAQ Chatbot
 * Plugin URI: https://example.com/faq-chatbot
 * Description: A simple, production-ready WordPress plugin that implements a reusable chatbot-style FAQ widget for service sites. No admin pages, no settings - just drop and use.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: faq-chatbot
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('FAQ_CHATBOT_VERSION', '1.0.0');
define('FAQ_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FAQ_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Email recipient - change this to your desired email address
define('FAQ_CHATBOT_RECIPIENT_EMAIL', 'herman@homecareassistanceofjefferson.com');

// SMTP Configuration for Hostinger
define('FAQ_CHATBOT_SMTP_HOST', 'smtp.hostinger.com');
define('FAQ_CHATBOT_SMTP_PORT', 465);
define('FAQ_CHATBOT_SMTP_SECURE', 'ssl');
define('FAQ_CHATBOT_SMTP_USERNAME', 'noreply@homecareassistanceofjefferson.com');
define('FAQ_CHATBOT_SMTP_PASSWORD', 'r|Xv7Q0QV7');

/**
 * Main FAQ Chatbot Class
 */
class FAQ_Chatbot {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_sticky_chatbot'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('faq-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Register shortcode
        add_shortcode('wp_chatbot', array($this, 'render_chatbot_shortcode'));
        
        // Register Gutenberg block (optional)
        add_action('init', array($this, 'register_gutenberg_block'));
        
        // Configure WordPress mail settings
        add_action('phpmailer_init', array($this, 'configure_mail'));
        
        // Add test email hook
        add_action('init', array($this, 'test_email'));
    }
    
    /**
     * Configure mail settings for Hostinger SMTP
     */
    public function configure_mail($phpmailer) {
        // Only configure SMTP for our plugin emails
        if (!$this->is_our_email()) {
            return;
        }
        
        try {
            // Configure SMTP settings
            $phpmailer->isSMTP();
            $phpmailer->Host = FAQ_CHATBOT_SMTP_HOST;
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = FAQ_CHATBOT_SMTP_PORT;
            $phpmailer->SMTPSecure = FAQ_CHATBOT_SMTP_SECURE;
            $phpmailer->Username = FAQ_CHATBOT_SMTP_USERNAME;
            $phpmailer->Password = FAQ_CHATBOT_SMTP_PASSWORD;
            
            // Set From address
            $phpmailer->setFrom(FAQ_CHATBOT_SMTP_USERNAME, get_bloginfo('name'));
            
            // Additional SMTP options for better delivery
            $phpmailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Enable debugging
            $phpmailer->SMTPDebug = 2;
            $phpmailer->Debugoutput = function($str, $level) {
                error_log("FAQ Chatbot SMTP Debug: $str");
            };
            
            error_log('FAQ Chatbot: SMTP configured successfully');
            
        } catch (Exception $e) {
            error_log('FAQ Chatbot: SMTP configuration error: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if this is our plugin's email
     */
    private function is_our_email() {
        $backtrace = debug_backtrace();
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] === 'FAQ_Chatbot') {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Test email function - can be called via URL parameter for debugging
     */
    public function test_email() {
        if (isset($_GET['test_faq_email']) && current_user_can('administrator')) {
            error_log('FAQ Chatbot: Starting test email function');
            
            // First test SMTP connection
            $smtp_test = $this->test_smtp_connection();
            if (!$smtp_test['success']) {
                wp_die('SMTP Connection Failed: ' . $smtp_test['error'] . '<br><br>Check your SMTP credentials and server settings.');
            }
            
            // Add action to capture wp_mail errors
            add_action('wp_mail_failed', array($this, 'log_mail_error'));
            
            $result = wp_mail(
                FAQ_CHATBOT_RECIPIENT_EMAIL,
                'FAQ Chatbot Test Email - ' . date('Y-m-d H:i:s'),
                '<h3>Test Email from FAQ Chatbot Plugin</h3><p>This is a test email sent at ' . date('Y-m-d H:i:s') . '</p><p>If you receive this, your SMTP configuration is working correctly.</p>',
                array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . FAQ_CHATBOT_SMTP_USERNAME . '>'
                )
            );
            
            remove_action('wp_mail_failed', array($this, 'log_mail_error'));
            
            if ($result) {
                wp_die('✅ Test email sent successfully to ' . FAQ_CHATBOT_RECIPIENT_EMAIL . '<br><br>Check your inbox (and spam folder) for the test message.');
            } else {
                wp_die('❌ Test email failed to send. Check WordPress error logs for detailed information.');
            }
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Skip admin pages and login pages
        if (is_admin() || is_login()) {
            return;
        }
        
        wp_enqueue_style(
            'faq-chatbot-styles',
            FAQ_CHATBOT_PLUGIN_URL . 'assets/css/styles.css',
            array(),
            FAQ_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'faq-chatbot-script',
            FAQ_CHATBOT_PLUGIN_URL . 'assets/js/chatbot.js',
            array(),
            FAQ_CHATBOT_VERSION,
            true
        );
        
        // Localize script with REST API data
        wp_localize_script('faq-chatbot-script', 'faqChatbot', array(
            'restUrl' => rest_url('faq-chatbot/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'data' => $this->get_chatbot_data(),
            'strings' => array(
                'loading' => __('Loading...', 'faq-chatbot'),
                'error' => __('An error occurred. Please try again.', 'faq-chatbot'),
                'success' => __('Thank you! Your message has been sent successfully.', 'faq-chatbot'),
                'validation_name' => __('Please enter your name.', 'faq-chatbot'),
                'validation_email' => __('Please enter a valid email address.', 'faq-chatbot'),
                'validation_message' => __('Please enter your message.', 'faq-chatbot'),
            )
        ));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('faq-chatbot/v1', '/contact', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_contact_form'),
            'permission_callback' => array($this, 'verify_nonce'),
        ));
    }
    
    /**
     * Verify nonce for REST API
     */
    public function verify_nonce($request) {
        return wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
    }
    
    /**
     * Handle contact form submission
     */
    public function handle_contact_form($request) {
        // Rate limiting - prevent rapid submissions
        $ip = $_SERVER['REMOTE_ADDR'];
        $rate_limit_key = 'faq_chatbot_rate_limit_' . md5($ip);
        
        if (get_transient($rate_limit_key)) {
            return new WP_Error('rate_limited', __('Please wait before submitting another message.', 'faq-chatbot'), array('status' => 429));
        }
        
        // Set rate limit transient (30 seconds)
        set_transient($rate_limit_key, true, 30);
        
        // Get and sanitize form data
        $name = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        $phone = sanitize_text_field($request->get_param('phone'));
        $service = sanitize_text_field($request->get_param('service'));
        $message = sanitize_textarea_field($request->get_param('message'));
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($message)) {
            return new WP_Error('missing_fields', __('Please fill in all required fields.', 'faq-chatbot'), array('status' => 400));
        }
        
        if (!is_email($email)) {
            return new WP_Error('invalid_email', __('Please enter a valid email address.', 'faq-chatbot'), array('status' => 400));
        }
        
        // Prepare email
        $to = FAQ_CHATBOT_RECIPIENT_EMAIL;
        $subject = sprintf(__('New FAQ Chatbot Contact: %s', 'faq-chatbot'), $service);
        
        $email_message = sprintf(
            __("New contact form submission from FAQ Chatbot:\n\nName: %s\nEmail: %s\nPhone: %s\nService: %s\nMessage:\n%s\n\nSubmitted from: %s", 'faq-chatbot'),
            $name,
            $email,
            $phone ?: __('Not provided', 'faq-chatbot'),
            $service,
            $message,
            home_url()
        );
        
        // Set proper headers for wp_mail
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . get_bloginfo('name') . ' <' . FAQ_CHATBOT_SMTP_USERNAME . '>';
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
        
        // Create HTML email message
        $html_message = "
        <h3>New FAQ Chatbot Contact Form Submission</h3>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Phone:</strong> " . ($phone ?: 'Not provided') . "</p>
        <p><strong>Service:</strong> {$service}</p>
        <p><strong>Message:</strong></p>
        <p>{$message}</p>
        <hr>
        <p><small>Submitted from: " . home_url() . "</small></p>
        ";
        
        // Log email attempt for debugging
        error_log('FAQ Chatbot: Attempting to send email to ' . $to);
        error_log('FAQ Chatbot: Subject: ' . $subject);
        error_log('FAQ Chatbot: SMTP Host: ' . FAQ_CHATBOT_SMTP_HOST);
        error_log('FAQ Chatbot: SMTP Port: ' . FAQ_CHATBOT_SMTP_PORT);
        error_log('FAQ Chatbot: SMTP Username: ' . FAQ_CHATBOT_SMTP_USERNAME);
        
        // Add action to capture wp_mail errors
        add_action('wp_mail_failed', array($this, 'log_mail_error'));
        
        // Send email using wp_mail
        $sent = wp_mail($to, $subject, $html_message, $headers);
        
        // Remove the error handler
        remove_action('wp_mail_failed', array($this, 'log_mail_error'));
        
        // Log result and return response
        if ($sent) {
            error_log('FAQ Chatbot: wp_mail returned true - checking actual delivery');
            
            // Test SMTP connection directly
            $smtp_test = $this->test_smtp_connection();
            if ($smtp_test['success']) {
                error_log('FAQ Chatbot: SMTP connection test successful');
                return array(
                    'success' => true,
                    'message' => __('Thank you! Your message has been sent successfully.', 'faq-chatbot')
                );
            } else {
                error_log('FAQ Chatbot: SMTP connection test failed: ' . $smtp_test['error']);
                return new WP_Error('smtp_failed', __('Email configuration error. Please contact administrator.', 'faq-chatbot'), array('status' => 500));
            }
        } else {
            error_log('FAQ Chatbot: wp_mail returned false - email failed');
            return new WP_Error('email_failed', __('Failed to send message. Please try again later.', 'faq-chatbot'), array('status' => 500));
        }
    }
    
    /**
     * Log wp_mail errors
     */
    public function log_mail_error($wp_error) {
        error_log('FAQ Chatbot: wp_mail error: ' . $wp_error->get_error_message());
    }
    
    /**
     * Test SMTP connection
     */
    private function test_smtp_connection() {
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        }
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = FAQ_CHATBOT_SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = FAQ_CHATBOT_SMTP_USERNAME;
            $mail->Password = FAQ_CHATBOT_SMTP_PASSWORD;
            $mail->SMTPSecure = FAQ_CHATBOT_SMTP_SECURE;
            $mail->Port = FAQ_CHATBOT_SMTP_PORT;
            
            // Test connection
            $mail->smtpConnect();
            $mail->smtpClose();
            
            return array('success' => true);
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }
    
    /**
     * Get chatbot data
     */
    private function get_chatbot_data() {
        return include FAQ_CHATBOT_PLUGIN_DIR . 'inc/data.php';
    }
    
    /**
     * Render sticky chatbot widget in footer
     */
    public function render_sticky_chatbot() {
        // Skip admin pages and login pages
        if (is_admin() || is_login()) {
            return;
        }
        ?>
        <!-- Sticky Chatbot Button -->
        <div id="faq-chatbot-button" class="faq-chatbot-button" aria-label="<?php esc_attr_e('Open Chat Support', 'faq-chatbot'); ?>">
            <span class="faq-chatbot-button__icon">💬</span>
            <span class="faq-chatbot-button__text"><?php _e('Chat', 'faq-chatbot'); ?></span>
        </div>
        
        <!-- Sticky Chatbot Widget -->
        <div class="faq-chatbot faq-chatbot--sticky" id="faq-chatbot-widget" role="region" aria-label="<?php esc_attr_e('FAQ Chatbot', 'faq-chatbot'); ?>" style="display: none;">
            <div class="faq-chatbot__header">
                <h3><?php _e('How can we help you?', 'faq-chatbot'); ?></h3>
            </div>
            <div class="faq-chatbot__content" aria-live="polite" id="chat-messages">
                <!-- Messages will be added here -->
            </div>
            <div class="faq-chatbot__input-area">
                <div class="faq-chatbot__quick-actions">
                    <button type="button" class="faq-chatbot__restart-btn" id="restart-chat"><?php _e('🔄 Start Over', 'faq-chatbot'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render chatbot shortcode
     */
    public function render_chatbot_shortcode($atts) {
        $atts = shortcode_atts(array(), $atts, 'wp_chatbot');
        
        ob_start();
        ?>
        <div class="faq-chatbot" id="faq-chatbot-widget-shortcode" role="region" aria-label="<?php esc_attr_e('FAQ Chatbot', 'faq-chatbot'); ?>">
            <div class="faq-chatbot__header">
                <h3><?php _e('How can we help you?', 'faq-chatbot'); ?></h3>
            </div>
            <div class="faq-chatbot__content" aria-live="polite" id="chat-messages-shortcode">
                <!-- Messages will be added here -->
            </div>
            <div class="faq-chatbot__input-area">
                <div class="faq-chatbot__quick-actions">
                    <button type="button" class="faq-chatbot__restart-btn" id="restart-chat-shortcode"><?php _e('🔄 Start Over', 'faq-chatbot'); ?></button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Register Gutenberg block (optional)
     */
    public function register_gutenberg_block() {
        if (function_exists('register_block_type')) {
            register_block_type('faq-chatbot/widget', array(
                'render_callback' => array($this, 'render_chatbot_shortcode'),
            ));
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'faq_chatbot_rate_limit_%'");
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new FAQ_Chatbot();
