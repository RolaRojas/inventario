<?php 

class Inventario_model extends CI_Model {  

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function getarticulo() {
        // Modificar la consulta para no usar id_ubicacion si no existe
        $this->db->select("a.*, m.nombre as marca_nombre, f.nombre as familia_nombre");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->select("u.nombre as ubicacion_nombre");
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
        } else {
            // Si no existe, usar un valor predeterminado
            $this->db->select("'No especificada' as ubicacion_nombre");
        }
        
        return $this->db->get()->result();
    }

    public function lista_inicio() {
        $query = "SELECT articulo.inventario_interno, articulo.nroserie, familia.nombre as familia, marca.nombre as marca 
        FROM `articulo` 
        inner join marca on articulo.id_marca = marca.id
        inner join familia on articulo.id_familia = familia.id";
        return $this->db->query($query);
    }

    // Mantener el método para el gráfico
    public function obtenerResumenGrafico() {
        $query = $this->db->query("
            SELECT familia.nombre AS categoria, COUNT(*) AS cantidad
            FROM articulo
            INNER JOIN familia ON articulo.id_familia = familia.id
            GROUP BY familia.nombre
        ");
        return $query->result();
    }

    // Obtener todas las marcas
    public function getMarcas() {
        return $this->db->get('marca')->result();
    }
    
    // Obtener todas las familias
    public function getFamilias() {
        return $this->db->get('familia')->result();
    }
    
    // Obtener todas las ubicaciones
    public function getUbicaciones() {
        return $this->db->get('ubicacion')->result();
    }
    
    // Agregar un nuevo artículo
    public function agregarArticulo($datos) {
        $this->db->insert('articulo', $datos);
        return $this->db->insert_id();
    }
    
    // Agregar registro en historial
    public function agregarHistorial($datos_historial) {
        return $this->db->insert('historial', $datos_historial);
    }
    
    // Obtener el siguiente inventario_interno
    public function getSiguienteInventarioInterno() {
        $this->db->select_max('inventario_interno');
        $query = $this->db->get('articulo');
        $resultado = $query->row();
        
        if ($resultado && $resultado->inventario_interno) {
            return $resultado->inventario_interno + 1;
        }
        return 1; // Si no hay registros, empezar desde 1
    }
    
    // Modificar un artículo
    public function modificarArticulo($id, $datos) {
        $this->db->where('id', $id);
        return $this->db->update('articulo', $datos);
    }
    
    // Obtener un artículo por ID
    public function getArticuloPorId($id) {
        // Modificar la consulta para no usar id_ubicacion si no existe
        $this->db->select("a.*, m.nombre as marca_nombre, f.nombre as familia_nombre");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->select("u.nombre as ubicacion_nombre");
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
        } else {
            // Si no existe, usar un valor predeterminado
            $this->db->select("'No especificada' as ubicacion_nombre");
        }
        
        $this->db->where("a.id", $id);
        return $this->db->get()->row();
    }
    
    // Nuevo método para obtener artículos retirados
    public function getArticulosRetirados() {
        // Eliminar la referencia a la columna cantidad que no existe
        $this->db->select("a.inventario_interno as id, a.inventario_interno, a.nroserie as nombre, f.nombre as categoria, 
                          a.fecha_retiro, a.motivo_retiro, a.descripcion, a.observacion,
                          m.nombre as marca");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
            $this->db->where("u.nombre", "retirados");
        } else {
            // Si no existe, usar otro criterio para identificar artículos retirados
            // Por ejemplo, si tienes un campo estado o similar
            $this->db->where("a.estado", "retirado");
        }
        
        return $this->db->get()->result();
    }
    
    // También actualiza el método getArticuloRetiradoPorId
    public function getArticuloRetiradoPorId($id) {
        // Eliminar la referencia a la columna cantidad que no existe
        $this->db->select("a.inventario_interno as id, a.inventario_interno, a.nroserie as nombre, f.nombre as categoria, 
                          a.fecha_retiro, a.motivo_retiro, a.descripcion, a.observacion,
                          m.nombre as marca");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
            $this->db->where("u.nombre", "retirados");
        } else {
            // Si no existe, usar otro criterio para identificar artículos retirados
            $this->db->where("a.estado", "retirado");
        }
        
        $this->db->where("a.inventario_interno", $id);
        return $this->db->get()->row();
    }
    
    // Método para exportar artículos a Excel
    public function exportarExcel() {
        // Modificar para trabajar sin id_ubicacion si no existe
        $this->db->select("
            a.inventario_interno as ID, 
            a.nro_inventario, 
            m.nombre as marca, 
            f.nombre as familia
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->select("u.nombre as ubicacion");
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
        } else {
            // Si no existe, usar un valor predeterminado
            $this->db->select("'No especificada' as ubicacion");
        }
        
        return $this->db->get()->result();
    }
    
    // Método para exportar artículos retirados a Excel
    public function exportarRetiradosExcel() {
        // Modificar para trabajar sin id_ubicacion si no existe
        $this->db->select("
            a.id,
            a.inventario_interno, 
            a.nroserie as nombre, 
            f.nombre as categoria, 
            a.cantidad,
            a.fecha_retiro,
            a.motivo_retiro
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Verificar si existe la columna id_ubicacion
        if ($this->db->field_exists('id_ubicacion', 'articulo')) {
            $this->db->select("u.nombre as ubicacion");
            $this->db->join("ubicacion u", "a.id_ubicacion = u.id", "left");
            $this->db->where("u.nombre", "retirados");
        } else {
            // Si no existe, filtrar por otro campo que indique que está retirado
            $this->db->where("a.estado", "retirado");
        }
        
        return $this->db->get()->result();
    }
}