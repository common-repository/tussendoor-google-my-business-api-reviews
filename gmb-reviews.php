<?php
/**
 * Plugin Name: Tussendoor - Google My Business API Reviews
 * Text Domain: gmb-reviews
 * Domain Path: /assets/languages
 * Plugin URI: https://tussendoor.nl/wordpress-plugins/
 * Description: Add a star rating to your website in the organic search results in Google and show your reviews with a widget or shortcode!
 * Version: 1.1.2
 * Author: Tussendoor B.V.
 * Author URI: https://tussendoor.nl/
 * Requires at least: 4.7
 * Tested up to: 6.4.2
 * Requires at least PHP 7.4
 */
require __DIR__.'/vendor/autoload.php';

add_action('plugins_loaded', 'Tussendoor\GmbReviews\Plugin::boot', 99);