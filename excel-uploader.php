<?php

if ( ! defined('ABSPATH')) {
    exit;
}
/**
 * WordPress - Excel Uploader
 *
 * Plugin Name:         Excel Uploader
 * Plugin URI:          https://wordpress.org/plugins/excel-uploader
 * Description:         Upload and parse Excel files into multi-dimensional PHP arrays
 * Version:             1.1.1
 * Requires at least:   5.2
 * Requires PHP:        8.2
 * Contributor:         Contributor according to the WordPress.org
 * Author:              Plugin_Author
 * Author URI:          https://suitepress.org/excel-uploader
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
