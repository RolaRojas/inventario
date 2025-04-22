<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retirados extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cargar modelos y librerías
        $this->load->model('Inventario_model');
        $this->load->library('session');
        $this->load->library('form_validation');

        if (!$this->session->userdata('logged_in') && $this->router->fetch_method() != 'index') {
            redirect('login');
        }
    }

    public function index() {
        // Cargar datos necesarios para la vista
        $data['articulos'] = $this->Inventario_model->getArticulosRetirados();
        $data['marcas'] = $this->Inventario_model->getMarcas();
        $data['familias'] = $this->Inventario_model->getFamilias();
        
        // Cargar la vista con los datos
        $this->load->view('retirados', $data);
    }

    // Método para exportar artículos retirados
    public function exportar($formato = 'excel') {
        if ($formato == 'excel') {
            // Exportar a Excel
            $this->exportar_excel();
        } else if ($formato == 'pdf') {
            // Exportar a PDF
            $this->exportar_pdf();
        } else {
            show_error('Formato de exportación no válido');
        }
    }
    
    // Método para exportar a Excel
    private function exportar_excel() {
        // Cargar la librería para Excel
        $this->load->library('PHPExcel');
        
        // Obtener los datos
        $articulos = $this->Inventario_model->exportarRetiradosExcel();
        
        // Crear un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        
        // Establecer propiedades
        $objPHPExcel->getProperties()->setCreator("Sistema de Inventario")
                                 ->setLastModifiedBy("Sistema de Inventario")
                                 ->setTitle("Artículos Retirados")
                                 ->setSubject("Artículos Retirados")
                                 ->setDescription("Lista de artículos retirados del inventario");
        
        // Agregar encabezados
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'Inventario Interno')
                    ->setCellValue('C1', 'Nombre')
                    ->setCellValue('D1', 'Categoría')
                    ->setCellValue('E1', 'Fecha de Retiro')
                    ->setCellValue('F1', 'Motivo');
        
        // Agregar datos
        $row = 2;
        foreach ($articulos as $articulo) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, $articulo->id)
                        ->setCellValue('B' . $row, $articulo->inventario_interno)
                        ->setCellValue('C' . $row, $articulo->nombre)
                        ->setCellValue('D' . $row, $articulo->categoria)
                        ->setCellValue('E' . $row, $articulo->fecha_retiro)
                        ->setCellValue('F' . $row, $articulo->motivo_retiro);
            $row++;
        }
        
        // Renombrar la hoja
        $objPHPExcel->getActiveSheet()->setTitle('Artículos Retirados');
        
        // Establecer la hoja activa
        $objPHPExcel->setActiveSheetIndex(0);
        
        // Redirigir la salida al navegador
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="articulos_retirados.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    // Método para exportar a PDF
    private function exportar_pdf() {
        // Cargar la librería para PDF
        $this->load->library('pdf');
        
        // Obtener los datos
        $articulos = $this->Inventario_model->exportarRetiradosExcel();
        
        // Crear un nuevo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Establecer información del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Inventario');
        $pdf->SetTitle('Artículos Retirados');
        $pdf->SetSubject('Artículos Retirados');
        
        // Establecer márgenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Agregar una página
        $pdf->AddPage();
        
        // Contenido HTML para el PDF
        $html = '<h1>Lista de Artículos Retirados</h1>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr><th>ID</th><th>Inventario Interno</th><th>Nombre</th><th>Categoría</th><th>Fecha de Retiro</th><th>Motivo</th></tr>';
        
        foreach ($articulos as $articulo) {
            $html .= '<tr>';
            $html .= '<td>' . $articulo->id . '</td>';
            $html .= '<td>' . $articulo->inventario_interno . '</td>';
            $html .= '<td>' . $articulo->nombre . '</td>';
            $html .= '<td>' . $articulo->categoria . '</td>';
            $html .= '<td>' . $articulo->fecha_retiro . '</td>';
            $html .= '<td>' . $articulo->motivo_retiro . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        // Escribir el HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Cerrar y generar el PDF
        $pdf->Output('articulos_retirados.pdf', 'D');
        exit;
    }

    // Método para agregar un artículo retirado
    public function agregar() {
        // Validar formulario
        $this->form_validation->set_rules('nroserie', 'Nombre/Nro. Serie', 'required');
        $this->form_validation->set_rules('categoria', 'Categoría', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver al formulario con errores
            $this->index();
        } else {
            // Preparar datos para insertar
            $datos = array(
                'nroserie' => $this->input->post('nroserie'),
                'descripcion' => $this->input->post('categoria'),
                'fecha_retiro' => date('Y-m-d'),
                'estado' => 'retirado'
            );
            
            // Insertar artículo
            $id_articulo = $this->Inventario_model->agregarArticulo($datos);
            
            if ($id_articulo) {
                // Mensaje de éxito
                $this->session->set_flashdata('mensaje', 'Artículo retirado agregado correctamente');
                $this->session->set_flashdata('tipo_mensaje', 'success');
            } else {
                // Mensaje de error
                $this->session->set_flashdata('mensaje', 'Error al agregar el artículo retirado');
                $this->session->set_flashdata('tipo_mensaje', 'error');
            }
            
            // Redireccionar
            redirect('retirados');
        }
    }

    // Método para modificar un artículo retirado
    public function modificar() {
        // Validar formulario
        $this->form_validation->set_rules('id_articulo', 'ID del Artículo', 'required');
        $this->form_validation->set_rules('nroserie', 'Nombre/Nro. Serie', 'required');
        $this->form_validation->set_rules('categoria', 'Categoría', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver al formulario con errores
            $this->index();
        } else {
            // Preparar datos para actualizar
            $id_articulo = $this->input->post('id_articulo');
            $datos = array(
                'nroserie' => $this->input->post('nroserie'),
                'descripcion' => $this->input->post('categoria')
            );
            
            // Actualizar artículo
            $resultado = $this->Inventario_model->modificarArticulo($id_articulo, $datos);
            
            if ($resultado) {
                // Mensaje de éxito
                $this->session->set_flashdata('mensaje', 'Artículo retirado modificado correctamente');
                $this->session->set_flashdata('tipo_mensaje', 'success');
            } else {
                // Mensaje de error
                $this->session->set_flashdata('mensaje', 'Error al modificar el artículo retirado');
                $this->session->set_flashdata('tipo_mensaje', 'error');
            }
            
            // Redireccionar
            redirect('retirados');
        }
    }
}
