<?php
namespace ExcelUploader\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use ExcelUploader\Services\TwigRenderer;

/**
 * Controller for handling Excel file uploads and processing attendee data
 */
class ExcelUploadController {

    /**
     * Initialize controller and register WordPress hooks
     */
    public function __construct() {
        add_action('admin_post_excel_upload', [$this, 'handle_upload']);
    }

    /**
     * Render the Excel upload form using Twig template
     */
    public function render_form() {
        $renderer = new TwigRenderer();
        
        $templateData = [
            'page_title' => get_admin_page_title(),
            'success' => isset($_GET['success']) && $_GET['success'] == '1',
            'success_count' => $_GET['count'] ?? 0,
            'error' => isset($_GET['error']),
            'error_message' => $this->get_error_message($_GET['error'] ?? null),
            'admin_post_url' => admin_url('admin-post.php'),
            'nonce_field' => wp_nonce_field('excel_upload_nonce', '_wpnonce', true, false),
            'submit_button' => get_submit_button('Upload and Parse Excel')
        ];
        
        echo $renderer->render('excel-upload-form.twig', $templateData);
    }
    
    /**
     * Get user-friendly error message based on error code
     * @param string|null $error Error code
     * @return string Error message
     */
    private function get_error_message($error) {
        switch($error) {
            case '1': return 'Please select a valid Excel file.';
            case '2': return 'Error parsing Excel file. Please check the file format.';
            default: return 'An error occurred while processing the file.';
        }
    }

    /**
     * Handle Excel file upload, parse data, and save to database with privacy protection
     */
    public function handle_upload() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'excel_upload_nonce')) {
            wp_die('Security check failed');
        }

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=excel-uploader-dashboard-menu&error=1'));
            exit;
        }

        try {
            $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headers = array_shift($rows);
            $headers = $this->standardize_headers($headers);
            
            $attendeeDb = new \ExcelUploader\Services\AttendeeDatabase();
            $attendeeDb->create_table();
            $saved_count = 0;
            
            foreach ($rows as $row) {
                if (empty($row) || count($row) != count($headers)) continue;
                
                $rowData = array_combine($headers, $row);
                
                // ALWAYS apply sensitive information subduing
                $rowData = $this->subdue_sensitive_data($rowData);
                
                // Save to attendee_records table
                if ($attendeeDb->save_attendee($rowData)) {
                    $saved_count++;
                }
            }

            wp_redirect(admin_url('admin.php?page=excel-uploader-dashboard-menu&success=1&count=' . $saved_count));
            exit;

        } catch (Exception $e) {
            wp_redirect(admin_url('admin.php?page=excel-uploader-dashboard-menu&error=2'));
            exit;
        }
    }

    /**
     * Apply privacy protection by redacting sensitive information
     * @param array $rowData Row data from Excel file
     * @return array Processed row data with sensitive information redacted
     */
    private function subdue_sensitive_data($rowData) {
        foreach ($rowData as $key => $value) {
            // Remove last names
            if (stripos($key, 'last') !== false || stripos($key, 'surname') !== false) {
                $rowData[$key] = '[REDACTED]';
            }
            
            // Process full names - remove last name from any field with multiple words
            if (stripos($key, 'name') !== false && stripos($key, 'first') === false) {
                $names = explode(' ', trim($value));
                if (count($names) > 1) {
                    $rowData[$key] = $names[0];
                }
            }
            
            // Process phone numbers - detect by pattern only
            if (preg_match('/\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/', $value)) {
                $phone = preg_replace('/[^0-9]/', '', $value);
                if (strlen($phone) >= 10) {
                    $areaCode = substr($phone, 0, 3);
                    $rowData[$key] = $areaCode;
                }
            }

            // Process email address
            if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value)) {
                $parts = explode('@', $value);
                $domain = $parts[1];
                $rowData[$key] = '[REDACTED]@' . $domain;
            }                        


            // Process addresses - be more aggressive
            if (stripos($key, 'address') !== false || stripos($key, 'street') !== false || stripos($key, 'addr') !== false) {
                $rowData[$key] = '[ADDRESS REDACTED]';
            }
            
            // Also check for common patterns in values
            if (preg_match('/\d+\s+[A-Za-z]+\s+(st|street|ave|avenue|rd|road|blvd|boulevard|dr|drive|ln|lane|ct|court)/i', $value)) {
                $rowData[$key] = '[ADDRESS REDACTED]';
            }
        }
        
        return $rowData;
    }

    /**
     * Standardize Excel column headers to match database field names
     * @param array $headers Raw headers from Excel file
     * @return array Standardized header names
     */
    private function standardize_headers($headers) {
        $standardized = [];
        foreach ($headers as $header) {
            $clean = trim(strtolower($header));
            $clean = preg_replace('/[^a-z0-9]/', '_', $clean);
            $clean = preg_replace('/_+/', '_', $clean);
            $clean = trim($clean, '_');
            
            // Map Excel columns to database fields
            $mappings = [
                'division' => 'division',
                'concentration' => 'concentration',
                'year_attended' => 'year_attended',
                'netid' => 'netid',
                'last' => 'last',
                'preferred_first' => 'preferred_first',
                'legal_first' => 'legal_first',
                'gender' => 'gender',
                'pronouns' => 'pronouns',
                'birthday' => 'birthday',
                'maiden_name' => 'maiden_name',
                'dead_name_used_while_attending' => 'dead_name_used_while_attending',
                'email' => 'email',
                'student_cell' => 'student_cell',
                'hometown' => 'hometown',
                'home_state_province' => 'home_state_province',
                'home_country' => 'home_country',
                'zip_code' => 'zip_code',
                'nu_student' => 'nu_student',
                'nu_grad_year' => 'nu_grad_year',
                'primary_school' => 'primary_school',
                'primary_major_enrollment' => 'primary_major_enrollment'
            ];
            
            $standardized[] = $mappings[$clean] ?? $clean;
        }
        return $standardized;
    }
}