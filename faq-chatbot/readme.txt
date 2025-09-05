=== FAQ Chatbot ===
Contributors: yourname
Tags: faq, chatbot, customer service, contact form, shortcode
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple, production-ready WordPress plugin that implements a reusable chatbot-style FAQ widget for service sites.

== Description ==

FAQ Chatbot is a lightweight WordPress plugin that provides an interactive FAQ widget for service-based websites. The plugin is designed to be drop-and-use with no configuration required - simply install, activate, and add the shortcode to any page or post.

**Key Features:**

* **Zero Configuration**: No admin pages or settings - works immediately after activation
* **Hardcoded Content**: All FAQ data is stored in plugin files for easy customization
* **Interactive Flow**: Users navigate through services → questions → answers → contact form
* **Contact Form Integration**: Built-in contact form with email notifications
* **Secure**: Nonce verification, rate limiting, and proper data sanitization
* **Accessible**: Keyboard navigation, ARIA labels, and screen reader support
* **Responsive**: Mobile-friendly design that works on all devices
* **Lightweight**: Minimal CSS and vanilla JavaScript - no external dependencies

**Perfect for:**
* Auto repair shops
* Service businesses
* Customer support pages
* FAQ sections
* Lead generation

== Installation ==

1. Upload the `faq-chatbot` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode `[wp_chatbot]` to any page, post, or widget where you want the FAQ chatbot to appear

That's it! The chatbot will work immediately with the default automotive service data.

== Usage ==

**Adding the Chatbot:**
Use the shortcode `[wp_chatbot]` anywhere you want the FAQ widget to appear:

* In posts/pages: Add `[wp_chatbot]` in the content editor
* In widgets: Use a text widget with the shortcode
* In theme files: Use `<?php echo do_shortcode('[wp_chatbot]'); ?>`

**User Flow:**
1. Users see a list of service categories
2. Clicking a service shows related questions
3. Clicking a question displays the answer
4. After reading an answer, users can:
   - Return to services list
   - Access the contact form
5. Contact form submissions are emailed to the configured recipient

== Customization ==

**Changing the Email Recipient:**
Edit the main plugin file `faq-chatbot/faq-chatbot.php` and modify this line near the top:
```php
define('FAQ_CHATBOT_RECIPIENT_EMAIL', get_option('admin_email'));
```

Change it to your desired email address:
```php
define('FAQ_CHATBOT_RECIPIENT_EMAIL', 'your-email@example.com');
```

**Modifying FAQ Content:**
Edit the file `faq-chatbot/inc/data.php` to customize:
* Service categories
* Questions and answers
* Add or remove services

The data structure is a simple PHP array - just follow the existing format.

**Styling:**
The plugin includes minimal CSS with the `.faq-chatbot` namespace to avoid conflicts. You can override styles in your theme's CSS file.

== Technical Details ==

**Requirements:**
* WordPress 5.8 or higher
* PHP 7.4 or higher
* No external dependencies

**Security Features:**
* Nonce verification for all form submissions
* Rate limiting (30-second cooldown per IP)
* Input sanitization and validation
* Secure REST API endpoints

**Accessibility:**
* Keyboard navigation support
* ARIA labels and live regions
* Screen reader compatible
* Focus management

**Performance:**
* Scripts only load on pages with the shortcode
* Minimal CSS and JavaScript footprint
* No database queries for FAQ data

== REST API ==

The plugin registers a REST endpoint at `/wp-json/faq-chatbot/v1/contact` for form submissions. This endpoint:
* Requires a valid nonce
* Validates and sanitizes all input
* Implements rate limiting
* Sends emails via wp_mail()

== Frequently Asked Questions ==

= How do I change the FAQ content? =

Edit the file `inc/data.php` in the plugin directory. The content is stored as a PHP array - just follow the existing structure to add, modify, or remove services and questions.

= Can I change the email address for contact form submissions? =

Yes, edit the main plugin file `faq-chatbot.php` and change the `FAQ_CHATBOT_RECIPIENT_EMAIL` constant at the top of the file.

= Does this plugin create database tables? =

No, the plugin uses only WordPress core functionality and stores no data in the database. All FAQ content is hardcoded in the plugin files.

= Can I use this plugin multiple times on the same page? =

The plugin is designed for single use per page. Multiple instances may cause JavaScript conflicts.

= Is the plugin translation-ready? =

Yes, all strings are wrapped with translation functions and a POT file is included in the languages folder.

= How do I customize the styling? =

All CSS uses the `.faq-chatbot` namespace. You can override styles in your theme's CSS file or child theme.

== Screenshots ==

1. Initial services selection view
2. Questions list for a selected service
3. Answer display with action buttons
4. Contact form interface
5. Mobile responsive design

== Changelog ==

= 1.0.0 =
* Initial release
* Interactive FAQ chatbot widget
* Contact form with email notifications
* Responsive design
* Accessibility features
* Security implementations

== Upgrade Notice ==

= 1.0.0 =
Initial release of FAQ Chatbot plugin.

== Developer Notes ==

**File Structure:**
```
faq-chatbot/
├── faq-chatbot.php          # Main plugin file
├── inc/
│   └── data.php             # FAQ data structure
├── assets/
│   ├── css/
│   │   └── styles.css       # Widget styles
│   └── js/
│       └── chatbot.js       # Widget functionality
├── languages/
│   └── faq-chatbot.pot      # Translation template
└── readme.txt               # This file
```

**Hooks Available:**
Currently, the plugin doesn't provide custom hooks, but this may be added in future versions based on user feedback.

**Contributing:**
This plugin is designed to be simple and lightweight. Feature requests should align with the core philosophy of minimal configuration and hardcoded content.

== Support ==

For support, customization requests, or bug reports, please contact the plugin author or submit issues through the appropriate channels.

Remember: This plugin is designed for simplicity. If you need complex admin interfaces, dynamic content management, or database-driven FAQs, consider other solutions or custom development.
