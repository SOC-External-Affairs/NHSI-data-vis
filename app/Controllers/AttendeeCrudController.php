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
        add_action('admin_post_delete_attendee', [$this, 'delete_single']);
        add_action('admin_post_update_attendee', [$this, 'update_attendee']);
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
        
        // Generate nonces for each attendee
        foreach ($attendees as $attendee) {
            $attendee->delete_nonce = wp_nonce_field('delete_attendee_' . $attendee->id, '_wpnonce', true, false);
            $attendee->edit_url = admin_url('admin.php?page=edit-attendee&id=' . $attendee->id);
        }
        
        $templateData = [
            'page_title' => 'Attendee Records',
            'attendees' => $attendees,
            'debug_info' => $debug_info,
            'delete_all_url' => admin_url('admin-post.php'),
            'delete_nonce' => wp_nonce_field('delete_all_nonce', '_wpnonce', true, false),
            'delete_single_url' => admin_url('admin-post.php'),
            'search_first_name' => $search_first_name,
            'search_netid' => $search_netid
        ];
        
        echo $renderer->render('attendee-list.twig', $templateData);
    }

    public function edit_form() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $attendee_id = intval($_GET['id'] ?? 0);
        $attendeeDb = new AttendeeDatabase();
        $attendee = $attendeeDb->get_attendee($attendee_id);
        
        if (!$attendee) {
            wp_die('Attendee not found');
        }
        
        $renderer = new TwigRenderer();
        
        $templateData = [
            'attendee' => $attendee,
            'update_url' => admin_url('admin-post.php'),
            'nonce' => wp_nonce_field('update_attendee_' . $attendee_id, '_wpnonce', true, false)
        ];
        
        echo $renderer->render('edit-attendee.twig', $templateData);
    }

    public function update_attendee() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $attendee_id = intval($_POST['attendee_id']);
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update_attendee_' . $attendee_id)) {
            wp_die('Security check failed');
        }

        $attendeeDb = new AttendeeDatabase();
        $current_attendee = $attendeeDb->get_attendee($attendee_id);
        
        if (!$current_attendee) {
            wp_die('Attendee not found');
        }

        // Only update editable fields
        $data = [
            'home_state' => sanitize_text_field($_POST['home_state']),
            'nu_student' => sanitize_text_field($_POST['nu_student']),
            'nu_grad_year' => sanitize_text_field($_POST['nu_grad_year']),
            'primary_school' => sanitize_text_field($_POST['primary_school']),
            'primary_major' => sanitize_text_field($_POST['primary_major']),
        ];

        $updated = $attendeeDb->update_attendee($attendee_id, $data);

        wp_redirect(admin_url('admin.php?page=attendee-records&updated=' . ($updated !== false ? 1 : 0)));
        exit;
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

    /**
     * Handle deletion of single attendee record
     */
    public function delete_single() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_attendee_' . $_POST['attendee_id'])) {
            wp_die('Security check failed');
        }

        $attendee_id = intval($_POST['attendee_id']);
        $attendeeDb = new AttendeeDatabase();
        $deleted = $attendeeDb->delete_attendee($attendee_id);

        wp_redirect(admin_url('admin.php?page=attendee-records&single_deleted=' . ($deleted ? 1 : 0)));
        exit;
    }
}