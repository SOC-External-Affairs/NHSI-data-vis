<?php

if ( ! defined('ABSPATH')) {
    exit;
}

// Ensure WordPress functions are available
if ( ! function_exists('add_action') ) {
    exit;
}

/**
 * WordPress - Data Visualizer
 *
 * Plugin Name:         Data Visualizer
 * Plugin URI:          https://wordpress.org/plugins/data-visualizer
 * Description:         Securely import and visualize student attendee data from Excel files
 * Version:             1.1.1
 * Requires at least:   5.2
 * Requires PHP:        8.2
 * Contributor:         Contributor according to the WordPress.org
 * Author:              Plugin_Author
 * Author URI:          https://suitepress.org/data-visualizer
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         excel-uploader
 * Domain Path:         /languages
 */
require_once __DIR__ . '/vendor/autoload.php';

use ExcelUploader\App;

if ( class_exists( 'ExcelUploader\App' ) ) {
    $app = new App();
}
