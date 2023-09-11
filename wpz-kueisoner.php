<?php

/**
 * Plugin Name: WPZ Kueisoner
 * Plugin URI: https://github.com/adityathok/wpz-kueisoner
 * Description: Plugin Kueisoner for Theme WPZaro
 * Version: 0.0.1
 * Author: Aditya Thok
 * Author URI: https://github.com/adityathok
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define constants
 *
 * @since 2.0.0
 */
if (!defined('WPZ_KUEIS_VERSION'))        define('WPZ_KUEIS_VERSION', '0.0.1');
if (!defined('WPZ_KUEIS_PLUGIN'))         define('WPZ_KUEIS_PLUGIN', trim(dirname(plugin_basename(__FILE__)), '/'));
if (!defined('WPZ_KUEIS_PLUGIN_DIR'))     define('WPZ_KUEIS_PLUGIN_DIR', plugin_dir_path(__FILE__));
if (!defined('WPZ_KUEIS_PLUGIN_URL'))     define('WPZ_KUEIS_PLUGIN_URL', plugin_dir_url(__FILE__));


// Load everything
$includes = [
    'inc/admin.php',
    'inc/kueisoner.php',
    'inc/dimensi.php',
    'inc/faktor.php',
    'inc/indikator.php',
    'inc/form.php',
    'inc/hasil.php',
    'inc/shortcode.php',
];
foreach ($includes as $include) {
    require_once(WPZ_KUEIS_PLUGIN_DIR . $include);
}
