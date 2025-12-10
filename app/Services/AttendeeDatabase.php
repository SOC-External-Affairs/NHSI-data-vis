<?php
namespace ExcelUploader\Services;

class AttendeeDatabase {

    public function __construct() {
        add_action('init', [$this, 'create_table']);
        register_activation_hook(EXCEL_UPLOADER_PATH . '/excel-uploader.php', [$this, 'create_table']);
        $this->create_table();
    }

    public function create_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'attendee_records';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            division varchar(100),
            concentration varchar(100),
            year_attended varchar(20),
            netid varchar(50),
            last_name varchar(100),
            preferred_first varchar(100),
            legal_first varchar(100),
            gender varchar(50),
            pronouns varchar(50),
            birthday date,
            maiden_name varchar(100),
            dead_name varchar(100),
            email varchar(255),
            student_cell varchar(50),
            hometown varchar(100),
            home_state varchar(100),
            home_country varchar(100),
            zip_code varchar(20),
            nu_student varchar(10),
            nu_grad_year varchar(20),
            primary_school varchar(255),
            primary_major varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function save_attendee($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'attendee_records';
        
        $result = $wpdb->insert(
            $table_name,
            [
                'division' => $data['division'] ?? '',
                'concentration' => $data['concentration'] ?? '',
                'year_attended' => $data['year_attended'] ?? '',
                'netid' => $data['netid'] ?? '',
                'last_name' => $data['last'] ?? '',
                'preferred_first' => $data['preferred_first'] ?? '',
                'legal_first' => $data['legal_first'] ?? '',
                'gender' => $data['gender'] ?? '',
                'pronouns' => $data['pronouns'] ?? '',
                'birthday' => $data['birthday'] ?? null,
                'maiden_name' => $data['maiden_name'] ?? '',
                'dead_name' => $data['dead_name_used_while_attending'] ?? '',
                'email' => $data['email'] ?? '',
                'student_cell' => $data['student_cell'] ?? '',
                'hometown' => $data['hometown'] ?? '',
                'home_state' => $data['home_state_province'] ?? '',
                'home_country' => $data['home_country'] ?? '',
                'zip_code' => $data['zip_code'] ?? '',
                'nu_student' => $data['nu_student'] ?? '',
                'nu_grad_year' => $data['nu_grad_year'] ?? '',
                'primary_school' => $data['primary_school'] ?? '',
                'primary_major' => $data['primary_major_enrollment'] ?? ''
            ]
        );
        
        if ($result === false) {
            error_log('Database insert failed: ' . $wpdb->last_error);
            error_log('Data: ' . print_r($data, true));
        }
        
        return $result;
    }
    
    public function get_all_attendees($sort = 'created_at', $order = 'desc', $search_first_name = '', $search_netid = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        
        $allowed_columns = ['id', 'division', 'concentration', 'year_attended', 'netid', 'last_name', 'preferred_first', 'legal_first', 'gender', 'pronouns', 'email', 'student_cell', 'hometown', 'home_state', 'home_country', 'zip_code', 'nu_student', 'nu_grad_year', 'primary_school', 'primary_major', 'created_at'];
        $allowed_orders = ['asc', 'desc'];
        
        $sort = in_array($sort, $allowed_columns) ? $sort : 'created_at';
        $order = in_array($order, $allowed_orders) ? strtoupper($order) : 'DESC';
        
        $where_conditions = [];
        
        if (!empty($search_first_name) && strlen($search_first_name) <= 100) {
            $where_conditions[] = $wpdb->prepare("(preferred_first LIKE %s OR legal_first LIKE %s)", '%' . $search_first_name . '%', '%' . $search_first_name . '%');
        }
        
        if (!empty($search_netid) && strlen($search_netid) <= 20 && preg_match('/^[0-9]+$/', $search_netid)) {
            $where_conditions[] = $wpdb->prepare("netid REGEXP %s", $search_netid);
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $sql = "SELECT * FROM $table_name $where_clause ORDER BY $sort $order";
        return $wpdb->get_results($sql);
    }

    public function delete_all_attendees() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $wpdb->query("TRUNCATE TABLE $table_name");
        return $count;
    }

    public function delete_attendee($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->delete($table_name, ['id' => intval($id)], ['%d']);
    }
    
    public function debug_table_status() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        
        if (!$table_exists) {
            return ['exists' => false, 'count' => 0, 'error' => 'Table does not exist'];
        }
        
        // Get record count
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        return ['exists' => true, 'count' => $count, 'error' => null];
    }
    
    public function get_domestic_by_state() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT home_state, COUNT(*) as count FROM $table_name WHERE (home_country LIKE '%United States%' OR home_country LIKE '%USA%' OR home_country LIKE '%US%' OR home_country = '' OR home_country IS NULL) AND home_state != '' AND home_state IS NOT NULL GROUP BY home_state ORDER BY count DESC LIMIT 20");
    }
    
    public function get_domestic_by_zip() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT zip_code, COUNT(*) as count FROM $table_name WHERE (home_country LIKE '%United States%' OR home_country LIKE '%USA%' OR home_country LIKE '%US%' OR home_country = '' OR home_country IS NULL) AND zip_code != '' AND zip_code IS NOT NULL GROUP BY zip_code ORDER BY count DESC LIMIT 20");
    }
    
    public function get_international_by_country() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT home_country, COUNT(*) as count FROM $table_name WHERE home_country NOT LIKE '%United States%' AND home_country NOT LIKE '%USA%' AND home_country NOT LIKE '%US%' AND home_country != '' AND home_country IS NOT NULL GROUP BY home_country ORDER BY count DESC LIMIT 20");
    }
    
    public function get_by_area_code() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT student_cell, COUNT(*) as count FROM $table_name WHERE student_cell IS NOT NULL AND student_cell != '' GROUP BY student_cell ORDER BY count DESC LIMIT 20");
    }
    
    public function get_by_primary_major() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT primary_major, COUNT(*) as count FROM $table_name WHERE primary_major IS NOT NULL AND primary_major != '' GROUP BY primary_major ORDER BY count DESC LIMIT 20");
    }
    
    public function get_state_trends_by_year() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT home_state, year_attended, COUNT(*) as count FROM $table_name WHERE (home_country LIKE '%United States%' OR home_country LIKE '%USA%' OR home_country LIKE '%US%' OR home_country = '' OR home_country IS NULL) AND home_state IS NOT NULL AND home_state != '' AND year_attended IS NOT NULL AND year_attended != '' GROUP BY home_state, year_attended ORDER BY year_attended, home_state");
    }
    
    public function get_country_trends_by_year() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        return $wpdb->get_results("SELECT home_country, year_attended, COUNT(*) as count FROM $table_name WHERE home_country IS NOT NULL AND home_country != '' AND year_attended IS NOT NULL AND year_attended != '' GROUP BY home_country, year_attended ORDER BY year_attended, home_country");
    }
    
    // Find duplicate records across multiple identifying fields
    // Checks: email, netid, name, birthday, phone, zip, year
    public function find_duplicates() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'attendee_records';
        
        $sql = "SELECT 
            GROUP_CONCAT(id) as ids,
            email, netid, preferred_first, legal_first, last_name, birthday, student_cell, zip_code, year_attended,
            COUNT(*) as duplicate_count,
            'multi-field' as match_type
        FROM $table_name 
        WHERE (email IS NOT NULL AND email != '' AND email != '[REDACTED]@domain.com')
            OR (netid IS NOT NULL AND netid != '')
            OR (preferred_first IS NOT NULL AND preferred_first != '' AND last_name IS NOT NULL AND last_name != '' AND last_name != '[REDACTED]')
            OR (birthday IS NOT NULL)
            OR (student_cell IS NOT NULL AND student_cell != '')
            OR (zip_code IS NOT NULL AND zip_code != '')
            OR (year_attended IS NOT NULL AND year_attended != '')
        GROUP BY 
            COALESCE(NULLIF(email, ''), NULLIF(email, '[REDACTED]@domain.com')),
            COALESCE(NULLIF(netid, ''), ''),
            CONCAT(COALESCE(NULLIF(preferred_first, ''), ''), '|', COALESCE(NULLIF(last_name, ''), NULLIF(last_name, '[REDACTED]'), '')),
            COALESCE(birthday, '0000-00-00'),
            COALESCE(NULLIF(student_cell, ''), ''),
            COALESCE(NULLIF(zip_code, ''), ''),
            COALESCE(NULLIF(year_attended, ''), '')
        HAVING COUNT(*) > 1
        ORDER BY duplicate_count DESC";
        
        return $wpdb->get_results($sql);
    }
}