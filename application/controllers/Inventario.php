<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
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
		$this->load->model('Inventario_model');
		$data ['articulos'] = $this->Inventario_model->lista_inicio()->result();
		$data['data_grafico'] = $this->Inventario_model->lista_inicio()->result();
		//echo json_encode($data ['articulos']);
		$this->load->view('inicio',$data);
//		echo json_encode($this->Inventario_model->getarticulo());
//		$this->load->view('inicio');
	}

	
	public function recuperar()
	{
		$this->load->view('recuperar_clave');
	}

    public function ingresar() {
        // Cargar datos necesarios para la vista
        $data['articulos'] = $this->Inventario_model->getarticulo();
        $data['marcas'] = $this->Inventario_model->getMarcas();
        $data['familias'] = $this->Inventario_model->getFamilias();
        $data['ubicaciones'] = $this->Inventario_model->getUbicaciones();
        $data['siguiente_inventario'] = $this->Inventario_model->getSiguienteInventarioInterno();
        
        $this->load->view('ingresar', $data);
    }

/*---------------------------------------------------------------*/
public function agregar() {
        // Validar formulario
        $this->form_validation->set_rules('nro_inventario', 'Número de Inventario', 'required');
        $this->form_validation->set_rules('nroserie', 'Número de Serie', 'required');
        $this->form_validation->set_rules('descripcion', 'Descripción', 'required');
        $this->form_validation->set_rules('modelo', 'Modelo', 'required');
        $this->form_validation->set_rules('id_marca', 'Marca', 'required|numeric');
        $this->form_validation->set_rules('id_familia', 'Familia', 'required|numeric');
        $this->form_validation->set_rules('id_ubicacion', 'Ubicación', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver al formulario con errores
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
                'id_familia' => $this->input->post('id_familia'),
                'id_ubicacion' => $this->input->post('id_ubicacion'),
                'fecha_ingreso' => date('Y-m-d H:i:s')
            );
            
            // Insertar artículo
            $id_articulo = $this->Inventario_model->agregarArticulo($datos);
            
            if ($id_articulo) {
                // Registrar en historial
                $datos_historial = array(
                    'id_articulo' => $id_articulo,
                    'id_ubicacion' => $this->input->post('id_ubicacion'),
                    'fecha' => date('Y-m-d H:i:s')
                );
                
                $this->Inventario_model->agregarHistorial($datos_historial);
                
                // Mensaje de éxito
                $this->session->set_flashdata('mensaje', 'Artículo agregado correctamente');
                $this->session->set_flashdata('tipo_mensaje', 'success');
                
                // Devolver datos del artículo para mostrar en el popup
                $articulo = $this->Inventario_model->getArticuloPorId($id_articulo);
                
                // Si es una solicitud AJAX
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array(
                        'success' => true,
                        'articulo' => $articulo
                    ));
                    return;
                }
            } else {
                // Mensaje de error
                $this->session->set_flashdata('mensaje', 'Error al agregar el artículo');
                $this->session->set_flashdata('tipo_mensaje', 'error');
                
                // Si es una solicitud AJAX
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('success' => false));
                    return;
                }
            }
            
            // Redireccionar
            redirect('ingresar');
        }
    }
/*-----------------------------------------------------------------*/

	public function retirados()
	{
		$this->load->model('Inventario_model');
		// Cambiamos para usar el nuevo método específico para artículos retirados
		$data['articulos'] = $this->Inventario_model->getArticulosRetirados();
		$this->load->view('retirados', $data);
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
	
	// Método para exportar artículos retirados
	public function exportar_retirados($formato = 'excel')
	{
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
					->setCellValue('E1', 'Cantidad')
					->setCellValue('F1', 'Fecha de Retiro')
					->setCellValue('G1', 'Motivo');
		
		// Agregar datos
		$row = 2;
		foreach ($articulos as $articulo) {
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A' . $row, $articulo->id)
						->setCellValue('B' . $row, $articulo->inventario_interno)
						->setCellValue('C' . $row, $articulo->nombre)
						->setCellValue('D' . $row, $articulo->categoria)
						->setCellValue('E' . $row, $articulo->cantidad)
						->setCellValue('F' . $row, $articulo->fecha_retiro)
						->setCellValue('G' . $row, $articulo->motivo_retiro);
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
	private function exportar_pdf_retirados()
	{
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
		$html .= '<tr><th>ID</th><th>Inventario Interno</th><th>Nombre</th><th>Categoría</th><th>Cantidad</th><th>Fecha de Retiro</th><th>Motivo</th></tr>';
		
		foreach ($articulos as $articulo) {
			$html .= '<tr>';
			$html .= '<td>' . $articulo->id . '</td>';
			$html .= '<td>' . $articulo->inventario_interno . '</td>';
			$html .= '<td>' . $articulo->nombre . '</td>';
			$html .= '<td>' . $articulo->categoria . '</td>';
			$html .= '<td>' . $articulo->cantidad . '</td>';
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
//		echo json_encode($this->Inventario_model->getarticulo());
//		$this->load->view('inicio');
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



}