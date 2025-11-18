<?php
namespace ExcelUploader\Hooks\Handlers;

use ExcelUploader\Controllers\ExcelUploadController;

class AdminMenuHandlers {

    public function __construct() {
        $this->init();
    }

    public function init() {
        add_action('admin_menu', [$this, 'plugins_names_add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'conditionally_enqueue_assets']);
    }

    public function conditionally_enqueue_assets($hook) {
        // Only load assets on our plugin page
        if (
            strpos($hook, 'excel-uploader-dashboard-menu') === false &&
            strpos($hook, 'excel-uploader-dashboard-submenu') === false
        ) {
            return;
        }
        $this->suitepress_plugins_names_enqueue_assets();
    }

    public function plugins_names_add_admin_menu() {

        add_menu_page(
            __('Excel Uploader', 'excel-uploader'),
            __('Excel Uploader', 'excel-uploader'),
            'manage_options',
            'excel-uploader-dashboard-menu',
            [$this, 'render_dashboard'],
            $this->tranpr_get_menu_icon(),
            26
        );
        // add_submenu_page(
        //     "excel-uploader-dashboard-menu",
        //     __("Upload Excel","excel-uploader"),
        //     __("Upload Excel","excel-uploader"),
        //     "manage_options",
        //     "excel-uploader-dashboard-submenu",
        //     [$this, 'render_dashboard']
        // );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Attendee Records","excel-uploader"),
            __("Attendee Records","excel-uploader"),
            "manage_options",
            "attendee-records",
            [$this, 'render_attendee_list']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Cell Phone Report","excel-uploader"),
            __("Cell Phone Report","excel-uploader"),
            "manage_options",
            "cell-phone-report",
            [$this, 'render_cell_phone_report']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Country Report","excel-uploader"),
            __("Country Report","excel-uploader"),
            "manage_options",
            "country-report",
            [$this, 'render_country_report']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("State Report","excel-uploader"),
            __("State Report","excel-uploader"),
            "manage_options",
            "state-report",
            [$this, 'render_state_report']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Zip Code Report","excel-uploader"),
            __("Zip Code Report","excel-uploader"),
            "manage_options",
            "zip-code-report",
            [$this, 'render_zip_report']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Primary Major Report","excel-uploader"),
            __("Primary Major Report","excel-uploader"),
            "manage_options",
            "major-report",
            [$this, 'render_major_report']
        );
        
        add_submenu_page(
            "excel-uploader-dashboard-menu",
            __("Check Duplicates","excel-uploader"),
            __("Check Duplicates","excel-uploader"),
            "manage_options",
            "duplicate-checker",
            [$this, 'render_duplicate_checker']
        );
    }

    public function tranpr_get_menu_icon()
    {
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="33" height="32" viewBox="0 0 33 32" fill="none">
<path d="M7.80152 9.14362H11.3787C12.6375 9.14362 13.6544 10.1649 13.6544 11.4291C13.6544 12.6932 12.6375 13.7145 11.3787 13.7145H2.27574C1.01697 13.7145 0 12.6932 0 11.4291V2.28724C0 1.0231 1.01697 0.00178562 2.27574 0.00178562C3.53451 0.00178562 4.55148 1.0231 4.55148 2.28724V5.94398L5.80314 4.68698C12.0259 -1.56233 22.1102 -1.56233 28.333 4.68698C34.5557 10.9363 34.5557 21.0637 28.333 27.313C22.1102 33.5623 12.0259 33.5623 5.80314 27.313C4.91417 26.4203 4.91417 24.9704 5.80314 24.0777C6.6921 23.1849 8.13577 23.1849 9.02473 24.0777C13.4695 28.5415 20.6737 28.5415 25.1185 24.0777C29.5633 19.6139 29.5633 12.379 25.1185 7.91519C20.6737 3.4514 13.4695 3.4514 9.02473 7.91519L7.80152 9.14362Z" fill="#CFCFDE"/>
<path d="M23.6179 10.1547C23.8948 10.3449 24.0411 10.6751 23.9906 11.0061L22.2895 22.3861C22.2496 22.6514 22.0893 22.8845 21.8547 23.0167C21.6202 23.1489 21.3387 23.1666 21.0895 23.0637L17.8141 21.719L15.9501 23.7527C15.7079 24.0189 15.3255 24.108 14.9887 23.9782C14.6519 23.8484 14.4318 23.524 14.4303 23.1632L14.4205 20.8777C14.4201 20.7684 14.4606 20.6643 14.5341 20.582L19.0946 15.5651C19.2524 15.3922 19.2458 15.1271 19.0811 14.9637C18.9164 14.8004 18.6512 14.7906 18.4796 14.9472L12.9401 19.9108L10.5211 18.7128C10.2307 18.5691 10.0435 18.2801 10.0339 17.9576C10.0244 17.635 10.1926 17.3336 10.4707 17.1711L22.6883 10.1204C22.9801 9.95241 23.341 9.96727 23.6179 10.1547Z" fill="#CFCFDE"/>
</svg>');
    }

    public function render_dashboard() {
        $controller = new ExcelUploadController();
        $controller->render_form();
    }
    
    public function render_attendee_list() {
        $controller = new \ExcelUploader\Controllers\AttendeeCrudController();
        $controller->render_list();
    }
    
    public function render_cell_phone_report() {
        $controller = new \ExcelUploader\Controllers\ReportsController();
        $controller->render_cell_phone_report();
    }
    
    public function render_country_report() {
        $controller = new \ExcelUploader\Controllers\ReportsController();
        $controller->render_country_report();
    }
    
    public function render_state_report() {
        $controller = new \ExcelUploader\Controllers\ReportsController();
        $controller->render_state_report();
    }
    
    public function render_zip_report() {
        $controller = new \ExcelUploader\Controllers\ReportsController();
        $controller->render_zip_report();
    }
    
    public function render_major_report() {
        $controller = new \ExcelUploader\Controllers\ReportsController();
        $controller->render_major_report();
    }
    
    public function render_duplicate_checker() {
        $controller = new \ExcelUploader\Controllers\DuplicateController();
        $controller->render_duplicate_checker();
    }

    public function suitepress_plugins_names_enqueue_assets() {

        $dev_server = 'http://localhost:5173';
        $hot_file_path = EXCEL_UPLOADER_PATH . '/.hot';
        $is_dev = file_exists($hot_file_path);

        if ($is_dev) {
            // Enqueue Vite HMR client and main entry
            wp_enqueue_script('vite-client', $dev_server . '/@vite/client', [], null, true);
            wp_enqueue_script('my-plugin-vite', $dev_server . '/js/main.js',  [], null, true);

            wp_localize_script('my-plugin-vite', 'SuitePressSettings', [
                'restUrl' => esc_url_raw(rest_url('suitepress/v1/plugin-stats')),
                'nonce'   => wp_create_nonce('wp_rest'),
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]);

        } else {

            // Prod: Use filetime for cache busting
            $main_js = EXCEL_UPLOADER_BUILD_PATH . '/main.js';
            $main_css = EXCEL_UPLOADER_BUILD_PATH . '/main.css';

            $js_version = file_exists($main_js) ? filemtime($main_js) : '1.0.0';
            $css_version = file_exists($main_css) ? filemtime($main_css) : '1.0.0';

            // Load compiled assets
            wp_enqueue_script('my-plugin-main', EXCEL_UPLOADER_BUILD_URL . '/main.js', [], $js_version, true);
            wp_enqueue_style('my-plugin-style', EXCEL_UPLOADER_BUILD_URL . '/main.css', [], $css_version);

            wp_localize_script('my-plugin-main', 'SuitePressSettings', [
                'restUrl' => esc_url_raw(rest_url('suitepress/v1/plugin-stats')),
                'nonce'   => wp_create_nonce('wp_rest'),
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]);
        }

        // Optional: Add type="module" for both dev and prod
        add_filter('script_loader_tag', function ($tag, $handle) {
            if (in_array($handle, ['vite-client', 'my-plugin-vite', 'my-plugin-main'])) {
                $tag = str_replace('<script ', '<script type="module" ', $tag);
            }
            return $tag;
        }, 10, 2);
    }
}
