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
    
    /**
     * Render state trends by year report
     */
    public function render_state_trends_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $rawData = $attendeeDb->get_state_trends_by_year();
        
        // Process data for chart
        $years = [];
        $states = [];
        $chartData = [];
        
        foreach ($rawData as $row) {
            $years[$row->year_attended] = true;
            $states[$row->home_state] = true;
            $chartData[$row->home_state][$row->year_attended] = $row->count;
        }
        
        $years = array_keys($years);
        $states = array_keys($states);
        sort($years);
        sort($states);
        
        $templateData = [
            'page_title' => 'State Trends by Year',
            'data' => $rawData,
            'years' => $years,
            'states' => $states,
            'chartData' => $chartData
        ];
        
        echo $renderer->render('state-trends-report.twig', $templateData);
    }
    
    /**
     * Render country trends by year report
     */
    public function render_country_trends_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $rawData = $attendeeDb->get_country_trends_by_year();
        
        // Process data for chart
        $years = [];
        $countries = [];
        $chartData = [];
        
        foreach ($rawData as $row) {
            $years[$row->year_attended] = true;
            $countries[$row->home_country] = true;
            $chartData[$row->home_country][$row->year_attended] = $row->count;
        }
        
        $years = array_keys($years);
        $countries = array_keys($countries);
        sort($years);
        sort($countries);
        
        $templateData = [
            'page_title' => 'Country Trends by Year',
            'data' => $rawData,
            'years' => $years,
            'countries' => $countries,
            'chartData' => $chartData
        ];
        
        echo $renderer->render('country-trends-report.twig', $templateData);
    }
    
    /**
     * Render division by year and state report
     */
    public function render_division_report() {
        $renderer = new TwigRenderer();
        $attendeeDb = new AttendeeDatabase();
        
        $rawData = $attendeeDb->get_waterfall_data();
        
        // Build Sankey data structure
        $nodes = [];
        $links = [];
        $nodeIndex = 0;
        $nodeMap = [];
        
        // Create nodes and links for Sankey
        foreach ($rawData as $row) {
            $year = $row->year_attended;
            $state = $row->home_state;
            $division = $row->division;
            $count = intval($row->count);
            
            // Create unique node IDs
            $yearNode = "Year_" . $year;
            $stateNode = "State_" . $state;
            $divisionNode = "Division_" . $division;
            
            // Add nodes if not exists
            if (!isset($nodeMap[$yearNode])) {
                $nodeMap[$yearNode] = $nodeIndex++;
                $nodes[] = ['name' => $year, 'category' => 'year'];
            }
            if (!isset($nodeMap[$stateNode])) {
                $nodeMap[$stateNode] = $nodeIndex++;
                $nodes[] = ['name' => $state, 'category' => 'state'];
            }
            if (!isset($nodeMap[$divisionNode])) {
                $nodeMap[$divisionNode] = $nodeIndex++;
                $nodes[] = ['name' => $division, 'category' => 'division'];
            }
            
            // Add links
            $links[] = [
                'source' => $nodeMap[$yearNode],
                'target' => $nodeMap[$stateNode],
                'value' => $count,
                'year' => $year,
                'state' => $state,
                'division' => $division,
                'type' => 'year-to-state'
            ];
            $links[] = [
                'source' => $nodeMap[$stateNode],
                'target' => $nodeMap[$divisionNode],
                'value' => $count,
                'year' => $year,
                'state' => $state,
                'division' => $division,
                'type' => 'state-to-division'
            ];
        }
        
        $templateData = [
            'page_title' => 'Division Flow: Year → State → Division',
            'data' => $rawData,
            'sankeyData' => [
                'nodes' => $nodes,
                'links' => $links
            ]
        ];
        
        echo $renderer->render('division-report.twig', $templateData);
    }
}