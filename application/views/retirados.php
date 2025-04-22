<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Inventario - Retirados</title>
    
    <!-- Estilos externos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
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
            background-color: #e6f7ff !important;
            border-left: 3px solid #1890ff;
        }

        /* Estilos para las celdas */
        #tablainventario td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: middle;
            text-align: center;
        }

        /* Estilos para la paginación */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            display: block;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            color: #0d6efd;
            text-decoration: none;
        }

        .pagination li.active a {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .pagination li a:hover {
            background-color: #e9ecef;
        }

        .pagination li.disabled a {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .page-info {
            color: #6c757d;
        }

        /* Estilos para el selector de registros por página */
        .records-per-page {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-select-sm {
            padding-right: 25px !important;
            background-position: right 5px center !important;
        }
        
        /* Resto de estilos de la página */
        /* Estilos generales */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 220px;
            background-color: #343a40;
            color: white;
            position: fixed;
            height: 100%;
            transition: width 0.3s ease;
            display: flex;
            flex-direction: column;
            padding: 15px 10px;
            z-index: 999;
        }
        
        /* SIDEBAR COLAPSADO */
        .sidebar.collapsed {
            width: 60px;
        }
        
        /* BOTÓN HAMBURGUESA */
        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar.collapsed .toggle-btn {
            transform: rotate(90deg);
        }
        
        /* ENCABEZADO DE SIDEBAR */
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
        }
        
        .sidebar.collapsed .sidebar-header {
            justify-content: center;
        }
        
        /* NOMBRE DE LA APLICACIÓN */
        .brand {
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        /* Ocultar el nombre cuando el sidebar está colapsado */
        .sidebar.collapsed .brand {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            opacity: 0 !important;
            visibility: hidden !important;
            z-index: -1 !important;
            pointer-events: none !important;
            clip: rect(0, 0, 0, 0) !important;
            margin: -1px !important;
            padding: 0 !important;
            border: 0 !important;
        }
        
        /* ========== BOTONES DE MENÚ ========== */

        .nav-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            transition: background-color 0.3s ease;
            justify-content: flex-start;
            position: relative;
        }
        
        .nav-btn i {
            font-size: 20px;
        }
        
        .nav-btn:hover {
            background-color: #495057;
            color: white;
            text-decoration: none;
        }
        
        /* COLAPSADO: solo íconos */
        .sidebar.collapsed .nav-btn {
            justify-content: center;
            padding: 12px;
            background-color: #4a4e54;
            overflow: hidden;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .sidebar.collapsed .nav-btn i {
            font-size: 22px;
            color: white;
            margin: 0;
        }
        
        .sidebar.collapsed .nav-btn span {
            display: none;
        }
        
        /* Añadir tooltip al pasar el mouse sobre los iconos cuando está colapsado */
        .sidebar.collapsed .nav-btn:hover::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: #343a40;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            white-space: nowrap;
            z-index: 1000;
        }
        
        /* ========== BOTÓN DE CERRAR SESIÓN ========== */
        .logout {
            margin-top: auto;
            padding-bottom: 10px;
        }
        
        .logout a {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            padding: 10px;
            transition: all 0.3s ease;
            border-radius: 5px;
            background-color: #dc3545;
        }
        
        .logout a:hover {
            background-color: #c82333;
            color: white;
            text-decoration: none;
        }
        
        .sidebar.collapsed .logout a {
            width: 40px;
            margin: auto;
            padding: 12px;
        }
        
        .sidebar.collapsed .logout a span {
            display: none;
        }
        
        .sidebar.collapsed .logout a i {
            font-size: 20px;
            margin: 0;
        }
        
        /* Añadir tooltip al botón de logout cuando está colapsado */
        .sidebar.collapsed .logout a:hover::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: #343a40;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            white-space: nowrap;
            z-index: 1000;
        }
        
        /* ========== CONTENIDO PRINCIPAL ========== */
        .main-content {
            margin-left: 220px;
            padding: 30px;
            transition: margin-left 0.3s ease, width 0.3s ease;
            width: calc(100% - 220px);
            overflow-y: auto;
            height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 60px;
            width: calc(100% - 60px);
        }
        
        /* ========== RESPONSIVO ========== */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            
            .nav-btn span {
                display: none;
            }
            
            .nav-btn {
                justify-content: center;
                font-size: 0;
                padding: 12px;
            }
            
            .sidebar-header .brand {
                display: none;
            }
            
            .logout a {
                width: 40px;
                margin: auto;
                padding: 12px;
            }
            
            .logout a span {
                display: none;
            }
        }
        
        /* Animación para el botón de hamburguesa */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .sidebar.collapsed .toggle-btn:hover {
            animation: pulse 1s infinite;
        }
        
        /* Botones de acción */
        .action-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .action-buttons button:hover {
            background-color: #0056b3;
        }
        
        /* Botón de exportar con un color diferente */
        .action-buttons .export-btn {
            background-color: #28a745;
        }
        
        .action-buttons .export-btn:hover {
            background-color: #218838;
        }
        
        /* Estilos para el filtro */
        .filter-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">RETIRADOS</div>
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

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content" id="mainContent">
    <div class="row">
        <!-- Lista de artículos -->
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Lista de Artículos Retirados</h2>
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
                                <?php if(isset($marcas) && !empty($marcas)): ?>
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?= $marca->id; ?>"><?= $marca->nombre; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="filtroFamilia" class="form-label">Familia:</label>
                            <select id="filtroFamilia" class="form-select">
                                <option value="">Todas las familias</option>
                                <?php if(isset($familias) && !empty($familias)): ?>
                                    <?php foreach ($familias as $familia): ?>
                                        <option value="<?= $familia->id; ?>"><?= $familia->nombre; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarModal">Agregar</button>
                <button class="btn btn-warning">Modificar</button>
                <button class="btn btn-success export-btn">Exportar</button>
            </div>
            
            <div class="table-container">
                <table id="tablainventario" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th style="width: 30%;">Nombre</th>
                            <th>Categoría</th>
                            <th>Ubicación</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if(isset($articulos) && !empty($articulos)): ?>
                            <?php foreach ($articulos as $articulo): ?>
                                <tr>
                                    <td><?= $articulo->inventario_interno; ?></td>
                                    <td><?= $articulo->nroserie; ?></td>
                                    <td><?= $articulo->descripcion; ?></td>
                                    <td><?= isset($articulo->modelo) ? $articulo->modelo : 'No especificada'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay artículos retirados para mostrar</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                <div class="page-info">Mostrando <span id="startRecord">1</span> a <span id="endRecord">6</span> de <span id="totalRecords">6</span> registros</div>
                <ul class="page-info"> <span id="totalRecords">6</span> registros</div></ul>
                <ul class="pagination" id="pagination"></ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Artículo -->
<div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="agregarModalLabel">Agregar Nuevo Artículo Retirado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAgregar" action="<?= base_url('index.php/retirados/agregar'); ?>" method="post">
          <div class="mb-3">
            <label for="nroserie" class="form-label">Nombre/Nro. Serie</label>
            <input type="text" class="form-control" id="nroserie" name="nroserie" required>
          </div>
          <div class="mb-3">
            <label for="categoria" class="form-label">Categoría</label>
            <input type="text" class="form-control" id="categoria" name="categoria" required>
          </div>
          <div class="mb-3">
            <label for="cantidad" class="form-label">Retirado</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
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
        <h5 class="modal-title" id="modificarModalLabel">Modificar Artículo Retirado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formModificar" action="<?= base_url('index.php/retirados/modificar'); ?>" method="post">
          <input type="hidden" id="id_articulo" name="id_articulo">
          <div class="mb-3">
            <label for="mod_nroserie" class="form-label">Nombre/Nro. Serie</label>
            <input type="text" class="form-control" id="mod_nroserie" name="nroserie" required>
          </div>
          <div class="mb-3">
            <label for="mod_categoria" class="form-label">Categoría</label>
            <input type="text" class="form-control" id="mod_categoria" name="categoria" required>
          </div>
          <div class="mb-3">
            <label for="mod_cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="mod_cantidad" name="cantidad" required>
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
        <h5 class="modal-title" id="exportarModalLabel">Exportar Artículos Retirados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Seleccione el formato de exportación:</p>
        <div class="d-flex gap-3 justify-content-center">
          <a href="<?= base_url('index.php/retirados/exportar/excel'); ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Excel
          </a>

          <a href="<?= base_url('index.php/retirados/exportar/pdf'); ?>" class="btn btn-danger">
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

<!-- Scripts -->
<script>
// Funcionalidad del sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    // Función para ajustar el sidebar según el tamaño de la pantalla
    function ajustarSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            toggleBtn.disabled = true;  // Deshabilitar el botón en móviles
        } else {
            toggleBtn.disabled = false;  // Habilitar el botón en pantallas de escritorio
        }
    }
    
    // Ejecutar la función al cargar la página y al cambiar el tamaño de la ventana
    ajustarSidebar();
    window.addEventListener('resize', ajustarSidebar);
    
    // Funcionalidad del botón de toggle
    toggleBtn.addEventListener('click', function() {
        if (window.innerWidth > 768) { // Solo funciona en pantallas grandes
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });
    
    // Paginación de la tabla
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
    
    // Funcionalidad para los botones de acción
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
        document.getElementById('mod_categoria').value = celdas[2].textContent;
        document.getElementById('mod_cantidad').value = 1; // Valor por defecto
        
        modificarModal.show();
    });
    
    // Evento para el botón Exportar
    btnExportar.addEventListener('click', function() {
        exportarModal.show();
    });
    
    // Permitir seleccionar filas de la tabla
    tabla.addEventListener('click', function(e) {
        const fila = e.target.closest('tr');
        if (!fila || fila.parentElement.tagName === 'THEAD') return;
        
        // Deseleccionar la fila anterior si existe
        if (filaSeleccionada) {
            filaSeleccionada.classList.remove('table-active');
        }
        
        // Seleccionar la nueva fila
        fila.classList.add('table-active');
        filaSeleccionada = fila;
    });
    
    // Funcionalidad para los filtros
    document.getElementById('btnFiltrar').addEventListener('click', function() {
        // Aquí iría la lógica para filtrar los resultados
        // Por ahora solo mostraremos un mensaje
        alert('Función de filtrado implementada');
    });
    
    document.getElementById('btnLimpiarFiltro').addEventListener('click', function() {
        // Limpiar los campos de filtro
        document.getElementById('filtroMarca').value = '';
        document.getElementById('filtroFamilia').value = '';
        document.getElementById('filtroBusqueda').value = '';
    });
});
</script>

</body>
</html>
