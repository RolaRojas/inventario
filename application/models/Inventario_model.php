<?php 

class Inventario_model extends CI_Model {  

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function getarticulo() {
        // Consulta básica sin subconsulta para ubicación
        $this->db->select("a.*, m.nombre as marca_nombre, f.nombre as familia_nombre");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Excluir artículos retirados
        $this->db->where("a.fecha_retiro IS NULL");
        
        // Ordenar por inventario_interno en orden ascendente
        $this->db->order_by('a.inventario_interno', 'ASC');
        
        $articulos = $this->db->get()->result();
        $articulos_filtrados = array();
        
        // Ahora obtenemos las ubicaciones en un bucle separado
        foreach ($articulos as $articulo) {
            $this->db->select("u.nombre as ubicacion_nombre, u.id as id_ubicacion");
            $this->db->from("historial h");
            $this->db->join("ubicacion u", "h.id_ubicacion = u.id", "left");
            $this->db->where("h.id_articulo", $articulo->inventario_interno);
            $this->db->order_by("h.fecha", "DESC");
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $ubicacion = $query->row();
                $articulo->ubicacion_nombre = $ubicacion->ubicacion_nombre;
                $articulo->id_ubicacion = $ubicacion->id_ubicacion;
                
                // Solo incluir el artículo si su ubicación NO es "retirados"
                if (strtolower($ubicacion->ubicacion_nombre) !== 'retirados') {
                    $articulos_filtrados[] = $articulo;
                }
            } else {
                $articulo->ubicacion_nombre = "No especificada";
                $articulo->id_ubicacion = null;
                // Incluir artículos sin ubicación especificada
                $articulos_filtrados[] = $articulo;
            }
        }
        
        return $articulos_filtrados;
    }

    public function lista_inicio() {
        $query = "SELECT articulo.inventario_interno, articulo.nroserie, familia.nombre as familia, marca.nombre as marca 
        FROM `articulo` 
        inner join marca on articulo.id_marca = marca.id
        inner join familia on articulo.id_familia = familia.id
        where articulo.fecha_retiro IS NULL
        ORDER BY articulo.inventario_interno ASC";
        return $this->db->query($query);
    }

    // Mantener el método para el gráfico
    public function obtenerResumenGrafico() {
        $query = $this->db->query("
            SELECT familia.nombre AS categoria, COUNT(*) AS cantidad
            FROM articulo
            INNER JOIN familia ON articulo.id_familia = familia.id
            where articulo.fecha_retiro IS NULL
            GROUP BY familia.nombre
        ");
        return $query->result();
    }
    //$this->db->where("a.fecha_retiro IS NULL");

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
        $fecha_actual = $datos['fecha_ingreso'] = date('d-m-Y'); // Agregar fecha de creación
        $fechamysql = date('Y-m-d', strtotime($fecha_actual)); // Convertir a formato MySQL
        $datos['fecha_ingreso'] = $fechamysql; // Asignar la fecha convertida
        $this->db->insert('articulo', $datos);
        return $this->db->insert_id();
    }
    
    // Agregar registro en historial
    public function agregarHistorial($datos_historial) {
        // Verificar si ya existe un registro con el mismo id_articulo
        $this->db->where('id_articulo', $datos_historial['id_articulo']);
        $this->db->order_by('fecha', 'DESC');
        $this->db->limit(1);
        $ultimo_historial = $this->db->get('historial')->row();
        
        // Si existe un registro previo y tiene la misma ubicación, no agregar uno nuevo
        if ($ultimo_historial && $ultimo_historial->id_ubicacion == $datos_historial['id_ubicacion']) {
            return false;
        }
        
        // Insertar el nuevo registro en el historial
        $result = $this->db->insert('historial', $datos_historial);
        return $result;
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
        // Primero intentar con inventario_interno
        $this->db->where('inventario_interno', $id);
        $result = $this->db->update('articulo', $datos);
        
        // Si no se actualizó ninguna fila, intentar con id
        if ($this->db->affected_rows() == 0) {
            $this->db->where('id', $id);
            $result = $this->db->update('articulo', $datos);
        }
        
        return $result;
    }
    
    // Obtener un artículo por ID
    public function getArticuloPorId($id) {
        // Registrar información de depuración
        log_message('debug', 'Modelo: Buscando artículo con ID: ' . $id);
        
        // Consulta principal para obtener datos del artículo
        $this->db->select('a.*, m.nombre as marca_nombre, f.nombre as familia_nombre');
        $this->db->from('articulo a');
        $this->db->join('marca m', 'm.id = a.id_marca', 'left');
        $this->db->join('familia f', 'f.id = a.id_familia', 'left');
        $this->db->where('a.inventario_interno', $id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            log_message('debug', 'Modelo: No se encontró artículo con ID: ' . $id);
            return null;
        }
        
        $articulo = $query->row_array();
        
        // Obtener la ubicación actual - CORREGIDO: usar inventario_interno en lugar de id
        $this->db->select('h.id_ubicacion, u.nombre as ubicacion_nombre');
        $this->db->from('historial h');
        $this->db->join('ubicacion u', 'u.id = h.id_ubicacion', 'left');
        $this->db->where('h.id_articulo', $id); // Usar el ID pasado como parámetro
        $this->db->order_by('h.fecha', 'DESC');
        $this->db->limit(1);
        
        $query_ubicacion = $this->db->get();
        
        if ($query_ubicacion->num_rows() > 0) {
            $ubicacion = $query_ubicacion->row_array();
            $articulo['id_ubicacion'] = $ubicacion['id_ubicacion'];
            $articulo['ubicacion_nombre'] = $ubicacion['ubicacion_nombre'];
        }
        
        log_message('debug', 'Modelo: Artículo encontrado: ' . json_encode($articulo));
        
        return $articulo;
    }

    // Obtener la ubicación actual de un artículo
    public function getUbicacionActual($id_articulo) {
        $this->db->select('h.id_ubicacion, u.nombre as ubicacion_nombre');
        $this->db->from('historial h');
        $this->db->join('ubicacion u', 'h.id_ubicacion = u.id', 'left');
        $this->db->where('h.id_articulo', $id_articulo);
        $this->db->order_by('h.fecha', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }

    // Modificar un artículo por inventario_interno
    public function modificarArticuloPorInventarioInterno($inventario_interno, $datos) {
        $this->db->where('inventario_interno', $inventario_interno);
        $this->db->update('articulo', $datos);
        
        // Devolver true si se actualizó al menos una fila
        return ($this->db->affected_rows() > 0);
    }
    
    // Nuevo método para obtener artículos retirados
    public function getArticulosRetirados() {
        // Add debug logging
        log_message('debug', 'Executing getArticulosRetirados method');
        
        // Simplified query to ensure we're getting results
        $this->db->select("
            articulo.inventario_interno,
            articulo.nro_inventario, 
            articulo.nroserie, 
            articulo.descripcion, 
            articulo.modelo, 
            marca.nombre as marca_nombre,
            familia.nombre as familia_nombre,
            articulo.fecha_retiro,
            articulo.motivo_retiro");
        $this->db->from("articulo");
        $this->db->join("marca", "marca.id = articulo.id_marca", "left");
        $this->db->join("familia", "familia.id = articulo.id_familia", "left");
        $this->db->where("articulo.fecha_retiro IS NOT NULL");
        $this->db->order_by("articulo.fecha_retiro", "DESC");
        
        $query = $this->db->get();
        
        // Log the SQL query and result count for debugging
        log_message('debug', 'SQL Query: ' . $this->db->last_query());
        log_message('debug', 'Result count: ' . $query->num_rows());
        
        return $query->result();
    }
    
    // También actualiza el método getArticuloRetiradoPorId
    public function getArticuloRetiradoPorId($id) {
        $this->db->select("a.inventario_interno as id, a.inventario_interno, a.nroserie as nombre, 
                          a.descripcion as categoria, a.fecha_retiro, a.motivo_retiro, 
                          a.descripcion, a.observacion, m.nombre as marca");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Usar fecha_retiro IS NOT NULL en lugar de estado = 'retirado'
        $this->db->where("a.fecha_retiro IS NOT NULL");
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
        $this->db->select("
            a.inventario_interno as id,
            a.inventario_interno, 
            a.nroserie as nombre, 
            a.descripcion as categoria, 
            a.fecha_retiro,
            a.motivo_retiro
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Usar fecha_retiro IS NOT NULL en lugar de estado = 'retirado'
        $this->db->where("a.fecha_retiro IS NOT NULL");
        
        return $this->db->get()->result();
    }

    // Método para filtrar artículos
    public function filtrarArticulos($marca = null, $familia = null, $busqueda = null) {
        $this->db->select("a.*, m.nombre as marca_nombre, f.nombre as familia_nombre");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Aplicar filtros si se proporcionan
        if (!empty($marca)) {
            $this->db->where('a.id_marca', $marca);
        }
        
        if (!empty($familia)) {
            $this->db->where('a.id_familia', $familia);
        }
        
        if (!empty($busqueda)) {
            $this->db->group_start();
            $this->db->like('a.nro_inventario', $busqueda);
            $this->db->or_like('a.nroserie', $busqueda);
            $this->db->or_like('a.descripcion', $busqueda);
            $this->db->or_like('a.modelo', $busqueda);
            $this->db->or_like('m.nombre', $busqueda);
            $this->db->or_like('f.nombre', $busqueda);
            $this->db->group_end();
        }
        
        // Excluir artículos retirados
        $this->db->where("a.fecha_retiro IS NULL");
        
        // Ordenar por inventario_interno
        $this->db->order_by('a.inventario_interno', 'ASC');
        
        $articulos = $this->db->get()->result();
        $articulos_con_ubicacion = array();
        
        // Obtener la ubicación actual para cada artículo
        foreach ($articulos as $articulo) {
            $this->db->select("u.nombre as ubicacion_nombre, u.id as id_ubicacion");
            $this->db->from("historial h");
            $this->db->join("ubicacion u", "h.id_ubicacion = u.id", "left");
            $this->db->where("h.id_articulo", $articulo->inventario_interno);
            $this->db->order_by("h.fecha", "DESC");
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $ubicacion = $query->row();
                $articulo->ubicacion_nombre = $ubicacion->ubicacion_nombre;
                $articulo->id_ubicacion = $ubicacion->id_ubicacion;
                
                // Solo incluir el artículo si su ubicación NO es "retirados"
                if (strtolower($ubicacion->ubicacion_nombre) !== 'retirados') {
                    $articulos_con_ubicacion[] = $articulo;
                }
            } else {
                $articulo->ubicacion_nombre = "No especificada";
                $articulo->id_ubicacion = null;
                // Incluir artículos sin ubicación especificada
                $articulos_con_ubicacion[] = $articulo;
            }
        }
        
        return $articulos_con_ubicacion;
    }

    // Método para exportar artículos a Excel con ubicación actual
    public function exportarExcelConUbicacion() {
        // Consulta principal para obtener los artículos
        $this->db->select("
            a.inventario_interno, 
            a.nro_inventario, 
            a.nroserie,
            a.descripcion,
            a.modelo,
            a.observacion,
            m.nombre as marca_nombre, 
            f.nombre as familia_nombre
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Excluir artículos retirados
        $this->db->where("a.fecha_retiro IS NULL");
        
        // Ordenar por inventario_interno
        $this->db->order_by("a.inventario_interno", "ASC");
        
        $articulos = $this->db->get()->result();
        $articulos_con_ubicacion = array();
        
        // Obtener la ubicación actual para cada artículo
        foreach ($articulos as $articulo) {
            $this->db->select("u.nombre as ubicacion_nombre");
            $this->db->from("historial h");
            $this->db->join("ubicacion u", "h.id_ubicacion = u.id", "left");
            $this->db->where("h.id_articulo", $articulo->inventario_interno);
            $this->db->order_by("h.fecha", "DESC");
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $articulo->ubicacion_nombre = $query->row()->ubicacion_nombre;
                
                // Solo incluir el artículo si su ubicación NO es "retirados"
                if (strtolower($articulo->ubicacion_nombre) !== 'retirados') {
                    $articulos_con_ubicacion[] = $articulo;
                }
            } else {
                $articulo->ubicacion_nombre = "No especificada";
                // Incluir artículos sin ubicación especificada
                $articulos_con_ubicacion[] = $articulo;
            }
        }
        
        return $articulos_con_ubicacion;
    }

    // Método para filtrar artículos con ubicación
    public function filtrarArticulosConUbicacion($marca = null, $familia = null, $busqueda = null) {
        // Consulta principal para obtener los artículos
        $this->db->select("
            a.inventario_interno, 
            a.nro_inventario, 
            a.nroserie,
            a.descripcion,
            a.modelo,
            a.observacion,
            m.nombre as marca_nombre, 
            f.nombre as familia_nombre
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Aplicar filtros si se proporcionan
        if (!empty($marca)) {
            $this->db->where('a.id_marca', $marca);
        }
        
        if (!empty($familia)) {
            $this->db->where('a.id_familia', $familia);
        }
        
        if (!empty($busqueda)) {
            $this->db->group_start();
            $this->db->like('a.nro_inventario', $busqueda);
            $this->db->or_like('a.nroserie', $busqueda);
            $this->db->or_like('a.descripcion', $busqueda);
            $this->db->or_like('a.modelo', $busqueda);
            $this->db->or_like('m.nombre', $busqueda);
            $this->db->or_like('f.nombre', $busqueda);
            $this->db->group_end();
        }
        
        // Excluir artículos retirados
        $this->db->where("a.fecha_retiro IS NULL");
        
        // Ordenar por inventario_interno
        $this->db->order_by("a.inventario_interno", "ASC");
        
        $articulos = $this->db->get()->result();
        $articulos_con_ubicacion = array();
        
        // Obtener la ubicación actual para cada artículo
        foreach ($articulos as $articulo) {
            $this->db->select("u.nombre as ubicacion_nombre");
            $this->db->from("historial h");
            $this->db->join("ubicacion u", "h.id_ubicacion = u.id", "left");
            $this->db->where("h.id_articulo", $articulo->inventario_interno);
            $this->db->order_by("h.fecha", "DESC");
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $articulo->ubicacion_nombre = $query->row()->ubicacion_nombre;
                
                // Solo incluir el artículo si su ubicación NO es "retirados"
                if (strtolower($articulo->ubicacion_nombre) !== 'retirados') {
                    $articulos_con_ubicacion[] = $articulo;
                }
            } else {
                $articulo->ubicacion_nombre = "No especificada";
                // Incluir artículos sin ubicación especificada
                $articulos_con_ubicacion[] = $articulo;
            }
        }
        
        return $articulos_con_ubicacion;
    }

    //-------------------------------------------------------------------------
    // Obtener o crear una marca
    public function obtenerOCrearMarca($nombre_marca) {
        // Buscar si ya existe la marca
        $this->db->where('nombre', $nombre_marca);
        $query = $this->db->get('marca');
        
        if ($query->num_rows() > 0) {
            // Si existe, devolver su ID
            return $query->row()->id;
        } else {
            // Si no existe, crearla
            $datos = array('nombre' => $nombre_marca);
            $this->db->insert('marca', $datos);
            return $this->db->insert_id();
        }
    }

    // Obtener o crear una familia
    public function obtenerOCrearFamilia($nombre_familia) {
        // Buscar si ya existe la familia
        $this->db->where('nombre', $nombre_familia);
        $query = $this->db->get('familia');
        
        if ($query->num_rows() > 0) {
            // Si existe, devolver su ID
            return $query->row()->id;
        } else {
            // Si no existe, crearla
            $datos = array('nombre' => $nombre_familia);
            $this->db->insert('familia', $datos);
            return $this->db->insert_id();
        }
    }

    // Obtener o crear una ubicación
    public function obtenerOCrearUbicacion($nombre_ubicacion) {
        // Buscar si ya existe la ubicación
        $this->db->where('nombre', $nombre_ubicacion);
        $query = $this->db->get('ubicacion');
        
        if ($query->num_rows() > 0) {
            // Si existe, devolver su ID
            return $query->row()->id;
        } else {
            // Si no existe, crearla
            $datos = array('nombre' => $nombre_ubicacion);
            $this->db->insert('ubicacion', $datos);
            return $this->db->insert_id();
        }
    }

    // Método para importar artículos desde un array de datos
    public function importarArticulos($datos_articulos, $crear_nuevos = true) {
        // Depuración
        error_log("Valor de crear_nuevos: " . var_export($crear_nuevos, true));
        
        // Convertir $crear_nuevos a booleano si es una cadena "true"
        if ($crear_nuevos === "true") {
            $crear_nuevos = true;
        }
        
        $resultados = array(
            'total' => count($datos_articulos),
            'insertados' => 0,
            'actualizados' => 0,
            'errores' => 0,
            'mensajes_error' => array()
        );
        
        foreach ($datos_articulos as $indice => $articulo) {
            // Validar datos mínimos requeridos
            if (empty($articulo['descripcion']) || 
                empty($articulo['modelo']) || empty($articulo['marca']) || empty($articulo['familia'])) {
                $resultados['errores']++;
                $resultados['mensajes_error'][] = "Fila " . ($indice + 2) . ": Faltan campos obligatorios";
                continue;
            }
            
            // Si no hay número de serie, asignar "S/N"
            if (empty($articulo['nroserie'])) {
                $articulo['nroserie'] = "S/N";
            }
            
            try {
                // Iniciar transacción
                $this->db->trans_start();
                
                // Obtener IDs de marca y familia (creándolos si no existen)
                $id_marca = $this->obtenerOCrearMarca($articulo['marca']);
                $id_familia = $this->obtenerOCrearFamilia($articulo['familia']);
                
                // Obtener el siguiente inventario_interno
                $siguiente_inventario = $this->getSiguienteInventarioInterno();
                
                // Preparar datos para insertar
                $datos_insert = array(
                    'inventario_interno' => $siguiente_inventario,
                    'nro_inventario' => !empty($articulo['nro_inventario']) ? $articulo['nro_inventario'] : null,
                    'nroserie' => $articulo['nroserie'],
                    'descripcion' => $articulo['descripcion'],
                    'modelo' => $articulo['modelo'],
                    'observacion' => isset($articulo['observacion']) ? $articulo['observacion'] : '', // Asegurar que no sea NULL
                    'id_marca' => $id_marca,
                    'id_familia' => $id_familia,
                    'fecha_ingreso' => date('Y-m-d')
                );
                
                // Insertar artículo
                $this->db->insert('articulo', $datos_insert);
                
                // Si se especificó una ubicación, registrar en historial
                if (!empty($articulo['ubicacion'])) {
                    $id_ubicacion = $this->obtenerOCrearUbicacion($articulo['ubicacion']);
                    
                    if ($id_ubicacion) {
                        $datos_historial = array(
                            'id_articulo' => $siguiente_inventario,
                            'id_ubicacion' => $id_ubicacion,
                            'fecha' => date('Y-m-d H:i:s')
                        );
                        
                        $this->agregarHistorial($datos_historial);
                    }
                }
                
                $resultados['insertados']++;
                
                // Finalizar transacción
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    $resultados['errores']++;
                    $resultados['mensajes_error'][] = "Fila " . ($indice + 2) . ": Error en la transacción de base de datos";
                }
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $resultados['errores']++;
                $resultados['mensajes_error'][] = "Fila " . ($indice + 2) . ": " . $e->getMessage();
            }
        }
        
        return $resultados;
    }
    
    // Método para filtrar artículos retirados
    public function filtrarArticulosRetirados($marca = null, $familia = null, $busqueda = null) {
        $this->db->select("
            a.inventario_interno,
            a.nro_inventario,
            a.nroserie,
            a.descripcion,
            a.modelo,
            a.fecha_retiro,
            a.motivo_retiro,
            m.nombre as marca_nombre,
            f.nombre as familia_nombre
        ");
        $this->db->from("articulo a");
        $this->db->join("marca m", "a.id_marca = m.id", "left");
        $this->db->join("familia f", "a.id_familia = f.id", "left");
        
        // Aplicar filtros si se proporcionan
        if (!empty($marca)) {
            $this->db->where('a.id_marca', $marca);
        }
        
        if (!empty($familia)) {
            $this->db->where('a.id_familia', $familia);
        }
        
        if (!empty($busqueda)) {
            $this->db->group_start();
            $this->db->like('a.nro_inventario', $busqueda);
            $this->db->or_like('a.nroserie', $busqueda);
            $this->db->or_like('a.descripcion', $busqueda);
            $this->db->or_like('a.modelo', $busqueda);
            $this->db->or_like('m.nombre', $busqueda);
            $this->db->or_like('f.nombre', $busqueda);
            $this->db->group_end();
        }
        
        // Incluir solo artículos retirados
        $this->db->where("a.fecha_retiro IS NOT NULL");
        
        // Ordenar por fecha de retiro descendente
        $this->db->order_by("a.fecha_retiro", "DESC");
        
        return $this->db->get()->result();
    }
}
