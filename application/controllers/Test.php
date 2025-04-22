<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    
    public function index() {
        echo "Controlador de prueba funcionando correctamente.";
    }
    
    public function info() {
        phpinfo();
    }
    
    public function session_test() {
        $this->load->library('session');
        echo "<pre>";
        echo "Session data: ";
        print_r($this->session->userdata());
        echo "</pre>";
        
        echo "<p>Para simular un inicio de sesión, <a href='".site_url('test/login_sim')."'>haz clic aquí</a></p>";
    }
    
    public function login_sim() {
        $this->load->library('session');
        
        // Simular datos de sesión
        $datos_sesion = array(
            'id_usuario' => 1,
            'nombre' => 'Usuario de Prueba',
            'rol' => 'admin',
            'logged_in' => TRUE
        );
        
        // Establecer sesión
        $this->session->set_userdata($datos_sesion);
        
        echo "Sesión iniciada correctamente. <a href='".site_url('inicio')."'>Ir a inicio</a>";
    }
    
    public function redirect_test() {
        redirect('inicio');
    }
    
    public function base_url_test() {
        echo "base_url(): " . base_url() . "<br>";
        echo "site_url('inicio'): " . site_url('inicio') . "<br>";
        echo "current_url(): " . current_url() . "<br>";
    }
}