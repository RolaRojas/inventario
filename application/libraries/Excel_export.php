<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_export {
    
    public function generate($data, $filename = 'export.xls') {
        // Set headers for Excel download
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Start output buffering
        ob_start();
        
        // Output HTML table that Excel can read
        echo '<table border="1">';
        
        // Output headers if data exists
        if (!empty($data)) {
            echo '<tr>';
            foreach (array_keys((array)$data[0]) as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr>';
            
            // Output data rows
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars($cell) . '</td>';
                }
                echo '</tr>';
            }
        }
        
        echo '</table>';
        
        // Get the buffer contents and end buffering
        $content = ob_get_contents();
        ob_end_clean();
        
        // Output the content
        echo $content;
        exit;
    }
}
