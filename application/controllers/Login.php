<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
    }

    public function index() {
        // Si ya hay sesión, ir a inicio según el rol
        if ($this->session->userdata('logged_in')) {
            if ($this->session->userdata('id_rol') == 1) {
                redirect('inicio');
            } else {
                redirect('inicio_visita');
            }
        }
        
        $this->load->view('login');
    }

    public function validar_login() {
        // Validar el formulario
        $this->form_validation->set_rules('rut', 'RUT', 'required');
        $this->form_validation->set_rules('password', 'Contraseña', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver al login con errores
            $this->load->view('login');
        } else {
            // Obtener datos del formulario
            $rut = $this->input->post('rut');
            $password = $this->input->post('password');
            
            // Verificar credenciales
            $usuario = $this->Usuario_model->validar_usuario($rut, $password);
            
            if ($usuario) {
                // Crear datos de sesión
                $datos_sesion = array(
                    'id_usuario' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'id_rol' => $usuario->id_rol, // Usar id_rol en lugar de rol
                    'logged_in' => TRUE
                );
                
                // Establecer sesión
                $this->session->set_userdata($datos_sesion);
                
                // Redirigir según el rol
                if ($usuario->id_rol == 1) {
                    redirect('inicio');
                } else if ($usuario->id_rol == 2) {
                    redirect('inicio_visita');
                } else {
                    // Por defecto, redirigir a inicio
                    redirect('inicio');
                }
            } else {
                // Credenciales incorrectas
                $data['error'] = 'RUT o contraseña incorrectos';
                $this->load->view('login', $data);
            }
        }
    }
    
    public function cerrar_sesion() {
        // Destruir sesión
        $this->session->sess_destroy();


        // Redirigir al login
        redirect('login');
    }
}