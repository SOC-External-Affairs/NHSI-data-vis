<?php

namespace ExcelUploader\Hooks\Handlers;

class RestApiHandlers {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('suitepress/v1', '/plugin-stats', [
            'methods' => 'GET',
            'callback' => [$this, 'get_plugin_stats'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
    }

    public function get_plugin_stats() {
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);

        return [
            'total' => count($all_plugins),
            'active' => count($active_plugins),
            'inactive' => count($all_plugins) - count($active_plugins),
        ];
    }
}
