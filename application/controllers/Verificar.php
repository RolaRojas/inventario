<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verificar extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        echo "<h1>Verificación de la tabla usuario</h1>";
        
        // Verificar si la tabla existe
        if ($this->db->table_exists('usuario')) {
            echo "<p style='color:green'>La tabla 'usuario' existe.</p>";
            
            // Obtener la estructura de la tabla
            $fields = $this->db->field_data('usuario');
            
            echo "<h2>Estructura de la tabla:</h2>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Longitud máxima</th></tr>";
            
            foreach ($fields as $field) {
                echo "<tr>";
                echo "<td>" . $field->name . "</td>";
                echo "<td>" . $field->type . "</td>";
                echo "<td>" . $field->max_length . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Mostrar los primeros 5 registros
            echo "<h2>Primeros 5 registros:</h2>";
            
            $query = $this->db->limit(5)->get('usuario');
            
            if ($query->num_rows() > 0) {
                echo "<table border='1' cellpadding='5'>";
                
                // Encabezados de la tabla
                echo "<tr>";
                foreach ($fields as $field) {
                    echo "<th>" . $field->name . "</th>";
                }
                echo "</tr>";
                
                // Datos
                foreach ($query->result_array() as $row) {
                    echo "<tr>";
                    foreach ($fields as $field) {
                        echo "<td>" . (isset($row[$field->name]) ? $row[$field->name] : '') . "</td>";
                    }
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>No hay registros en esta tabla.</p>";
            }
        } else {
            echo "<p style='color:red'>La tabla 'usuario' no existe.</p>";
        }
    }
}