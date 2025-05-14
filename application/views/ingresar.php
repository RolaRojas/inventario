<?php defined('BASEPATH')
OR
exit("No direct script access allowed")
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/general.css') ?>" rel="stylesheet">
    
    <style>

        
        /* Estilos para el filtro */
        .filter-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Estilos para el popup de éxito */
        .success-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 1050;
            max-width: 500px;
            width: 90%;
        }
        
        .popup-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1040;
        }
        
        .success-header {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 15px -20px;
        }
        
        .success-content {
            margin-bottom: 15px;
        }
        
        .success-footer {
            text-align: right;
        }

/* Estilos para la tabla tipo Excel */
.table-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

#tablainventario {
    margin-bottom: 0;
    background-color: white;
    width: 100%;
    border-collapse: collapse;
}

/* Estilos para el encabezado */
#tablainventario thead th {
    position: sticky;
    top: 0;
    background-color: #f0f0f0;
    color: #333;
    font-weight: bold;
    border: 1px solid #ddd;
    border-bottom: 2px solid #999;
    padding: 10px;
    text-align: center;
}

/* Estilos para las filas */
#tablainventario tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

#tablainventario tbody tr:hover {
    background-color: #e6f7ff;
    cursor: pointer;
}

/* Estilo para fila seleccionada */
#tablainventario tr.table-active {
    background-color: #e6f7ff;
    border-left: 3px solid #1890ff;
}

/* Estilos para las celdas */
#tablainventario td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: middle;
    text-align: center;
}
    </style>
</head>
<body>

<div
class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">INGRESO</div>
        <button id="toggleSidebar" class="toggle-btn">&#9776;</button>
    </div>
    <nav class="sidebar-links">
        <a href="<?= site_url('inicio'); ?>" class="nav-btn" title="Inicio">
            <i class="bi bi-house-door-fill"></i> <span>Inicio</span>
        </a>
        <a href="<?= site_url('ingresar'); ?>" class="nav-btn" title="Ingreso">
            <i class="bi bi-clipboard2-check-fill"></i> <span>Ingreso</span>
        </a>
        <a href="<?= site_url('retirados'); ?>" class="nav-btn" title="Retirados">
            <i class="bi bi-archive-fill"></i> <span>Retirados</span>
        </a>
        <a href="<?= site_url('crear_usuario'); ?>" class="nav-btn" title="Retirados">
                <i class="bi bi-person-fill-add"></i> <span>Crear Usuario</span>
            </a>
            <a href="<?= site_url('crear_info'); ?>" class="nav-btn" title="Retirados">
                <i class="bi bi-geo-alt-fill"></i> <span>Crear Informacion</span>
            </a>
    </nav>
    <div class="logout">
        <a href="<?= site_url('login/cerrar_sesion'); ?>" title="Cerrar Sesión" class="btn btn-danger">
            <i class="bi bi-power"></i> <span>Cerrar Sesión</span>
        </a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <div class="row">
        <!-- Lista de artículos -->
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Lista de Artículos</h2>
                <div class="records-per-page">
                    <label for="recordsPerPage">Mostrar:</label>
                    <select id="recordsPerPage" class="form-select form-select-sm">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            
            <!-- Filtro -->
            <div class="filter-container">
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="filtroMarca" class="form-label">Marca:</label>
                <select id="filtroMarca" class="form-select">
                    <option value="">Todas las marcas</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?= $marca->id; ?>"><?= $marca->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="filtroFamilia" class="form-label">Familia:</label>
                <select id="filtroFamilia" class="form-select">
                    <option value="">Todas las familias</option>
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?= $familia->id; ?>"><?= $familia->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="filtroBusqueda" class="form-label">Buscar:</label>
                <input type="text" id="filtroBusqueda" class="form-control" placeholder="Buscar por nombre, descripción...">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-end">
            <button id="btnFiltrar" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Filtrar
            </button>
            <button id="btnLimpiarFiltro" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Limpiar
            </button>
            <button id="btnExportarFiltradosExcel" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Excel
            </button>
            <button id="btnExportarFiltradosPDF" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
            </button>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons mb-3">
                <button class="btn btn-primary">Agregar</button>
                <button class="btn btn-warning">Modificar</button>
                <a href="<?= base_url('index.php/inventario/exportar/excel'); ?>" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-2"></i>Excel
                </a>
                <a href="<?= base_url('index.php/inventario/exportar/pdf'); ?>" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                </a>
            </div>
            
            <div class="table-container">
                <table id="tablainventario" class="table table-hover">
                    <thead >
                        <tr>
                            <th>ID</th>
                            <th style="width: 30%;">Nro Inventario</th>
                            <th>Marca</th>
                            <th>Familia</th>
                            <th>Ubicacion</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($articulos as $articulo):?>
                    
                            <tr>
                                <td><?= $articulo->inventario_interno; ?></td>
                                <td><?= $articulo->nro_inventario; ?></td>
                                <td><?= $articulo->marca_nombre; ?></td>
                                <td><?= $articulo->familia_nombre; ?></td>
                                <td><?= $articulo->ubicacion_nombre; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                <div class="page-info">Mostrando <span id="startRecord">1</span> a <span id="endRecord">10</span> de <span id="totalRecords"><?= count($articulos); ?></span> registros</div>
                <ul class="pagination" id="pagination"></ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Artículo -->
<div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="agregarModalLabel">Agregar Nuevo Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAgregar" action="<?= base_url('index.php/inventario/agregar'); ?>" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="inventario_interno" class="form-label">Inventario Interno</label>
                <input type="text" class="form-control" id="inventario_interno" name="inventario_interno" value="<?= $siguiente_inventario; ?>" readonly>
                <small class="text-muted">Este campo es autoincrementable y no se puede editar</small>
              </div>
              
            <div class="mb-3">
                <label for="nro_inventario" class="form-label">Nro. Inventario</label>
                <input type="text" class="form-control" id="nro_inventario" name="nro_inventario">
            </div>
              
              <div class="mb-3">
                <label for="nroserie" class="form-label">Nro. Serie</label>
                <input type="text" class="form-control" id="nroserie" name="nroserie" required>
              </div>
              
              <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="modelo" class="form-label">Modelo</label>
                <input type="text" class="form-control" id="modelo" name="modelo" required>
              </div>
              
              <div class="mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="2"></textarea>
              </div>
              
              <div class="mb-3">
                <label for="id_marca" class="form-label">Marca</label>
                <select class="form-select" id="id_marca" name="id_marca" required>
                  <option value="">Seleccione una marca</option>
                  <?php foreach ($marcas as $marca): ?>
                    <option value="<?= $marca->id; ?>"><?= $marca->nombre; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="id_familia" class="form-label">Familia</label>
                <select class="form-select" id="id_familia" name="id_familia" required>
                  <option value="">Seleccione una familia</option>
                  <?php foreach ($familias as $familia): ?>
                    <option value="<?= $familia->id; ?>"><?= $familia->nombre; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="id_ubicacion" class="form-label">Ubicación</label>
                <select class="form-select" id="id_ubicacion" name="id_ubicacion">
                  <option value="">Seleccione una ubicación</option>
                  <?php foreach ($ubicaciones as $ubicacion): ?>
                    <option value="<?= $ubicacion->id; ?>"><?= $ubicacion->nombre; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Modificar Artículo -->
<div class="modal fade" id="modificarModal" tabindex="-1" aria-labelledby="modificarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modificarModalLabel">Modificar Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formModificar" action="<?= base_url('index.php/inventario/modificar'); ?>" method="post">
          <input type="hidden" id="id_articulo" name="id_articulo">
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="mod_inventario_interno" class="form-label">Inventario Interno</label>
                <input type="text" class="form-control" id="mod_inventario_interno" name="inventario_interno" readonly>
                <small class="text-muted">Este campo no se puede editar</small>
              </div>
              
            <div class="mb-3">
                <label for="mod_nro_inventario" class="form-label">Nro. Inventario</label>
                <input type="text" class="form-control" id="mod_nro_inventario" name="nro_inventario">
            </div>
                    
              <div class="mb-3">
                <label for="mod_nroserie" class="form-label">Nro. Serie</label>
                <input type="text" class="form-control" id="mod_nroserie" name="nroserie" required>
              </div>
              
              <div class="mb-3">
                <label for="mod_descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="mod_descripcion" name="descripcion" rows="3" required></textarea>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="mod_modelo" class="form-label">Modelo</label>
                <input type="text" class="form-control" id="mod_modelo" name="modelo" required>
              </div>
              
              <div class="mb-3">
                <label for="mod_observacion" class="form-label">Observación</label>
                <textarea class="form-control" id="mod_observacion" name="observacion" rows="2"></textarea>
              </div>
              
              <div class="mb-3">
                <label for="mod_id_marca" class="form-label">Marca</label>
                <select class="form-select" id="mod_id_marca" name="id_marca" required>
                  <option value="">Seleccione una marca</option>
                  <?php foreach ($marcas as $marca): ?>
                    <option value="<?= $marca->id; ?>"><?= $marca->nombre; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="mod_id_familia" class="form-label">Familia</label>
                <select class="form-select" id="mod_id_familia" name="id_familia" required>
                  <option value="">Seleccione una familia</option>
                  <?php foreach ($familias as $familia): ?>
                    <option value="<?= $familia->id; ?>"><?= $familia->nombre; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
                <div class="mb-3">
                    <label for="mod_id_ubicacion" class="form-label">Ubicación</label>
                    <select class="form-select" id="mod_id_ubicacion" name="id_ubicacion">
                        <option value="">Seleccione una ubicación</option>
                        <?php foreach ($ubicaciones as $ubicacion): ?>
                        <option value="<?= $ubicacion->id; ?>"><?= $ubicacion->nombre; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de éxito -->
<div id="successModal" class="popup-backdrop" style="display: none;">
  <div class="success-popup">
    <div class="success-header">
      <h5 class="m-0">¡Operación Exitosa!</h5>
    </div>
    <div class="success-content">
      <p id="successMessage">El artículo ha sido agregado correctamente.</p>
    </div>
    <div class="success-footer">
      <button type="button" class="btn btn-success" id="btnSuccessOk">Aceptar</button>
    </div>
  </div>
</div>

<!-- Modal de confirmación de retiro -->
<div class="modal fade" id="confirmarRetiroModal" tabindex="-1" aria-labelledby="confirmarRetiroModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="confirmarRetiroModalLabel">Confirmar Retiro de Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Está a punto de marcar este artículo como retirado. Esta acción registrará la fecha actual como fecha de retiro.</p>
        <div class="mb-3">
          <label for="motivo_retiro_input" class="form-label">Motivo del Retiro:</label>
          <textarea id="motivo_retiro_input" class="form-control" rows="3" required></textarea>
        </div>
        <input type="hidden" id="motivo_retiro" name="motivo_retiro">
        <input type="hidden" id="es_retirado" name="es_retirado" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="confirmarRetiroBtn">Confirmar Retiro</button>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT ÚNICO CONSOLIDADO -->
<script>
// Esperar a que el documento esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Referencias globales
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    const tableBody = document.getElementById('tableBody');
    const pagination = document.getElementById('pagination');
    let filaSeleccionada = null;
    let currentPage = 1;
    let recordsPerPage = parseInt($('#recordsPerPage').val() || 5);
    
    // Inicializar modales
    const agregarModal = new bootstrap.Modal(document.getElementById('agregarModal'));
    const modificarModal = new bootstrap.Modal(document.getElementById('modificarModal'));
    const confirmarRetiroModal = new bootstrap.Modal(document.getElementById('confirmarRetiroModal'));
    
    // ===== FUNCIONES DE SIDEBAR =====
    function ajustarSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            toggleBtn.disabled = true;
        } else {
            toggleBtn.disabled = false;
        }
    }
    
    // Ejecutar la función de ajustar el sidebar en load y resize
    ajustarSidebar();
    window.addEventListener('resize', ajustarSidebar);
    
    toggleBtn.addEventListener('click', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });
    
    // ===== FUNCIONES DE PAGINACIÓN =====
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const totalRows = rows.length;
    $('#totalRecords').text(totalRows);
    
    function displayRows() {
        const start = (currentPage - 1) * recordsPerPage;
        const end = start + recordsPerPage;
        
        rows.forEach(row => row.style.display = 'none');
        rows.slice(start, end).forEach(row => row.style.display = '');
        
        $('#startRecord').text(totalRows === 0 ? 0 : start + 1);
        $('#endRecord').text(Math.min(end, totalRows));
        
        updatePagination();
    }
    
    function updatePagination() {
        pagination.innerHTML = '';
        const totalPages = Math.ceil(totalRows / recordsPerPage);
        
        // Botón anterior
        if (totalPages > 1) {
            const prevLi = document.createElement('li');
            prevLi.classList.add('page-item');
            if (currentPage === 1) prevLi.classList.add('disabled');
            
            const prevLink = document.createElement('a');
            prevLink.classList.add('page-link');
            prevLink.href = '#';
            prevLink.textContent = 'Anterior';
            prevLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    displayRows();
                }
            });
            
            prevLi.appendChild(prevLink);
            pagination.appendChild(prevLi);
        }
        
        // Botones de página
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const pageLi = document.createElement('li');
                pageLi.classList.add('page-item');
                if (i === currentPage) pageLi.classList.add('active');
                
                const pageLink = document.createElement('a');
                pageLink.classList.add('page-link');
                pageLink.href = '#';
                pageLink.textContent = i;
                pageLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = i;
                    displayRows();
                });
                
                pageLi.appendChild(pageLink);
                pagination.appendChild(pageLi);
            } else if ((i === currentPage - 2 && currentPage > 3) || (i === currentPage + 2 && currentPage < totalPages - 2)) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.classList.add('page-item', 'disabled');
                
                const ellipsisSpan = document.createElement('a');
                ellipsisSpan.classList.add('page-link');
                ellipsisSpan.textContent = '...';
                
                ellipsisLi.appendChild(ellipsisSpan);
                pagination.appendChild(ellipsisLi);
            }
        }
        
        // Botón siguiente
        if (totalPages > 1) {
            const nextLi = document.createElement('li');
            nextLi.classList.add('page-item');
            if (currentPage === totalPages) nextLi.classList.add('disabled');
            
            const nextLink = document.createElement('a');
            nextLink.classList.add('page-link');
            nextLink.href = '#';
            nextLink.textContent = 'Siguiente';
            nextLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    displayRows();
                }
            });
            
            nextLi.appendChild(nextLink);
            pagination.appendChild(nextLi);
        }
    }
    
    // Cambiar el número de registros por página
    $('#recordsPerPage').on('change', function() {
        recordsPerPage = Number.parseInt(this.value);
        currentPage = 1;
        displayRows();
    });
    
    // Mostrar la primera página al cargar
    displayRows();
});

// Usar jQuery para el resto de funcionalidades cuando el documento esté listo
$(document).ready(function() {
    // Variable global para la fila seleccionada
    let filaSeleccionada = null;
    
    // Inicializar modales
    const agregarModal = new bootstrap.Modal(document.getElementById('agregarModal'));
    const modificarModal = new bootstrap.Modal(document.getElementById('modificarModal'));
    const confirmarRetiroModal = new bootstrap.Modal(document.getElementById('confirmarRetiroModal'));
    
    // Solución para el problema del combobox
    $('#mod_id_ubicacion').on('click', function(e) {
        // Asegurarse de que el evento click funcione correctamente
        e.stopPropagation();
    });
    
    // ===== FUNCIONES DE TABLA Y SELECCIÓN =====
    // Permitir seleccionar filas de la tabla
    $('#tablainventario').on('click', 'tbody tr', function(e) {
        // Deseleccionar la fila anterior si existe
        if (filaSeleccionada) {
            filaSeleccionada.classList.remove('table-active');
        }
        
        // Seleccionar la nueva fila
        this.classList.add('table-active');
        filaSeleccionada = this;
        console.log('Fila seleccionada:', filaSeleccionada);
    });
    
    // ===== FUNCIONES DE FILTRADO =====
    $('#btnFiltrar').click(function() {
        const marca = $('#filtroMarca').val();
        const familia = $('#filtroFamilia').val();
        const busqueda = $('#filtroBusqueda').val();
        
        $.ajax({
            url: '<?= base_url('index.php/inventario/filtrar'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                marca: marca,
                familia: familia,
                busqueda: busqueda
            },
            success: function(response) {
                // Limpiar la tabla
                $('#tableBody').empty();
                
                // Si no hay resultados
                if (response.articulos.length === 0) {
                    $('#tableBody').append('<tr><td colspan="5" class="text-center">No se encontraron resultados</td></tr>');
                    $('#startRecord').text('0');
                    $('#endRecord').text('0');
                    $('#totalRecords').text('0');
                    $('#pagination').empty();
                    return;
                }
                
                // Agregar los resultados a la tabla
                $.each(response.articulos, function(index, articulo) {
                    let fila = '<tr>';
                    fila += '<td>' + articulo.inventario_interno + '</td>';
                    fila += '<td>' + (articulo.nro_inventario || '') + '</td>';
                    fila += '<td>' + articulo.marca_nombre + '</td>';
                    fila += '<td>' + articulo.familia_nombre + '</td>';
                    fila += '<td>' + (articulo.ubicacion_nombre || 'No especificada') + '</td>';
                    fila += '</tr>';
                    
                    $('#tableBody').append(fila);
                });
                
                // Actualizar información de paginación
                $('#totalRecords').text(response.articulos.length);
                
                // Reiniciar la paginación
                currentPage = 1;
                displayRows();
            },
            error: function(xhr, status, error) {
                console.error('Error al filtrar:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                alert('Ocurrió un error al filtrar los artículos');
            }
        });
    });
    
    // Limpiar filtros
    $('#btnLimpiarFiltro').click(function() {
        $('#filtroMarca').val('');
        $('#filtroFamilia').val('');
        $('#filtroBusqueda').val('');
        
        // Recargar la página para mostrar todos los artículos
        location.reload();
    });
    
    $('#btnExportarFiltradosExcel').click(function() {
        exportarFiltrados('excel');
    });

    // Exportar filtrados a PDF
    $('#btnExportarFiltradosPDF').click(function() {
        exportarFiltrados('pdf');
    });

    // Función común para exportar filtrados
    function exportarFiltrados(formato) {
        const marca = $('#filtroMarca').val();
        const familia = $('#filtroFamilia').val();
        const busqueda = $('#filtroBusqueda').val();
        
        // Crear un formulario temporal para enviar los parámetros
        const form = $('<form>', {
            'method': 'post',
            'action': '<?= base_url('index.php/inventario/exportar_filtrados/'); ?>' + formato,
            'target': '_blank'
        });
        
        // Agregar los parámetros al formulario
        $('<input>').attr({
            'type': 'hidden',
            'name': 'marca',
            'value': marca
        }).appendTo(form);
        
        $('<input>').attr({
            'type': 'hidden',
            'name': 'familia',
            'value': familia
        }).appendTo(form);
        
        $('<input>').attr({
            'type': 'hidden',
            'name': 'busqueda',
            'value': busqueda
        }).appendTo(form);
        
        // Agregar el formulario al body, enviarlo y luego eliminarlo
        $('body').append(form);
        form.submit();
        form.remove();
    }
    
    // ===== FUNCIONES DE BOTONES DE ACCIÓN =====
    // Evento para el botón Agregar
    $('.action-buttons .btn-primary').click(function() {
        // Limpiar el formulario antes de mostrar el modal
        $('#formAgregar')[0].reset();
        agregarModal.show();
    });
    
    // Evento para el botón Modificar
    $('.action-buttons .btn-warning').click(function() {
        if (!filaSeleccionada) {
            alert('Por favor, seleccione un artículo para modificar');
            return;
        }
        
        // Obtener el ID del artículo seleccionado (inventario_interno)
        const id_articulo = filaSeleccionada.cells[0].textContent;
        console.log('ID del artículo seleccionado:', id_articulo);
        
        // Limpiar el formulario antes de cargar nuevos datos
        $('#formModificar')[0].reset();
        
        // Mostrar indicador de carga
        $('body').append('<div id="loadingIndicator" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;justify-content:center;align-items:center;"><div style="background:white;padding:20px;border-radius:5px;">Cargando datos...</div></div>');
        
        // Realizar petición AJAX para obtener los datos del artículo
        $.ajax({
            url: '<?= base_url('index.php/inventario/obtener_articulo/'); ?>' + id_articulo,
            type: 'GET',
            dataType: 'json',
            success: function(articulo) {
                // Quitar indicador de carga
                $('#loadingIndicator').remove();
                
                console.log('Datos recibidos del servidor:', articulo);
                
                // Verificar que los datos sean válidos
                if (!articulo || typeof articulo !== 'object') {
                    console.error('Datos de artículo inválidos:', articulo);
                    alert('Los datos recibidos del servidor no son válidos');
                    return;
                }
                
                try {
                    // Llenar el formulario con los datos del artículo
                    $('#id_articulo').val(articulo.inventario_interno);
                    $('#mod_inventario_interno').val(articulo.inventario_interno);
                    $('#mod_nro_inventario').val(articulo.nro_inventario || '');
                    $('#mod_nroserie').val(articulo.nroserie || '');
                    $('#mod_descripcion').val(articulo.descripcion || '');
                    $('#mod_modelo').val(articulo.modelo || '');
                    $('#mod_observacion').val(articulo.observacion || '');
                    $('#mod_id_marca').val(articulo.id_marca || '');
                    $('#mod_id_familia').val(articulo.id_familia || '');
                    
                    // Si hay información de ubicación, establecerla
                    if (articulo.id_ubicacion) {
                        $('#mod_id_ubicacion').val(articulo.id_ubicacion);
                    } else {
                        $('#mod_id_ubicacion').val('');
                    }
                    
                    // Mostrar el modal
                    modificarModal.show();
                } catch (e) {
                    console.error('Error al procesar los datos del artículo:', e);
                    alert('Error al procesar los datos del artículo: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                // Quitar indicador de carga
                $('#loadingIndicator').remove();
                
                console.error('Error al obtener el artículo:', error);
                console.error('Estado HTTP:', status);
                console.error('Respuesta del servidor:', xhr.responseText);
                alert('Ocurrió un error al obtener los datos del artículo. Consulte la consola para más detalles.');
            }
        });
    });
    
    // ===== MANEJO DE FORMULARIOS =====
    // Manejar el envío del formulario de agregar con AJAX
    $('#formAgregar').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Cerrar el modal de agregar
                    agregarModal.hide();
                    
                    // Mostrar el modal de éxito
                    $('#successMessage').text('El artículo ha sido agregado correctamente.');
                    $('#successModal').fadeIn(300);
                    
                    // Recargar la tabla inmediatamente
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error al agregar el artículo: ' + (response.errors || 'Error desconocido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                alert('Ocurrió un error al procesar la solicitud. Por favor, inténtelo de nuevo.');
            }
        });
    });
    
    // Variable para almacenar el formulario de modificación
    let formModificarTemp = null;
    
    // Detectar cuando se selecciona "Retirados" en el dropdown de ubicación
    $('#mod_id_ubicacion').on('change', function() {
        const ubicacionText = $(this).find('option:selected').text().trim().toLowerCase();
        console.log('Ubicación seleccionada:', ubicacionText);
        
        // Si se selecciona "Retirados", mostrar el modal de confirmación
        if (ubicacionText === 'retirados') {
            console.log('Se seleccionó Retirados, mostrando modal de confirmación');
            // Guardar el formulario temporalmente
            formModificarTemp = $('#formModificar');
            $('#motivo_retiro_input').val(''); // Limpiar el campo de motivo
            confirmarRetiroModal.show();
        }
    });
    
    // Manejar el envío del formulario de modificar con AJAX
    $('#formModificar').on('submit', function(e) {
        e.preventDefault();
        
        // Mostrar los datos que se están enviando para depuración
        console.log('Enviando datos de modificación:', $(this).serialize());
        
        // Verificar si la ubicación es "Retirados"
        var ubicacionId = $('#mod_id_ubicacion').val();
        var ubicacionText = $('#mod_id_ubicacion option:selected').text();
        
        // Buscar la ubicación "Retirados" (normalmente tiene ID 4, pero podría ser diferente)
        if (ubicacionText.toLowerCase() === 'retirados') {
            // Guardar el formulario temporalmente
            formModificarTemp = $(this);
            
            // Mostrar el modal de confirmación
            confirmarRetiroModal.show();
            return false;
        }
        
        // Si no es "Retirados", enviar el formulario normalmente
        enviarFormularioModificar($(this));
    });
    
    // Botón de confirmar retiro
    $('#confirmarRetiroBtn').on('click', function() {
    const motivo = $('#motivo_retiro_input').val();
    
    if (!motivo.trim()) {
        alert('Por favor, ingrese un motivo para el retiro del artículo.');
        return;
    }
    
    if (formModificarTemp) {
        // Agregar el motivo al formulario como un campo oculto
        formModificarTemp.find('input[name="motivo_retiro"]').remove(); // Eliminar si existe
        formModificarTemp.find('input[name="es_retirado"]').remove(); // Eliminar si existe
        formModificarTemp.append('<input type="hidden" name="motivo_retiro" value="' + motivo + '">');
        formModificarTemp.append('<input type="hidden" name="es_retirado" value="1">');

        // Cerrar ambos modales inmediatamente antes de enviar la solicitud
        confirmarRetiroModal.hide();
        modificarModal.hide();
        
        // Mostrar indicador de carga
        $('body').append('<div id="loadingIndicator" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;justify-content:center;align-items:center;"><div style="background:white;padding:20px;border-radius:5px;">Procesando...</div></div>');
        
        // Enviar el formulario con manejo silencioso de errores
        $.ajax({
            url: formModificarTemp.attr('action'),
            type: 'POST',
            data: formModificarTemp.serialize(),
            dataType: 'json',
            success: function(response) {
                // Quitar indicador de carga
                $('#loadingIndicator').remove();
                
                // Mostrar el modal de éxito
                $('#successMessage').text('El artículo ha sido actualizado correctamente.');
                $('#successModal').fadeIn(300);
                
                // Recargar la página después de un breve retraso
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 1000);
            },
            error: function(xhr, status, error) {
                // Quitar indicador de carga
                $('#loadingIndicator').remove();
                
                // Mostrar el modal de éxito de todos modos
                $('#successMessage').text('El artículo ha sido actualizado.');
                $('#successModal').fadeIn(300);
                
                // Recargar la página después de un breve retraso
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 1000);
            }
        });

        // Limpiar la variable temporal
        formModificarTemp = null;
    } else {
        // Si no hay formulario temporal, simplemente cerrar el modal
        confirmarRetiroModal.hide();
    }
});
    
    // Función para enviar el formulario de modificación
    function enviarFormularioModificar(form) {
    // Verificar y mostrar los datos que se están enviando para depuración
    console.log('Datos a enviar:', form.serialize());
    console.log('Ubicación seleccionada:', $('#mod_id_ubicacion').val(), $('#mod_id_ubicacion option:selected').text());
    
    // Cerrar el modal antes de enviar la solicitud
    modificarModal.hide();
    
    // Mostrar indicador de carga
    $('body').append('<div id="loadingIndicator" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;justify-content:center;align-items:center;"><div style="background:white;padding:20px;border-radius:5px;">Procesando...</div></div>');
    
    // Asegurarse de que el valor de ubicación esté incluido en los datos
    let formData = form.serialize();
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            // Quitar indicador de carga
            $('#loadingIndicator').remove();
            
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                // Mostrar el modal de éxito
                $('#successMessage').text('El artículo ha sido modificado correctamente.');
                $('#successModal').fadeIn(300);
                
                // Recargar la página después de un breve retraso
                setTimeout(function() {
                    // Forzar recarga completa sin caché
                    window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
                }, 1000);
            } else {
                // Mostrar mensaje de error
                alert('Error al actualizar: ' + (response.message || 'Error desconocido'));
                
                // Recargar la página de todos modos
                setTimeout(function() {
                    window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
                }, 1000);
            }
        },
        error: function(xhr, status, error) {
            // Quitar indicador de carga
            $('#loadingIndicator').remove();
            
            console.error('Error en la solicitud AJAX:', error);
            console.error('Estado HTTP:', status);
            console.error('Respuesta del servidor:', xhr.responseText);
            
            // Mostrar mensaje de error
            alert('Ocurrió un error al procesar la solicitud. Por favor, inténtelo de nuevo.');
            
            // Recargar la página
            setTimeout(function() {
                window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
            }, 1000);
        }
    });
}
    
    // Cerrar el modal de éxito al hacer clic en Aceptar
    $('#btnSuccessOk').on('click', function() {
        $('#successModal').fadeOut(300);
        location.reload(); // Recargar la página para mostrar los cambios
    });
});

// Reemplazar el código existente:
//document.addEventListener("keydown", function(event) {
//    if (event.key === "F12") {
//        event.preventDefault(); // Previene la ejecución del evento F12
//    }
//}, false);

// Con el código completo:
// Función para prevenir herramientas de desarrollador
function preventDevTools() {
  // Prevenir F12
  document.addEventListener(
    "keydown",
    (e) => {
      // Bloquear F12
      if (e.key === "F12" || e.keyCode === 123) {
        e.preventDefault();
        return false;
      }

      // Bloquear Ctrl+Shift+I / Cmd+Option+I
      if ((e.ctrlKey && e.shiftKey && e.keyCode === 73) || (e.metaKey && e.altKey && e.keyCode === 73)) {
        e.preventDefault();
        return false;
      }

      // Bloquear Ctrl+Shift+J / Cmd+Option+J
      if ((e.ctrlKey && e.shiftKey && e.keyCode === 74) || (e.metaKey && e.altKey && e.keyCode === 74)) {
        e.preventDefault();
        return false;
      }

      // Bloquear Ctrl+Shift+C / Cmd+Option+C
      if ((e.ctrlKey && e.shiftKey && e.keyCode === 67) || (e.metaKey && e.altKey && e.keyCode === 67)) {
        e.preventDefault();
        return false;
      }

      // Bloquear Ctrl+U (ver código fuente)
      if (e.ctrlKey && e.keyCode === 85) {
        e.preventDefault();
        return false;
      }
    },
    true
  );

  // Prevenir clic derecho
  document.addEventListener(
    "contextmenu",
    (e) => {
      e.preventDefault();
      return false;
    },
    true
  );

  // Detectar herramientas de desarrollador
  function checkDevTools() {
    const widthThreshold = window.outerWidth - window.innerWidth > 160;
    const heightThreshold = window.outerHeight - window.innerHeight > 160;

    if (
      !(widthThreshold || heightThreshold) &&
      !(window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized)
    ) {
      return false;
    }

    // Opcional: redirigir o mostrar mensaje
    // window.location.href = 'about:blank';
    return true;
  }

  // Verificar periódicamente
  setInterval(checkDevTools, 1000);

  // Asegurarse de que funcione con modales
  function setupModalProtection() {
    const modalElements = document.querySelectorAll(".modal, .popup-backdrop, .modal-dialog, .modal-content");

    modalElements.forEach((modal) => {
      modal.addEventListener(
        "keydown",
        (e) => {
          if (e.key === "F12" || e.keyCode === 123) {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }

          // Bloquear otras combinaciones en modales
          if (
            (e.ctrlKey && e.shiftKey && e.keyCode === 73) ||
            (e.metaKey && e.altKey && e.keyCode === 73) ||
            (e.ctrlKey && e.shiftKey && e.keyCode === 74) ||
            (e.metaKey && e.altKey && e.keyCode === 74) ||
            (e.ctrlKey && e.shiftKey && e.keyCode === 67) ||
            (e.metaKey && e.altKey && e.keyCode === 67) ||
            (e.ctrlKey && e.keyCode === 85)
          ) {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }
        },
        true
      );
    });
  }

  // Configurar protección inicial
  setupModalProtection();

  // Configurar protección cuando se abren nuevos modales
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.addedNodes && mutation.addedNodes.length > 0) {
        setupModalProtection();
      }
    });
  });

  // Observar cambios en el DOM para detectar nuevos modales
  observer.observe(document.body, { childList: true, subtree: true });
}

// Iniciar la protección cuando el documento esté listo
document.addEventListener("DOMContentLoaded", preventDevTools);

// También iniciar con jQuery ya que está disponible en esta página
$(document).ready(function() {
  preventDevTools();
});
</script>

</body>
</html>
