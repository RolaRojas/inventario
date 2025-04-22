<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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

    <link rel="stylesheet" href="<?= base_url('assets/css/inicio.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar.collapsed .brand {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            opacity: 0 !important;
            visibility: hidden !important;
            z-index: -1 !important;
        }
        
        .form-select-sm {
            padding-right: 25px !important;
            background-position: right 5px center !important;
        }
        
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
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
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
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons mb-3">
                <button class="btn btn-primary">Agregar</button>
                <button class="btn btn-warning">Modificar</button>
                <button class="btn btn-success export-btn">Exportar</button>
            </div>
            
            <div class="table-container">
                <table id="tablainventario" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th style="width: 30%;">Nro Inventario</th>
                            <th>Marca</th>
                            <th>Familia</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($articulos as $articulo):?>
                    
                            <tr>
                                <td><?= $articulo->inventario_interno; ?></td>
                                <td><?= $articulo->nro_inventario; ?></td>
                                <td><?= $articulo->id_marca; ?></td>
                                <td><?= $articulo->id_familia; ?></td>
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

<script>
// Funcionalidad del sidebar
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleBtn = document.getElementById('toggleSidebar');
const brandText = document.getElementById('brandText');

function ajustarSidebar() {
    // En dispositivos móviles, colapsar el sidebar
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        toggleBtn.disabled = true;  // Deshabilitar el botón en móviles
    } else {
        toggleBtn.disabled = false;  // Habilitar el botón en pantallas de escritorio
    }
}

// Ejecutar la función de ajustar el sidebar en load y resize
window.addEventListener('load', ajustarSidebar);
window.addEventListener('resize', ajustarSidebar);

// Habilitar la funcionalidad del toggle solo si no estamos en un dispositivo móvil
toggleBtn.addEventListener('click', function () {
    if (window.innerWidth > 768) { // Solo en pantallas grandes funciona
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        // Ocultar/mostrar el texto INVENTARIO con JavaScript también
        if (sidebar.classList.contains('collapsed')) {
            brandText.style.display = 'none';
        } else {
            brandText.style.display = 'block';
        }
    }
});

// Paginación de la tabla
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('tableBody');
    const pagination = document.getElementById('pagination');
    const recordsPerPageSelect = document.getElementById('recordsPerPage');
    const startRecordSpan = document.getElementById('startRecord');
    const endRecordSpan = document.getElementById('endRecord');
    const totalRecordsSpan = document.getElementById('totalRecords');
    
    // Obtener todas las filas de la tabla
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const totalRows = rows.length;
    totalRecordsSpan.textContent = totalRows;
    
    let currentPage = 1;
    let recordsPerPage = parseInt(recordsPerPageSelect.value);
    
    // Función para mostrar las filas correspondientes a la página actual
    function displayRows() {
    const start = (currentPage - 1) * recordsPerPage;
    const end = start + recordsPerPage;
    
    // Ocultar todas las filas
    rows.forEach(row => row.style.display = 'none');
    
    // Mostrar solo las filas de la página actual
    rows.slice(start, end).forEach(row => row.style.display = '');
    
    // Actualizar información de registros mostrados
    startRecordSpan.textContent = totalRows === 0 ? 0 : start + 1;
    endRecordSpan.textContent = Math.min(end, totalRows);
    
    // Actualizar paginación
    updatePagination();
}
    
    // Función para actualizar los botones de paginación
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
            // Mostrar solo algunas páginas para no sobrecargar la UI
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
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
            } else if (
                (i === currentPage - 2 && currentPage > 3) || 
                (i === currentPage + 2 && currentPage < totalPages - 2)
            ) {
                // Agregar puntos suspensivos
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
    recordsPerPageSelect.addEventListener('change', function() {
        recordsPerPage = parseInt(this.value);
        currentPage = 1; // Volver a la primera página
        displayRows();
    });
    
    // Mostrar la primera página al cargar
    displayRows();
    
    // Asegurarse de que el texto INVENTARIO esté oculto si el sidebar está colapsado
    if (sidebar.classList.contains('collapsed')) {
        brandText.style.display = 'none';
    }
});
</script>
<!-- Modal para Agregar Artículo -->
<div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="agregarModalLabel">Agregar Nuevo Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAgregar" action="<?= base_url('index.php/inventario/agregar'); ?>" method="post">
          <div class="mb-3">
            <label for="nroserie" class="form-label">Nombre/Nro. Serie</label>
            <input type="text" class="form-control" id="nroserie" name="nroserie" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="modelo" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="modelo" name="modelo" required>
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modificarModalLabel">Modificar Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formModificar" action="<?= base_url('index.php/inventario/modificar'); ?>" method="post">
          <input type="hidden" id="id_articulo" name="id_articulo">
          <div class="mb-3">
            <label for="mod_nroserie" class="form-label">Nombre/Nro. Serie</label>
            <input type="text" class="form-control" id="mod_nroserie" name="nroserie" required>
          </div>
          <div class="mb-3">
            <label for="mod_descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="mod_descripcion" name="descripcion" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="mod_modelo" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="mod_modelo" name="modelo" required>
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

<!-- Modal para confirmar exportación -->
<div class="modal fade" id="exportarModal" tabindex="-1" aria-labelledby="exportarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportarModalLabel">Exportar Inventario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Seleccione el formato de exportación:</p>
        <div class="d-flex gap-3 justify-content-center">
          <a href="<?= base_url('index.php/inventario/exportar/excel'); ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Excel
          </a>

          <a href="<?= base_url('index.php/inventario/exportar/pdf'); ?>" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>PDF
          </a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- -->

<script>
// Funcionalidad para los botones de acción
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los botones
    const btnAgregar = document.querySelector('.action-buttons .btn-primary');
    const btnModificar = document.querySelector('.action-buttons .btn-warning');
    const btnExportar = document.querySelector('.action-buttons .export-btn');
    
    // Referencias a los modales
    const agregarModal = new bootstrap.Modal(document.getElementById('agregarModal'));
    const modificarModal = new bootstrap.Modal(document.getElementById('modificarModal'));
    const exportarModal = new bootstrap.Modal(document.getElementById('exportarModal'));
    
    // Tabla y filas
    const tabla = document.getElementById('tablainventario');
    let filaSeleccionada = null;
    
    // Evento para el botón Agregar
    btnAgregar.addEventListener('click', function() {
        // Limpiar el formulario antes de mostrar el modal
        document.getElementById('formAgregar').reset();
        agregarModal.show();
    });
    
    // Evento para el botón Modificar
    btnModificar.addEventListener('click', function() {
        if (!filaSeleccionada) {
            alert('Por favor, seleccione un artículo para modificar');
            return;
        }
        
        // Llenar el formulario con los datos de la fila seleccionada
        const celdas = filaSeleccionada.querySelectorAll('td');
        document.getElementById('id_articulo').value = celdas[0].textContent;
        document.getElementById('mod_nroserie').value = celdas[1].textContent;
        document.getElementById('mod_descripcion').value = celdas[2].textContent;
        document.getElementById('mod_modelo').value = celdas[3].textContent;
        
        modificarModal.show();
    });
    
    // Evento para el botón Exportar
    btnExportar.addEventListener('click', function() {
        exportarModal.show();
    });
    
    // Permitir seleccionar filas de la tabla
    tabla.addEventListener('click', function(e) {
        const fila = e.target.closest('tr');
        if (!fila) return;
        
        // Deseleccionar la fila anterior si existe
        if (filaSeleccionada) {
            filaSeleccionada.classList.remove('table-active');
        }
        
        // Seleccionar la nueva fila
        fila.classList.add('table-active');
        filaSeleccionada = fila;
    });
    
    // Manejar el envío del formulario de agregar
    document.getElementById('formAgregar').addEventListener('submit', function(e) {
        // Aquí puedes agregar validación adicional si es necesario
        // El formulario se enviará normalmente al backend
    });
    
    // Manejar el envío del formulario de modificar
    document.getElementById('formModificar').addEventListener('submit', function(e) {
        // Aquí puedes agregar validación adicional si es necesario
        // El formulario se enviará normalmente al backend
    });
});
</script>

</body>
</html>