<?php
namespace ExcelUploader\Controllers;

use ExcelUploader\Services\TwigRenderer;
use ExcelUploader\Services\AttendeeDatabase;

/**
 * Controller for generating geographic and demographic reports
 */
class ReportsController {

    /**
     * Render cell phone report
     */
    public function render_cell_phone_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'Students by Cell Phone',
            'data' => $attendeeDb->get_by_area_code()
        ];
        
        echo $renderer->render('cell-phone-report.twig', $templateData);
    }
    
    /**
     * Render country report
     */
    public function render_country_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'International Students by Country',
            'data' => $attendeeDb->get_international_by_country()
        ];
        
        echo $renderer->render('country-report.twig', $templateData);
    }
    
    /**
     * Render state report
     */
    public function render_state_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'Domestic Students by State',
            'data' => $attendeeDb->get_domestic_by_state()
        ];
        
        echo $renderer->render('state-report.twig', $templateData);
    }
    
    /**
     * Render zip code report
     */
    public function render_zip_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'Domestic Students by Zip Code',
            'data' => $attendeeDb->get_domestic_by_zip()
        ];
        
        echo $renderer->render('zip-report.twig', $templateData);
    }
    
    /**
     * Render primary major report
     */
    public function render_major_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $templateData = [
            'page_title' => 'Students by Primary Major',
            'data' => $attendeeDb->get_by_primary_major()
        ];
        
        echo $renderer->render('major-report.twig', $templateData);
    }
}