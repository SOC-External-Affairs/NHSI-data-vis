<?php
namespace ExcelUploader;

use ExcelUploader\Hooks\Handlers\AdminMenuHandlers;
use ExcelUploader\Hooks\Handlers\RestApiHandlers;
use ExcelUploader\Controllers\ExcelUploadController;
use ExcelUploader\Controllers\AttendeeCrudController;
use ExcelUploader\Services\AttendeeDatabase;

class App {

    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    public function init() {

        define( 'EXCEL_UPLOADER', 'plugins-migrator' );
        define( 'EXCEL_UPLOADER_PATH', untrailingslashit( plugin_dir_path( __DIR__ ) ) );
        define( 'EXCEL_UPLOADER_URL', untrailingslashit( plugin_dir_url( __DIR__ ) ) );
        define( 'EXCEL_UPLOADER_BUILD_PATH', EXCEL_UPLOADER_PATH . '/public/assets' );
        define( 'EXCEL_UPLOADER_BUILD_URL', EXCEL_UPLOADER_URL . '/public/assets' );
        define( 'EXCEL_UPLOADER_VERSION', '1.1.1' );


       new AdminMenuHandlers();
       new RestApiHandlers();
       new ExcelUploadController();
       new AttendeeCrudController();
       new \ExcelUploader\Controllers\ReportsController();
       new AttendeeDatabase();
    }
}
