<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Inventario extends CI_Controller {

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
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('inicio');
        } else {
            $this->load->view('login');
        }
    }

    

    public function inicio()
    {
        $user = $this->session->userdata();

        if($user['id_rol'] == 1) {
            $this->load->model('Inventario_model');
            $data ['articulos'] = $this->Inventario_model->lista_inicio()->result();
            $data['data_grafico'] = $this->Inventario_model->lista_inicio()->result();
            //echo json_encode($data ['articulos']);
            $this->load->view('inicio',$data);
    //      echo json_encode($this->Inventario_model->getarticulo());
    //      $this->load->view('inicio');
        }else{
            redirect('inicio_visita');
        }	
        
    }

        public function crear_info()
    {
        $user = $this->session->userdata();

        if($user['id_rol'] == 1) {
            $data['articulos'] = $this->Inventario_model->getarticulo();
            $data['marcas'] = $this->Inventario_model->getMarcas();
            $data['familias'] = $this->Inventario_model->getFamilias();
            $data['ubicaciones'] = $this->Inventario_model->getUbicaciones();
            $data['siguiente_inventario'] = $this->Inventario_model->getSiguienteInventarioInterno();
            
            $this->load->view('crear_info', $data);
        }else{
            redirect('inicio_visita');
        }	
        // Cargar datos necesarios para la vista

    }

    

    public function inicio_visita()
    {
        $user = $this->session->userdata();

        if($user['id_rol'] != 2) {
            redirect('login');
        }else{
            $this->load->model('Inventario_model');
        $data ['articulos'] = $this->Inventario_model->lista_inicio()->result();
        $data['data_grafico'] = $this->Inventario_model->lista_inicio()->result();
        //echo json_encode($data ['articulos']);
        $this->load->view('inicio_visita',$data);
//      echo json_encode($this->Inventario_model->getarticulo());
//      $this->load->view('inicio');
        }	
       
    }
    

    
    public function recuperar()
    {
        $this->load->view('recuperar_clave');
    }

    public function ingresar() {
        $user = $this->session->userdata();

        if($user['id_rol'] == 1) {
            $data['articulos'] = $this->Inventario_model->getarticulo();
            $data['marcas'] = $this->Inventario_model->getMarcas();
            $data['familias'] = $this->Inventario_model->getFamilias();
            $data['ubicaciones'] = $this->Inventario_model->getUbicaciones();
            $data['siguiente_inventario'] = $this->Inventario_model->getSiguienteInventarioInterno();
            
            $this->load->view('ingresar', $data);
        }else{
            redirect('inicio_visita');
        }	
        // Cargar datos necesarios para la vista

    }

/*---------------------------------------------------------------*/
// Modificar el método agregar para manejar solicitudes AJAX
public function agregar() {
    // Validar formulario
    $this->form_validation->set_rules('nro_inventario', 'Número de Inventario', ''); // Ya no es requerido
    $this->form_validation->set_rules('nroserie', 'Número de Serie', 'required');
    $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
    $this->form_validation->set_rules('modelo', 'Modelo', 'required');
    $this->form_validation->set_rules('id_marca', 'Marca', 'required|numeric');
    $this->form_validation->set_rules('id_familia', 'Familia', 'required|numeric');
    
    if ($this->form_validation->run() === FALSE) {
        // Si la validación falla, devolver errores
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => false,
                'errors' => validation_errors()
            ));
            return;
        }
        $this->ingresar();
    } else {
        // Preparar datos para insertar
        $datos = array(
            'inventario_interno' => $this->Inventario_model->getSiguienteInventarioInterno(),
            'nro_inventario' => $this->input->post('nro_inventario'),
            'nroserie' => $this->input->post('nroserie'),
            'descripcion' => $this->input->post('descripcion'),
            'modelo' => $this->input->post('modelo'),
            'observacion' => $this->input->post('observacion'),
            'id_marca' => $this->input->post('id_marca'),
            'id_familia' => $this->input->post('id_familia')
        );
        
        // Insertar artículo
        $id_articulo = $this->Inventario_model->agregarArticulo($datos);
        
        if ($id_articulo) {
            // Si se especificó una ubicación, registrar en historial
            if ($this->input->post('id_ubicacion')) {
                $datos_historial = array(
                    'id_articulo' => $id_articulo,
                    'id_ubicacion' => $this->input->post('id_ubicacion'),
                    'fecha' => date('Y-m-d H:i:s')
                );
                
                $this->Inventario_model->agregarHistorial($datos_historial);
            }
            
            // Mensaje de éxito
            $this->session->set_flashdata('mensaje', 'Artículo agregado correctamente');
            $this->session->set_flashdata('tipo_mensaje', 'success');
            
            // Si es una solicitud AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Artículo agregado correctamente'
                ));
                return;
            }
        } else {
            // Mensaje de error
            $this->session->set_flashdata('mensaje', 'Error al agregar el artículo');
            $this->session->set_flashdata('tipo_mensaje', 'error');
            
            // Si es una solicitud AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Error al agregar el artículo'
                ));
                return;
            }
        }
        
        // Redireccionar si no es AJAX
        redirect('ingresar');
    }
}

// Método modificar corregido
public function modificar() {
    // Añadir depuración
    log_message('debug', 'Método modificar iniciado');
    log_message('debug', 'POST data: ' . json_encode($this->input->post()));
    
    // Validar formulario
    $this->form_validation->set_rules('id_articulo', 'ID del Artículo', 'required');
    $this->form_validation->set_rules('nro_inventario', 'Número de Inventario', ''); // No es requerido
    $this->form_validation->set_rules('nroserie', 'Número de Serie', 'required');
    $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
    $this->form_validation->set_rules('modelo', 'Modelo', 'required');
    $this->form_validation->set_rules('id_marca', 'Marca', 'required|numeric');
    $this->form_validation->set_rules('id_familia', 'Familia', 'required|numeric');
    
    if ($this->form_validation->run() === FALSE) {
        // Si la validación falla, devolver errores
        log_message('debug', 'Validación fallida: ' . validation_errors());
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => false,
                'errors' => validation_errors()
            ));
            return;
        }
        $this->ingresar();
    } else {
        // Preparar datos para actualizar
        $inventario_interno = $this->input->post('id_articulo');
        $datos = array(
            'nro_inventario' => $this->input->post('nro_inventario'),
            'nroserie' => $this->input->post('nroserie'),
            'descripcion' => $this->input->post('descripcion'),
            'modelo' => $this->input->post('modelo'),
            'observacion' => $this->input->post('observacion'),
            'id_marca' => $this->input->post('id_marca'),
            'id_familia' => $this->input->post('id_familia')
        );
        
        log_message('debug', 'Datos a modificar: ' . json_encode($datos));
        
        // Verificar si el artículo está retirado
        $this->db->select('fecha_retiro');
        $this->db->from('articulo');
        $this->db->where('inventario_interno', $inventario_interno);
        $query = $this->db->get();
        $articulo = $query->row();
        
        // Obtener el ID de ubicación "retirados"
        $this->db->select('id');
        $this->db->from('ubicacion');
        $this->db->where('nombre', 'retirados');
        $query_ubicacion = $this->db->get();
        $id_ubicacion_retirados = ($query_ubicacion->num_rows() > 0) ? $query_ubicacion->row()->id : null;
        
        log_message('debug', 'ID ubicación retirados: ' . $id_ubicacion_retirados);
        
        // Si el artículo está retirado y se intenta cambiar la ubicación a algo diferente de "retirados"
        $id_ubicacion = $this->input->post('id_ubicacion');
        if ($articulo && $articulo->fecha_retiro && $id_ubicacion != $id_ubicacion_retirados) {
            // No permitir cambiar la ubicación de un artículo retirado
            log_message('debug', 'Intento de cambiar ubicación de artículo retirado');
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'No se puede cambiar la ubicación de un artículo retirado'
                ));
                return;
            }
        }
        
        // Si se está marcando como retirado
        if ($id_ubicacion == $id_ubicacion_retirados && (!$articulo || !$articulo->fecha_retiro)) {
            // Agregar fecha de retiro
            $datos['fecha_retiro'] = date('Y-m-d');
            $datos['motivo_retiro'] = $this->input->post('motivo_retiro') ?: 'Retirado desde el panel de inventario';
            log_message('debug', 'Marcando artículo como retirado');
        }
        
        // Actualizar artículo
        $resultado = $this->Inventario_model->modificarArticuloPorInventarioInterno($inventario_interno, $datos);
        log_message('debug', 'Resultado de modificación: ' . ($resultado ? 'true' : 'false'));
        
        // Si se especificó una ubicación, registrar en historial
        $ubicacion_actualizada = false;
        if ($id_ubicacion) {
            // Obtener el ID real del artículo
            $this->db->select('id');
            $this->db->from('articulo');
            $this->db->where('inventario_interno', $inventario_interno);
            $query = $this->db->get();
            $articulo = $query->row();
            
            if ($articulo) {
                $datos_historial = array(
                    'id_articulo' => $articulo->id,
                    'id_ubicacion' => $id_ubicacion,
                    'fecha' => date('Y-m-d H:i:s')
                );
                
                $ubicacion_actualizada = $this->Inventario_model->agregarHistorial($datos_historial);
                log_message('debug', 'Ubicación actualizada: ' . ($ubicacion_actualizada ? 'true' : 'false'));
            }
        }
        
        if ($resultado || $ubicacion_actualizada) {
            // Mensaje de éxito
            $this->session->set_flashdata('mensaje', 'Artículo modificado correctamente');
            $this->session->set_flashdata('tipo_mensaje', 'success');
            
            // Si es una solicitud AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Artículo modificado correctamente'
                ));
                return;
            }
        } else {
            // Mensaje de error
            $this->session->set_flashdata('mensaje', 'No se realizaron cambios en el artículo');
            $this->session->set_flashdata('tipo_mensaje', 'warning');
            
            // Si es una solicitud AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'No se realizaron cambios en el artículo'
                ));
                return;
            }
        }
        
        // Redireccionar si no es AJAX
        redirect('ingresar');
    }
}


    // Método para filtrar artículos
    public function filtrar() {
        $marca = $this->input->post('marca');
        $familia = $this->input->post('familia');
        $busqueda = $this->input->post('busqueda');
        
        $articulos = $this->Inventario_model->filtrarArticulos($marca, $familia, $busqueda);
        
        echo json_encode(array('articulos' => $articulos));
    }


//-------------------------Crear Usuario------------------------------------------------------------------------------

    public function crear_usuario(){
        $user = $this->session->userdata();

        if($user['id_rol'] == 1) {
            $this->load->model('Inventario_model');
            $data ['articulos'] = $this->Inventario_model->lista_inicio()->result();
            $data['data_grafico'] = $this->Inventario_model->lista_inicio()->result();
           
            $this->load->view('crear_usuario', $data);
        }else{
            redirect('inicio_visita');
        }	
    }


//-----------------------Ubicaciones-------------------------------------------------------------------------------------------------
public function multivista() {
    // Verificar si el usuario está logueado
    if (!$this->session->userdata('logged_in')) {
        redirect('login');
    }
    
    // Cargar los datos necesarios para la vista
    $data['marcas'] = $this->Inventario_model->getMarcas();
    $data['familias'] = $this->Inventario_model->getFamilias();
    $data['ubicaciones'] = $this->Inventario_model->getUbicaciones();
    
    // Cargar la vista
    $this->load->view('multivista', $data);
}

// Métodos para agregar elementos
public function agregar_marca() {
    // Verificar si es una solicitud AJAX
    if (!$this->input->is_ajax_request()) {
        show_error('No se permite el acceso directo a este recurso');
        return;
    }
    
    // Obtener datos del formulario
    $nombre = $this->input->post('nombre');
    
    // Validar datos
    if (empty($nombre)) {
        echo json_encode(['exito' => false, 'mensaje' => 'El nombre es obligatorio']);
        return;
    }
    
    // Insertar en la base de datos
    $this->db->insert('marca', ['nombre' => $nombre]);
    
    // Verificar si se insertó correctamente
    if ($this->db->affected_rows() > 0) {
        echo json_encode(['exito' => true, 'mensaje' => 'Marca agregada correctamente']);
    } else {
        echo json_encode(['exito' => false, 'mensaje' => 'Error al agregar la marca']);
    }
}

// Métodos similares para familias y ubicaciones
// ...



/*-----------------------------------------------------------------*/

// Update the retirados method in the Inventario controller
public function retirados()
{
    $user = $this->session->userdata();

    if($user['id_rol'] == 1) {
        $this->load->model('Inventario_model');
    
        // Add debug logging
        log_message('debug', 'Executing retirados method in Inventario controller');
        
        // Get retired articles
        $articulos = $this->Inventario_model->getArticulosRetirados();
        
        // Debug: Check if we have any retired articles
        log_message('debug', 'Number of retired articles: ' . count($articulos));
        log_message('debug', 'Retired articles data: ' . json_encode($articulos));
        
        // Load additional data needed for the view
        $data['articulos'] = $articulos;
        $data['marcas'] = $this->Inventario_model->getMarcas();
        $data['familias'] = $this->Inventario_model->getFamilias();
        $data['ubicaciones'] = $this->Inventario_model->getUbicaciones();
        
        // Load the view with the data
        $this->load->view('retirados', $data);
    }else{
        redirect('inicio_visita');
    }	

}
    
    // Método para obtener detalles de un artículo retirado (para AJAX)
    public function detalles_retirado($id)
    {
        $this->load->model('Inventario_model');
        $articulo = $this->Inventario_model->getArticuloRetiradoPorId($id);
        
        if (!$articulo) {
            // Si no se encuentra el artículo, devolver error
            $response = array('error' => 'Artículo no encontrado');
            $this->output->set_status_header(404);
        } else {
            // Si se encuentra, devolver los datos
            $response = $articulo;
        }
        
        // Devolver respuesta en formato JSON
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

// Agregar este método para devolver artículos retirados al inventario
public function devolver_articulo() {
    // Agregar logs para depuración
    log_message('debug', 'Iniciando método devolver_articulo');
    log_message('debug', 'POST data: ' . json_encode($this->input->post()));
    
    // Validate the form data
    $this->form_validation->set_rules('id_articulo', 'ID del Artículo', 'required');
    $this->form_validation->set_rules('id_ubicacion', 'Nueva Ubicación', 'required');
    
    if ($this->form_validation->run() === FALSE) {
        // If validation fails, return errors
        log_message('debug', 'Validación fallida: ' . validation_errors());
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => false,
                'errors' => validation_errors()
            ));
            return;
        }
        redirect('retirados');
    } else {
        // Get form data
        $id_articulo = $this->input->post('id_articulo');
        $id_ubicacion = $this->input->post('id_ubicacion');
        $observacion = $this->input->post('observacion');
        
        log_message('debug', 'ID artículo: ' . $id_articulo);
        log_message('debug', 'ID ubicación: ' . $id_ubicacion);
        
        // Check if "En Bodega" option was selected
        if ($id_ubicacion === 'en_bodega') {
            log_message('debug', 'Opción "En Bodega" seleccionada');
            // Check if "En Bodega" location exists in the database
            $this->db->select('id');
            $this->db->from('ubicacion');
            $this->db->where('nombre', 'En Bodega');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                // If it exists, use its ID
                $id_ubicacion = $query->row()->id;
                log_message('debug', 'Ubicación "En Bodega" encontrada con ID: ' . $id_ubicacion);
            } else {
                // If it doesn't exist, create it
                $this->db->insert('ubicacion', array('nombre' => 'En Bodega'));
                $id_ubicacion = $this->db->insert_id();
                log_message('debug', 'Ubicación "En Bodega" creada con ID: ' . $id_ubicacion);
            }
        }
        
        // Prepare data to update
        $datos = array(
            'fecha_retiro' => NULL,
            'motivo_retiro' => NULL
        );
        
        log_message('debug', 'Datos para actualizar: ' . json_encode($datos));
        
        // Update article
        $resultado = $this->Inventario_model->modificarArticuloPorInventarioInterno($id_articulo, $datos);
        log_message('debug', 'Resultado de modificación: ' . ($resultado ? 'true' : 'false'));
        
        // Register new location in history
        $ubicacion_actualizada = false;
        if ($resultado) {
            $datos_historial = array(
                'id_articulo' => $id_articulo,
                'id_ubicacion' => $id_ubicacion,
                'fecha' => date('Y-m-d H:i:s')
            );
            
            log_message('debug', 'Datos historial: ' . json_encode($datos_historial));
            $ubicacion_actualizada = $this->Inventario_model->agregarHistorial($datos_historial);
            log_message('debug', 'Ubicación actualizada: ' . ($ubicacion_actualizada ? 'true' : 'false'));
            
            // Success message
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => true,
                    'message' => 'Artículo devuelto al inventario correctamente'
                ));
                return;
            }
        } else {
            // Error message
            log_message('error', 'Error al devolver el artículo al inventario');
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Error al devolver el artículo al inventario'
                ));
                return;
            }
        }
        
        // Redirect if not AJAX
        redirect('retirados');
    }
}

// Método para exportar artículos to Excel
public function exportar($formato = 'excel') {
    // Suprimir advertencias de deprecación
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    
    $this->load->model('Inventario_model');
    
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
    try {
        // Try to use PHPExcel if available
        if (file_exists(APPPATH . 'third_party/PHPExcel/PHPExcel.php')) {
            require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->exportar_excel_phpexcel();
            return;
        }
        
        // Obtener datos con ubicación
        $articulos = $this->Inventario_model->exportarExcelConUbicacion();
        
        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Establecer propiedades
        $spreadsheet->getProperties()->setCreator("Sistema de Inventario")
                                 ->setLastModifiedBy("Sistema de Inventario")
                                 ->setTitle("Inventario de Artículos")
                                 ->setSubject("Inventario de Artículos")
                                 ->setDescription("Lista completa de artículos en inventario");
        
        // Agregar encabezados
        $sheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'Nro. Inventario')
              ->setCellValue('C1', 'Nro. Serie')
              ->setCellValue('D1', 'Descripción')
              ->setCellValue('E1', 'Modelo')
              ->setCellValue('F1', 'Marca')
              ->setCellValue('G1', 'Familia')
              ->setCellValue('H1', 'Ubicación')
              ->setCellValue('I1', 'Observación');
        
        // Agregar datos
        $row = 2;
        foreach ($articulos as $articulo) {
            $sheet->setCellValue('A' . $row, $articulo->inventario_interno)
                  ->setCellValue('B' . $row, $articulo->nro_inventario)
                  ->setCellValue('C' . $row, $articulo->nroserie)
                  ->setCellValue('D' . $row, $articulo->descripcion)
                  ->setCellValue('E' . $row, $articulo->modelo)
                  ->setCellValue('F' . $row, $articulo->marca_nombre)
                  ->setCellValue('G' . $row, $articulo->familia_nombre)
                  ->setCellValue('H' . $row, $articulo->ubicacion_nombre)
                  ->setCellValue('I' . $row, $articulo->observacion);
            $row++;
        }
        
        // Ajustar el ancho de las columnas automáticamente
        foreach(range('A','I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Renombrar la hoja
        $sheet->setTitle('Inventario');
        
        // Establecer la hoja activa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Redirigir la salida al navegador
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inventario_articulos.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        show_error('Error al exportar a Excel: ' . $e->getMessage());
    }
}

// Original PHPExcel export method
private function exportar_excel_phpexcel() {
    // Obtener los datos con la ubicación actual
    $articulos = $this->Inventario_model->exportarExcelConUbicacion();
    
    // Crear un nuevo objeto PHPExcel
    $objPHPExcel = new PHPExcel();
    
    // Establecer propiedades
    $objPHPExcel->getProperties()->setCreator("Sistema de Inventario")
                             ->setLastModifiedBy("Sistema de Inventario")
                             ->setTitle("Inventario de Artículos")
                             ->setSubject("Inventario de Artículos")
                             ->setDescription("Lista completa de artículos en inventario");
    
    // Agregar encabezados
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Nro. Inventario')
                ->setCellValue('C1', 'Nro. Serie')
                ->setCellValue('D1', 'Descripción')
                ->setCellValue('E1', 'Modelo')
                ->setCellValue('F1', 'Marca')
                ->setCellValue('G1', 'Familia')
                ->setCellValue('H1', 'Ubicación')
                ->setCellValue('I1', 'Observación');
    
    // Agregar datos
    $row = 2;
    foreach ($articulos as $articulo) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, $articulo->inventario_interno)
                    ->setCellValue('B' . $row, $articulo->nro_inventario)
                    ->setCellValue('C' . $row, $articulo->nroserie)
                    ->setCellValue('D' . $row, $articulo->descripcion)
                    ->setCellValue('E' . $row, $articulo->modelo)
                    ->setCellValue('F' . $row, $articulo->marca_nombre)
                    ->setCellValue('G' . $row, $articulo->familia_nombre)
                    ->setCellValue('H' . $row, $articulo->ubicacion_nombre)
                    ->setCellValue('I' . $row, $articulo->observacion);
        $row++;
    }
    
    // Ajustar el ancho de las columnas automáticamente
    foreach(range('A','I') as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Renombrar la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Inventario');
    
    // Establecer la hoja activa
    $objPHPExcel->setActiveSheetIndex(0);
    
    // Redirigir la salida al navegador
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="inventario_articulos.xlsx"');
    header('Cache-Control: max-age=0');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}

// Método para exportar a PDF
private function exportar_pdf() {
    try {
        // Try to use TCPDF if available
        if (file_exists(APPPATH . 'third_party/tcpdf/tcpdf.php')) {
            require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
            $this->exportar_pdf_tcpdf();
            return;
        }
        
        // Obtener datos con ubicación
        $articulos = $this->Inventario_model->exportarExcelConUbicacion();
        
        // Crear un nuevo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Establecer información del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Inventario');
        $pdf->SetTitle('Inventario de Artículos');
        $pdf->SetSubject('Inventario de Artículos');
        
        // Establecer márgenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Agregar una página
        $pdf->AddPage('L'); // Landscape orientation
        
        // Contenido HTML para el PDF
        $html = '<h1>Inventario de Artículos</h1>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
        $html .= '<th>ID</th>';
        $html .= '<th>Nro. Inventario</th>';
        $html .= '<th>Nro. Serie</th>';
        $html .= '<th>Descripción</th>';
        $html .= '<th>Modelo</th>';
        $html .= '<th>Marca</th>';
        $html .= '<th>Familia</th>';
        $html .= '<th>Ubicación</th>';
        $html .= '<th>Observación</th>';
        $html .= '</tr>';
        
        foreach ($articulos as $articulo) {
            $html .= '<tr>';
            $html .= '<td>' . $articulo->inventario_interno . '</td>';
            $html .= '<td>' . $articulo->nro_inventario . '</td>';
            $html .= '<td>' . $articulo->nroserie . '</td>';
            $html .= '<td>' . $articulo->descripcion . '</td>';
            $html .= '<td>' . $articulo->modelo . '</td>';
            $html .= '<td>' . $articulo->marca_nombre . '</td>';
            $html .= '<td>' . $articulo->familia_nombre . '</td>';
            $html .= '<td>' . $articulo->ubicacion_nombre . '</td>';
            $html .= '<td>' . $articulo->observacion . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        // Escribir el HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Cerrar y generar el PDF
        $pdf->Output('inventario_articulos.pdf', 'D');
        exit;
    } catch (Exception $e) {
        show_error('Error al exportar a PDF: ' . $e->getMessage());
    }
}

// Original TCPDF export method
private function exportar_pdf_tcpdf() {
    // Obtener los datos con la ubicación actual
    $articulos = $this->Inventario_model->exportarExcelConUbicacion();
    
    // Crear un nuevo documento PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Establecer información del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema de Inventario');
    $pdf->SetTitle('Inventario de Artículos');
    $pdf->SetSubject('Inventario de Artículos');
    
    // Establecer márgenes
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    // Agregar una página
    $pdf->AddPage('L'); // Landscape orientation
    
    // Contenido HTML para el PDF
    $html = '<h1>Inventario de Artículos</h1>';
    $html .= '<table border="1" cellpadding="5">';
    $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
    $html .= '<th>ID</th>';
    $html .= '<th>Nro. Inventario</th>';
    $html .= '<th>Nro. Serie</th>';
    $html .= '<th>Descripción</th>';
    $html .= '<th>Modelo</th>';
    $html .= '<th>Marca</th>';
    $html .= '<th>Familia</th>';
    $html .= '<th>Ubicación</th>';
    $html .= '<th>Observación</th>';
    $html .= '</tr>';
    
    foreach ($articulos as $articulo) {
        $html .= '<tr>';
        $html .= '<td>' . $articulo->inventario_interno . '</td>';
        $html .= '<td>' . $articulo->nro_inventario . '</td>';
        $html .= '<td>' . $articulo->nroserie . '</td>';
        $html .= '<td>' . $articulo->descripcion . '</td>';
        $html .= '<td>' . $articulo->modelo . '</td>';
        $html .= '<td>' . $articulo->marca_nombre . '</td>';
        $html .= '<td>' . $articulo->familia_nombre . '</td>';
        $html .= '<td>' . $articulo->ubicacion_nombre . '</td>';
        $html .= '<td>' . $articulo->observacion . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</table>';
    
    // Escribir el HTML en el PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Cerrar y generar el PDF
    $pdf->Output('inventario_articulos.pdf', 'D');
    exit;
}

// Método para exportar artículos filtrados
public function exportar_filtrados($formato = 'excel') {
    // Suprimir advertencias de deprecación
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    
    $marca = $this->input->post('marca');
    $familia = $this->input->post('familia');
    $busqueda = $this->input->post('busqueda');
    
    $this->load->model('Inventario_model');
    $articulos = $this->Inventario_model->filtrarArticulosConUbicacion($marca, $familia, $busqueda);
    
    if ($formato == 'excel') {
        // Exportar a Excel
        $this->exportar_filtrados_excel($articulos);
    } else if ($formato == 'pdf') {
        // Exportar a PDF
        $this->exportar_filtrados_pdf($articulos);
    } else {
        show_error('Formato de exportación no válido');
    }
}

// Método para exportar artículos filtrados a Excel
private function exportar_filtrados_excel($articulos) {
    try {
        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Establecer propiedades
        $spreadsheet->getProperties()->setCreator("Sistema de Inventario")
                                 ->setLastModifiedBy("Sistema de Inventario")
                                 ->setTitle("Inventario de Artículos")
                                 ->setSubject("Inventario de Artículos")
                                 ->setDescription("Lista filtrada de artículos en inventario");
        
        // Agregar encabezados
        $sheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'Nro. Inventario')
              ->setCellValue('C1', 'Nro. Serie')
              ->setCellValue('D1', 'Descripción')
              ->setCellValue('E1', 'Modelo')
              ->setCellValue('F1', 'Marca')
              ->setCellValue('G1', 'Familia')
              ->setCellValue('H1', 'Ubicación')
              ->setCellValue('I1', 'Observación');
        
        // Agregar datos
        $row = 2;
        if ($articulos) {
            foreach ($articulos as $articulo) {
                $sheet->setCellValue('A' . $row, $articulo->inventario_interno)
                      ->setCellValue('B' . $row, $articulo->nro_inventario)
                      ->setCellValue('C' . $row, $articulo->nroserie)
                      ->setCellValue('D' . $row, $articulo->descripcion)
                      ->setCellValue('E' . $row, $articulo->modelo)
                      ->setCellValue('F' . $row, $articulo->marca_nombre)
                      ->setCellValue('G' . $row, $articulo->familia_nombre)
                      ->setCellValue('H' . $row, $articulo->ubicacion_nombre)
                      ->setCellValue('I' . $row, $articulo->observacion);
                $row++;
            }
        }
        
        // Ajustar el ancho de las columnas automáticamente
        foreach(range('A','I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Renombrar la hoja
        $sheet->setTitle('Inventario');
        
        // Establecer la hoja activa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Redirigir la salida al navegador
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inventario_articulos_filtrados.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        show_error('Error al exportar a Excel: ' . $e->getMessage());
    }
}

// Método para exportar artículos filtrados a PDF
private function exportar_filtrados_pdf($articulos) {
    try {
        // Crear un nuevo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Establecer información del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Inventario');
        $pdf->SetTitle('Inventario de Artículos');
        $pdf->SetSubject('Inventario de Artículos');
        
        // Establecer márgenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Agregar una página
        $pdf->AddPage('L'); // Landscape orientation
        
        // Contenido HTML para el PDF
        $html = '<h1>Inventario de Artículos (Filtrados)</h1>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
        $html .= '<th>ID</th>';
        $html .= '<th>Nro. Inventario</th>';
        $html .= '<th>Nro. Serie</th>';
        $html .= '<th>Descripción</th>';
        $html .= '<th>Modelo</th>';
        $html .= '<th>Marca</th>';
        $html .= '<th>Familia</th>';
        $html .= '<th>Ubicación</th>';
        $html .= '<th>Observación</th>';
        $html .= '</tr>';
        
        if ($articulos) {
            foreach ($articulos as $articulo) {
                $html .= '<tr>';
                $html .= '<td>' . $articulo->inventario_interno . '</td>';
                $html .= '<td>' . $articulo->nro_inventario . '</td>';
                $html .= '<td>' . $articulo->nroserie . '</td>';
                $html .= '<td>' . $articulo->descripcion . '</td>';
                $html .= '<td>' . $articulo->modelo . '</td>';
                $html .= '<td>' . $articulo->marca_nombre . '</td>';
                $html .= '<td>' . $articulo->familia_nombre . '</td>';
                $html .= '<td>' . $articulo->ubicacion_nombre . '</td>';
                $html .= '<td>' . $articulo->observacion . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="9" style="text-align:center;">No se encontraron artículos con los filtros aplicados</td></tr>';
        }
        
        $html .= '</table>';
        
        // Escribir el HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Cerrar y generar el PDF
        $pdf->Output('inventario_articulos_filtrados.pdf', 'D');
        exit;
    } catch (Exception $e) {
        show_error('Error al exportar a PDF: ' . $e->getMessage());
    }
}

    
    public function exportar_retirados($formato = 'excel')
    {
        // Suprimir advertencias de deprecación
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        
        $this->load->model('Inventario_model');
        
        if ($formato == 'excel') {
            // Exportar a Excel
            $this->exportar_excel_retirados();
        } else if ($formato == 'pdf') {
            // Exportar a PDF
            $this->exportar_pdf_retirados();
        } else {
            show_error('Formato de exportación no válido');
        }
    }
    
    // Método para exportar a Excel
    private function exportar_excel_retirados()
    {
        try {
            // Obtener los datos
            $articulos = $this->Inventario_model->exportarRetiradosExcel();
            
            // Crear un nuevo objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Establecer propiedades
            $spreadsheet->getProperties()->setCreator("Sistema de Inventario")
                                     ->setLastModifiedBy("Sistema de Inventario")
                                     ->setTitle("Artículos Retirados")
                                     ->setSubject("Artículos Retirados")
                                     ->setDescription("Lista de artículos retirados del inventario");
            
            // Agregar encabezados
            $sheet->setCellValue('A1', 'ID')
                  ->setCellValue('B1', 'Inventario Interno')
                  ->setCellValue('C1', 'Nombre')
                  ->setCellValue('D1', 'Categoría')
                  ->setCellValue('E1', 'Fecha de Retiro')
                  ->setCellValue('F1', 'Motivo');
            
            // Agregar datos
            $row = 2;
            foreach ($articulos as $articulo) {
                $sheet->setCellValue('A' . $row, $articulo->id)
                      ->setCellValue('B' . $row, $articulo->inventario_interno)
                      ->setCellValue('C' . $row, $articulo->nombre)
                      ->setCellValue('D' . $row, $articulo->categoria)
                      ->setCellValue('E' . $row, $articulo->fecha_retiro)
                      ->setCellValue('F' . $row, $articulo->motivo_retiro);
                $row++;
            }
            
            // Renombrar la hoja
            $sheet->setTitle('Artículos Retirados');
            
            // Establecer la hoja activa
            $spreadsheet->setActiveSheetIndex(0);
            
            // Redirigir la salida al navegador
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="articulos_retirados.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            show_error('Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    // Método para exportar a PDF retirados
    private function exportar_pdf_retirados()
    {
        try {
            // Try multiple possible paths for TCPDF
            $possible_paths = [
                APPPATH . 'third_party/tcpdf/tcpdf.php',
                APPPATH . 'libraries/tcpdf/tcpdf.php',
                APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php',
                // Add more potential paths if needed
            ];
            
            $loaded = false;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $loaded = true;
                    break;
                }
            }
            
            if (!$loaded) {
                show_error('No se pudo cargar la librería TCPDF. Por favor, verifique que esté instalada correctamente.');
                return;
            }
            
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
        } catch (Exception $e) {
            show_error('Error al exportar a PDF: ' . $e->getMessage());
        }
    }

// Add a debug method to check if there are any retired items in the database
public function debug_retirados()
{
    $this->load->model('Inventario_model');
    
    // Direct database query to check for retired items
    $this->db->select('COUNT(*) as count');
    $this->db->from('articulo');
    $this->db->where('fecha_retiro IS NOT NULL');
    $query = $this->db->get();
    $result = $query->row();
    
    echo "Number of retired items in database: " . $result->count;
    
    // Check database structure
    echo "<br><br>Database structure:<br>";
    $fields = $this->db->list_fields('articulo');
    echo "Fields in articulo table: " . implode(', ', $fields);
    
    // Check a few sample records
    echo "<br><br>Sample records:<br>";
    $this->db->limit(5);
    $query = $this->db->get('articulo');
    echo "<pre>";
    print_r($query->result_array());
    echo "</pre>";
    
    exit;
}

    public function vista()
    {
        $this->load->view('vista');
    }

    public function visitas()
    {
        $this->load->model('Inventario_model');
        $data ['articulos'] = $this->Inventario_model->lista_inicio()->result();
        $data['data_grafico'] = $this->Inventario_model->lista_inicio()->result();
        //echo json_encode($data ['articulos']);
        $this->load->view('visitas',$data);
//      echo json_encode($this->Inventario_model->getarticulo());
//      $this->load->view('inicio');
    }

    public function datos_grafico()
    {
        $this->load->model('Inventario_model');
        $datos = $this->Inventario_model->obtenerResumenGrafico();

        $respuesta = [
            'labels' => [],
            'data' => []
        ];

        foreach ($datos as $fila) {
            $respuesta['labels'][] = $fila->categoria;
            $respuesta['data'][] = (int) $fila->cantidad;
        }

        echo json_encode($respuesta);
    }


    public function retirar_articulo() {
        // Validar los datos del formulario
        $this->form_validation->set_rules('id_articulo', 'ID del Artículo', 'required');
        $this->form_validation->set_rules('motivo_retiro', 'Motivo de Retiro', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, devolver errores
            $response = [
                'success' => false,
                'errors' => validation_errors()
            ];
            echo json_encode($response);
            return;
        }
        
        $id_articulo = $this->input->post('id_articulo');
        $motivo_retiro = $this->input->post('motivo_retiro');
        
        // Obtener el ID de la ubicación "retirados"
        $this->db->select('id');
        $this->db->from('ubicacion');
        $this->db->where('nombre', 'retirados');
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            $response = [
                'success' => false,
                'message' => 'No se encontró la ubicación "retirados"'
            ];
            echo json_encode($response);
            return;
        }
        
        $id_ubicacion_retirados = $query->row()->id;
        
        // Actualizar el artículo
        $datos = [
            'id_ubicacion' => $id_ubicacion_retirados,
            'fecha_retiro' => date('Y-m-d'),
            'motivo_retiro' => $motivo_retiro
        ];
        
        $this->load->model('Inventario_model');
        $resultado = $this->Inventario_model->modificarArticulo($id_articulo, $datos);
        
        if ($resultado) {
            // Registrar en el historial
            $datos_historial = [
                'id_articulo' => $id_articulo,
                'id_ubicacion' => $id_ubicacion_retirados,
                'accion' => 'Retiro',
                'fecha' => date('Y-m-d H:i:s'),
                'usuario' => $this->session->userdata('nombre'),
                'observacion' => $motivo_retiro
            ];
            $this->Inventario_model->agregarHistorial($datos_historial);
            
            // Devolver respuesta exitosa
            $response = [
                'success' => true,
                'message' => 'Artículo retirado correctamente'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error al retirar el artículo'
            ];
        }
        
        echo json_encode($response);
    }


    // Método para obtener un artículo por ID (para AJAX)
    public function obtener_articulo($id) {
        // Cargar el modelo si no está cargado
        if (!isset($this->Inventario_model)) {
            $this->load->model('Inventario_model');
        }
        
        // Registrar información de depuración
        log_message('debug', 'Controlador: Obteniendo artículo con ID: ' . $id);
        
        $articulo = $this->Inventario_model->getArticuloPorId($id);
        
        if (!$articulo) {
            // Si no se encuentra el artículo, devolver error
            log_message('error', 'Controlador: Artículo no encontrado con ID: ' . $id);
            $response = array('error' => 'Artículo no encontrado');
            $this->output->set_status_header(404);
        } else {
            // Si se encuentra, devolver los datos
            log_message('debug', 'Controlador: Artículo encontrado: ' . json_encode($articulo));
            $response = $articulo;
        }
        
        // Establecer cabeceras CORS para permitir solicitudes desde cualquier origen
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Methods: GET');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        
        // Devolver respuesta en formato JSON
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    // Método para obtener la ubicación actual de un artículo
    public function obtener_ubicacion_actual($id_articulo) {
        // Cargar el modelo si no está cargado
        if (!isset($this->Inventario_model)) {
            $this->load->model('Inventario_model');
        }
        
        // Obtener la ubicación actual
        $ubicacion = $this->Inventario_model->getUbicacionActual($id_articulo);
        
        // Devolver respuesta en formato JSON
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($ubicacion));
    }

    //-----------------------------------------------------------
    // Método para mostrar la vista de importación
    public function importar() {
        // Verificar si el usuario está logueado
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        
        $this->load->view('importar_excel');
    }

    // Método para mostrar la vista de importación de Excel
    public function importar_excel() {
        // Verificar si el usuario está logueado
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        
        $this->load->view('importar_excel');
    }

    // Método para descargar una plantilla de ejemplo
    public function descargar_plantilla() {
        try {
            // Crear un nuevo objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Agregar encabezados
            $sheet->setCellValue('A1', 'Nro. Inventario')
                  ->setCellValue('B1', 'Nro. Serie')
                  ->setCellValue('C1', 'Descripción')
                  ->setCellValue('D1', 'Modelo')
                  ->setCellValue('E1', 'Marca')
                  ->setCellValue('F1', 'Familia')
                  ->setCellValue('G1', 'Ubicación')
                  ->setCellValue('H1', 'Observación');
            
            // Dar formato a los encabezados (negrita)
            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            
            // Agregar datos de ejemplo
            $sheet->setCellValue('A2', 'INV-001')
                  ->setCellValue('B2', 'SN12345678')
                  ->setCellValue('C2', 'Laptop Dell Latitude')
                  ->setCellValue('D2', 'E7450')
                  ->setCellValue('E2', 'Dell')
                  ->setCellValue('F2', 'Computadoras')
                  ->setCellValue('G2', 'Oficina Principal')
                  ->setCellValue('H2', 'Equipo nuevo');
            
            // Agregar otro ejemplo
            $sheet->setCellValue('A3', 'INV-002')
                  ->setCellValue('B3', 'IMEI87654321')
                  ->setCellValue('C3', 'Smartphone Samsung')
                  ->setCellValue('D3', 'Galaxy S21')
                  ->setCellValue('E3', 'Samsung')
                  ->setCellValue('F3', 'Teléfonos')
                  ->setCellValue('G3', 'Departamento Ventas')
                  ->setCellValue('H3', 'Equipo de reemplazo');
            
            // Ajustar el ancho de las columnas automáticamente
            foreach(range('A','H') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Renombrar la hoja
            $sheet->setTitle('Plantilla Artículos');
            
            // Redirigir la salida al navegador
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="plantilla_importacion.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            // Registrar el error
            log_message('error', 'Error al generar plantilla Excel: ' . $e->getMessage());
            
            // Mostrar un mensaje de error
            echo "Error al generar la plantilla: " . $e->getMessage();
            exit;
        }
    }

    // Método para procesar la importación del archivo Excel
    public function procesar_importacion() {
        // Verificar si el usuario está logueado
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        
        // Verificar si se ha enviado un archivo
        if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] != 0) {
            $this->session->set_flashdata('mensaje', 'No se ha seleccionado un archivo o ha ocurrido un error al subirlo.');
            $this->session->set_flashdata('tipo_mensaje', 'danger');
            redirect('inventario/importar_excel');
            return;
        }
        
        // Configurar la carga de archivos
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;
        
        // Crear la carpeta de uploads si no existe
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('archivo_excel')) {
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('mensaje', 'Error al subir el archivo: ' . $error);
            $this->session->set_flashdata('tipo_mensaje', 'danger');
            redirect('inventario/importar_excel');
            return;
        }
        
        $data = $this->upload->data();
        $archivo = $config['upload_path'] . $data['file_name'];
        
        // Suprimir los "Notice" de PHP
        $old_error_reporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE);
        
        try {
            // Cargar el archivo Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            
            // Obtener parámetros del formulario
            $hoja = $this->input->post('hoja');
            $fila_inicio = $this->input->post('fila_inicio');
            $modo_importacion = $this->input->post('modo_importacion');
            
            // Asegurarse de que $modo_importacion tenga un valor válido
            if ($modo_importacion !== 'actualizar') {
                $modo_importacion = true; // Por defecto, permitir crear nuevos artículos
            }

            // Seleccionar la hoja adecuada
            if (empty($hoja)) {
                $hoja_activa = $spreadsheet->getActiveSheet();
            } else {
                $hoja_activa = $spreadsheet->getSheetByName($hoja);
                if (!$hoja_activa) {
                    throw new Exception("La hoja '$hoja' no existe en el archivo Excel.");
                }
            }
            
            // Validar fila de inicio
            if (empty($fila_inicio) || !is_numeric($fila_inicio) || $fila_inicio < 1) {
                $fila_inicio = 2; // Por defecto, empezar desde la fila 2 (asumiendo encabezados en fila 1)
            }
            
            // Leer los datos del Excel
            $datos_excel = array();
            $highestRow = $hoja_activa->getHighestDataRow();
            
            for ($row = $fila_inicio; $row <= $highestRow; $row++) {
                $nro_inventario = $hoja_activa->getCellByColumnAndRow(1, $row)->getValue();
                $nroserie = $hoja_activa->getCellByColumnAndRow(2, $row)->getValue();
                $descripcion = $hoja_activa->getCellByColumnAndRow(3, $row)->getValue();
                $modelo = $hoja_activa->getCellByColumnAndRow(4, $row)->getValue();
                $marca = $hoja_activa->getCellByColumnAndRow(5, $row)->getValue();
                $familia = $hoja_activa->getCellByColumnAndRow(6, $row)->getValue();
                $ubicacion = $hoja_activa->getCellByColumnAndRow(7, $row)->getValue();
                $observacion = $hoja_activa->getCellByColumnAndRow(8, $row)->getValue();
                
                // Si todas las celdas están vacías, omitir esta fila
                if (empty($nroserie) && empty($descripcion) && empty($modelo) && empty($marca) && empty($familia)) {
                    continue;
                }
                
                $datos_excel[] = array(
                    'nro_inventario' => $nro_inventario,
                    'nroserie' => $nroserie,
                    'descripcion' => $descripcion,
                    'modelo' => $modelo,
                    'marca' => $marca,
                    'familia' => $familia,
                    'ubicacion' => $ubicacion,
                    'observacion' => $observacion
                );
            }
            
            // Restaurar el nivel de error reporting
            error_reporting($old_error_reporting);
            
            // Eliminar el archivo temporal
            unlink($archivo);
            
            // Si no hay datos, mostrar error
            if (empty($datos_excel)) {
                $this->session->set_flashdata('mensaje', 'No se encontraron datos para importar en el archivo Excel.');
                $this->session->set_flashdata('tipo_mensaje', 'warning');
                redirect('inventario/importar_excel');
                return;
            }
            
            // Procesar los datos con el modelo
            $resultados = $this->Inventario_model->importarArticulos($datos_excel, $modo_importacion);
                    
            // Preparar mensaje de resultado
            $mensaje = "Importación completada. ";
            $mensaje .= "Insertados: " . $resultados['insertados'] . ", ";
            $mensaje .= "Actualizados: " . $resultados['actualizados'] . ", ";
            $mensaje .= "Errores: " . $resultados['errores'];
            
            $this->session->set_flashdata('mensaje', $mensaje);
            $this->session->set_flashdata('tipo_mensaje', 'success');
            
            // Si hay errores, guardarlos en la sesión
            if (!empty($resultados['mensajes_error'])) {
                $this->session->set_flashdata('errores', $resultados['mensajes_error']);
            }
            
            // Guardar resumen para mostrar en la vista
            $this->session->set_flashdata('resumen', array(
                'total' => $resultados['total'],
                'exitosos' => $resultados['insertados'] + $resultados['actualizados'],
                'errores' => $resultados['errores']
            ));
            
            redirect('inventario/importar_excel');
            
        } catch (Exception $e) {
            // Restaurar el nivel de error reporting
            error_reporting($old_error_reporting);
            
            // Eliminar el archivo temporal si existe
            if (file_exists($archivo)) {
                unlink($archivo);
            }
            
            $this->session->set_flashdata('mensaje', 'Error al procesar el archivo Excel: ' . $e->getMessage());
            $this->session->set_flashdata('tipo_mensaje', 'danger');
            redirect('inventario/importar_excel');
        }
    }

    public function test_ruta() {
        echo "La ruta funciona correctamente.";
        echo "<br>Base URL: " . base_url();
        echo "<br>Site URL: " . site_url();
        exit;
    }

    // Método para pruebas AJAX
    public function test_ajax() {
        echo json_encode(['status' => 'success', 'message' => 'La conexión AJAX funciona correctamente']);
    }
    
    // Método para filtrar artículos retirados
    public function filtrar_retirados() {
        $marca = $this->input->post('marca');
        $familia = $this->input->post('familia');
        $busqueda = $this->input->post('busqueda');
        
        $articulos = $this->Inventario_model->filtrarArticulosRetirados($marca, $familia, $busqueda);
        
        echo json_encode(array('articulos' => $articulos));
    }
    
    // Método para exportar artículos retirados filtrados
    public function exportar_retirados_filtrados($formato = 'excel') {
        $marca = $this->input->post('marca');
        $familia = $this->input->post('familia');
        $busqueda = $this->input->post('busqueda');
        
        $this->load->model('Inventario_model');
        $articulos = $this->Inventario_model->filtrarArticulosRetirados($marca, $familia, $busqueda);
        
        if ($formato == 'excel') {
            // Exportar a Excel
            $this->exportar_retirados_filtrados_excel($articulos);
        } else if ($formato == 'pdf') {
            // Exportar a PDF
            $this->exportar_retirados_filtrados_pdf($articulos);
        } else {
            show_error('Formato de exportación no válido');
        }
    }
    
    // Método para exportar artículos retirados filtrados a Excel
    private function exportar_retirados_filtrados_excel($articulos) {
        // Cargar la librería para Excel
        // Try multiple possible paths for PHPExcel
        $possible_paths = [
            APPPATH . 'third_party/PHPExcel/PHPExcel.php',
            APPPATH . 'libraries/PHPExcel/PHPExcel.php',
            APPPATH . '../vendor/phpoffice/phpexcel/Classes/PHPExcel.php',
            // Add more potential paths if needed
        ];
        
        $loaded = false;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $loaded = true;
                break;
            }
        }
        
        if (!$loaded) {
            show_error('No se pudo cargar la librería PHPExcel. Por favor, verifique que esté instalada correctamente.');
            return;
        }
        
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
                    ->setCellValue('B1', 'Nro. Inventario')
                    ->setCellValue('C1', 'Marca')
                    ->setCellValue('D1', 'Familia')
                    ->setCellValue('E1', 'Fecha de Retiro')
                    ->setCellValue('F1', 'Motivo');
        
        // Agregar datos
        $row = 2;
        foreach ($articulos as $articulo) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, $articulo->inventario_interno)
                        ->setCellValue('B' . $row, $articulo->nro_inventario)
                        ->setCellValue('C' . $row, $articulo->marca_nombre)
                        ->setCellValue('D' . $row, $articulo->familia_nombre)
                        ->setCellValue('E' . $row, $articulo->fecha_retiro)
                        ->setCellValue('F' . $row, $articulo->motivo_retiro);
            $row++;
        }
        
        // Ajustar el ancho de las columnas automáticamente
        foreach(range('A','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Renombrar la hoja
        $objPHPExcel->getActiveSheet()->setTitle('Artículos Retirados');
        
        // Establecer la hoja activa
        $objPHPExcel->setActiveSheetIndex(0);
        
        // Redirigir la salida al navegador
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="articulos_retirados_filtrados.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    // Método para exportar artículos retirados filtrados a PDF
    private function exportar_retirados_filtrados_pdf($articulos) {
        try {
            // Try multiple possible paths for TCPDF
            $possible_paths = [
                APPPATH . 'third_party/tcpdf/tcpdf.php',
                APPPATH . 'libraries/tcpdf/tcpdf.php',
                APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php',
                // Add more potential paths if needed
            ];
            
            $loaded = false;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $loaded = true;
                    break;
                }
            }
            
            if (!$loaded) {
                show_error('No se pudo cargar la librería TCPDF. Por favor, verifique que esté instalada correctamente.');
                return;
            }
            
            // Crear un nuevo documento PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Establecer información del documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema de Inventario');
            $pdf->SetTitle('Artículos Retirados Filtrados');
            $pdf->SetSubject('Artículos Retirados Filtrados');
            
            // Establecer márgenes
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            
            // Agregar una página
            $pdf->AddPage('L'); // Landscape orientation
            
            // Contenido HTML para el PDF
            $html = '<h1>Lista de Artículos Retirados (Filtrados)</h1>';
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th>ID</th>';
            $html .= '<th>Nro. Inventario</th>';
            $html .= '<th>Marca</th>';
            $html .= '<th>Familia</th>';
            $html .= '<th>Fecha de Retiro</th>';
            $html .= '<th>Motivo</th>';
            $html .= '</tr>';
            
            if ($articulos) {
                foreach ($articulos as $articulo) {
                    $html .= '<tr>';
                    $html .= '<td>' . $articulo->inventario_interno . '</td>';
                    $html .= '<td>' . $articulo->nro_inventario . '</td>';
                    $html .= '<td>' . $articulo->marca_nombre . '</td>';
                    $html .= '<td>' . $articulo->familia_nombre . '</td>';
                    $html .= '<td>' . $articulo->fecha_retiro . '</td>';
                    $html .= '<td>' . $articulo->motivo_retiro . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td colspan="6" style="text-align:center;">No se encontraron artículos retirados con los filtros aplicados</td></tr>';
            }
            
            $html .= '</table>';
            
            // Escribir el HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Cerrar y generar el PDF
            $pdf->Output('articulos_retirados_filtrados.pdf', 'D');
            exit;
        } catch (Exception $e) {
            show_error('Error al exportar a PDF: ' . $e->getMessage());
        }
    }
}
