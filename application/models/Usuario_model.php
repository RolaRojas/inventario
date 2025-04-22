<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function validar_usuario($rut, $password) {
        // Buscar usuario por RUT
        $this->db->where('rut', $rut);
        $query = $this->db->get('usuario');
        
        if ($query->num_rows() == 1) {
            $usuario = $query->row();
            
            // Verificar contraseña usando el campo "clave" en lugar de "password"
            if ($password == $usuario->clave) {
                return $usuario;
            }
        }
        
        return false;
    }

    public function obtener_usuario($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('usuario');
        
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        
        return false;
    }
}