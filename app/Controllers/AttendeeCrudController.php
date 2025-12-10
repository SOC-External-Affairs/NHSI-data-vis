<?php
namespace ExcelUploader\Controllers;

use ExcelUploader\Services\TwigRenderer;
use ExcelUploader\Services\AttendeeDatabase;

/**
 * Controller for managing attendee records with CRUD operations
 */
class AttendeeCrudController {

    /**
     * Initialize controller and register WordPress hooks
     */
    public function __construct() {
        add_action('admin_post_delete_all_attendees', [$this, 'delete_all']);
    }

    /**
     * Render sortable list of attendee records with delete functionality
     */
    public function render_list() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';
        $search_first_name = sanitize_text_field($_GET['search_first_name'] ?? '');
        $search_netid = sanitize_text_field($_GET['search_netid'] ?? '');
        
        $attendees = $attendeeDb->get_all_attendees($sort, $order, $search_first_name, $search_netid);
        $debug_info = $attendeeDb->debug_table_status();
        
        $templateData = [
            'page_title' => 'Attendee Records',
            'attendees' => $attendees,
            'debug_info' => $debug_info,
            'delete_all_url' => admin_url('admin-post.php'),
            'delete_nonce' => wp_nonce_field('delete_all_nonce', '_wpnonce', true, false),
            'search_first_name' => $search_first_name,
            'search_netid' => $search_netid
        ];
        
        echo $renderer->render('attendee-list.twig', $templateData);
    }

    /**
     * Handle bulk deletion of all attendee records
     */
    public function delete_all() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_all_nonce')) {
            wp_die('Security check failed');
        }

        $attendeeDb = new AttendeeDatabase();
        $deleted = $attendeeDb->delete_all_attendees();

        wp_redirect(admin_url('admin.php?page=attendee-records&deleted=' . $deleted));
        exit;
    }
}