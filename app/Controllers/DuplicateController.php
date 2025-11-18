<?php
namespace ExcelUploader\Controllers;

use ExcelUploader\Services\TwigRenderer;
use ExcelUploader\Services\AttendeeDatabase;

/**
 * Controller for checking duplicate attendee records
 */
class DuplicateController {

    /**
     * Render duplicate checker page
     */
    public function render_duplicate_checker() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'Duplicate Records Checker',
            'duplicates' => $attendeeDb->find_duplicates()
        ];
        
        echo $renderer->render('duplicate-checker.twig', $templateData);
    }
}