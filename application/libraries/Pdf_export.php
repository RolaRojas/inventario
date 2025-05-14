<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_export {
    
    public function generate($data, $filename = 'export.pdf') {
        // Load the mPDF library if available
        if (file_exists(APPPATH . 'third_party/mpdf/mpdf.php')) {
            require_once(APPPATH . 'third_party/mpdf/mpdf.php');
            $this->generate_with_mpdf($data, $filename);
            return;
        }
        
        // If mPDF is not available, use a simple HTML to PDF conversion
        // Set headers for PDF download
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        // Start building HTML content
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Export</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h1>Exported Data</h1>
            <table>
        ';
        
        // Add table headers
        if (!empty($data)) {
            $html .= '<tr>';
            foreach (array_keys((array)$data[0]) as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $html .= '</tr>';
            
            // Add data rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '
            </table>
        </body>
        </html>
        ';
        
        // Output the HTML content
        echo $html;
        exit;
    }
    
    private function generate_with_mpdf($data, $filename) {
        // Create new mPDF instance
        $mpdf = new mPDF();
        
        // Start building HTML content
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
        <h1>Exported Data</h1>
        <table>
        ';
        
        // Add table headers
        if (!empty($data)) {
            $html .= '<tr>';
            foreach (array_keys((array)$data[0]) as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $html .= '</tr>';
            
            // Add data rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</table>';
        
        // Write HTML to PDF
        $mpdf->WriteHTML($html);
        
        // Output PDF
        $mpdf->Output($filename, 'D');
        exit;
    }
}
