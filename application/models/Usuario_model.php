<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function validar_usuario($rut, $password) {
        // Buscar usuario por RUT
        $this->db->select('usuario.*, rol.rol_usuario as nombre_rol');
        $this->db->from('usuario');
        $this->db->join('rol', 'usuario.id_rol = rol.id', 'left');
        $this->db->where('usuario.rut', $rut);
        $query = $this->db->get();
        
        if ($query->num_rows() == 1) {
            $usuario = $query->row();
            
            // Verificar contraseña
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