<?php defined('BASEPATH')
OR
exit("No direct script access allowed")
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Panel de Inventario - Artículos Retirados</title>
   
   <!-- Estilos externos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/general.css') ?>" rel="stylesheet">
   
   
   <style>
       .sidebar.collapsed .brand {
           display: none;
           width: 0;
           height: 0;
           overflow: hidden;
           position: absolute;
           opacity: 0;
           visibility: hidden;
           z-index: -1;
       }
       
       .form-select-sm {
           padding-right: 25px;
           background-position: right 5px center;
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
       
       /* Estilo para la fecha de retiro */
       .fecha-retiro {
           color: #dc3545;
           font-weight: bold;
       }
       
       /* Estilo para el motivo de retiro */
       .motivo-retiro {
           font-style: italic;
           color: #6c757d;
       }
       
       /* Estilo para el botón de devolver */
       .btn-devolver {
           background-color: #17a2b8;
           color: white;
       }
       
       .btn-devolver:hover {
           background-color: #138496;
           color: white;
       }
   </style>
</head>
<body>

<div
class="sidebar" id="sidebar">
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
        <a href="<?= site_url('retirados'); ?>" class="nav-btn active" title="Retirados">
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
       <!-- Lista de artículos retirados -->
       <div class="col-md-12">
           <div class="d-flex justify-content-between align-items-center mb-3">
               <h2>Artículos Retirados</h2>
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
               <button class="btn btn-info btn-devolver">Devolver a Inventario</button>
               <a href="<?= base_url('index.php/inventario/exportar_retirados/excel'); ?>" class="btn btn-success">
                   <i class="bi bi-file-earmark-excel me-2"></i>Excel
               </a>
               <a href="<?= base_url('index.php/inventario/exportar_retirados/pdf'); ?>" class="btn btn-danger">
                   <i class="bi bi-file-earmark-pdf me-2"></i>PDF
               </a>
           </div>
           
           <div class="table-container">
               <table id="tablainventario" class="table table-hover">
                   <thead>
                       <tr>
                           <th>ID</th>
                           <th>Nro Inventario</th>
                           <th>Marca</th>
                           <th>Familia</th>
                           <th>Fecha Retiro</th>
                           <th>Motivo</th>
                       </tr>
                   </thead>
                   <tbody id="tableBody">
                       <?php if(isset($articulos) && !empty($articulos)): ?>
                           <?php foreach ($articulos as $articulo): ?>
                               <tr>
                                   <td><?= isset($articulo->inventario_interno) ? $articulo->inventario_interno : 'N/A'; ?></td>
                                   <td><?= isset($articulo->nro_inventario) ? $articulo->nro_inventario : 'N/A'; ?></td>
                                   <td><?= isset($articulo->marca_nombre) ? $articulo->marca_nombre : 'N/A'; ?></td>
                                   <td><?= isset($articulo->familia_nombre) ? $articulo->familia_nombre : 'N/A'; ?></td>
                                   <td class="fecha-retiro"><?= isset($articulo->fecha_retiro) ? date('d/m/Y', strtotime($articulo->fecha_retiro)) : 'N/A'; ?></td>
                                   <td class="motivo-retiro"><?= isset($articulo->motivo_retiro) ? $articulo->motivo_retiro : 'No especificado'; ?></td>
                               </tr>
                           <?php endforeach; ?>
                       <?php else: ?>
                           <tr>
                               <td colspan="6" class="text-center">No hay artículos retirados.</td>
                           </tr>
                       <?php endif; ?>
                   </tbody>
               </table>
           </div>
           <div class="pagination-container">
               <div class="page-info">Mostrando <span id="startRecord">1</span> a <span id="endRecord">10</span> de <span id="totalRecords"><?= isset($articulos) ? count($articulos) : 0; ?></span> registros</div>
               <ul class="pagination" id="pagination"></ul>
           </div>
       </div>
   </div>
</div>

<!-- Modificar el modal para devolver artículo a inventario -->
<div class="modal fade" id="devolverModal" tabindex="-1" aria-labelledby="devolverModalLabel" aria-hidden="true">
 <div class="modal-dialog">
   <div class="modal-content">
     <div class="modal-header bg-info text-white">
       <h5 class="modal-title" id="devolverModalLabel">Devolver Artículo a Inventario</h5>
       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <div class="modal-body">
       <form id="formDevolver" action="<?= base_url('index.php/inventario/devolver_articulo'); ?>" method="post">
         <input type="hidden" id="dev_id_articulo" name="id_articulo">
         
         <div class="mb-3">
           <label for="dev_id_ubicacion" class="form-label">Nueva Ubicación:</label>
           <select class="form-select" id="dev_id_ubicacion" name="id_ubicacion" required>
             <option value="en_bodega">En Bodega (Predeterminado)</option>
             <?php 
             if(isset($ubicaciones) && !empty($ubicaciones)): 
               foreach ($ubicaciones as $ubicacion): 
                 // Solo mostrar ubicaciones que no sean "retirados"
                 if(strtolower($ubicacion->nombre) !== 'retirados'): 
             ?>
                   <option value="<?= $ubicacion->id; ?>"><?= $ubicacion->nombre; ?></option>
             <?php 
                 endif;
               endforeach; 
             endif; 
             ?>
           </select>
           <small class="text-muted">Si no aparecen opciones, se usará "En Bodega" como ubicación predeterminada.</small>
         </div>
         
         <div class="mb-3">
           <label for="dev_observacion" class="form-label">Observación:</label>
           <textarea class="form-control" id="dev_observacion" name="observacion" rows="3" placeholder="Indique el motivo de la devolución o cualquier observación relevante"></textarea>
         </div>
         
         <div class="modal-footer">
           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
           <button type="submit" class="btn btn-info">Devolver a Inventario</button>
         </div>
       </form>
     </div>
   </div>
 </div>
</div>

<!-- Modal para modificar artículo retirado -->
<div class="modal fade" id="modificarModal" tabindex="-1" aria-labelledby="modificarModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-lg">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title" id="modificarModalLabel">Modificar Artículo Retirado</h5>
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
                 <?php if(isset($marcas) && !empty($marcas)): ?>
                   <?php foreach ($marcas as $marca): ?>
                     <option value="<?= $marca->id; ?>"><?= $marca->nombre; ?></option>
                   <?php endforeach; ?>
                 <?php endif; ?>
               </select>
             </div>
             
             <div class="mb-3">
               <label for="mod_id_familia" class="form-label">Familia</label>
               <select class="form-select" id="mod_id_familia" name="id_familia" required>
                 <option value="">Seleccione una familia</option>
                 <?php if(isset($familias) && !empty($familias)): ?>
                   <?php foreach ($familias as $familia): ?>
                     <option value="<?= $familia->id; ?>"><?= $familia->nombre; ?></option>
                   <?php endforeach; ?>
                 <?php endif; ?>
               </select>
             </div>
             
             <div class="mb-3">
               <label for="mod_motivo_retiro" class="form-label">Motivo de Retiro</label>
               <textarea class="form-control" id="mod_motivo_retiro" name="motivo_retiro" rows="2"></textarea>
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
     <p id="successMessage">El artículo ha sido modificado correctamente.</p>
   </div>
   <div class="success-footer">
     <button type="button" class="btn btn-success" id="btnSuccessOk">Aceptar</button>
   </div>
 </div>
</div>

<!-- Debug info para ubicaciones -->
<div class="d-none">
  <pre>
    <?php 
    if(isset($ubicaciones)) {
      echo "Ubicaciones cargadas: " . count($ubicaciones) . "\n";
      foreach($ubicaciones as $u) {
        echo "- ID: " . $u->id . ", Nombre: " . $u->nombre . "\n";
      }
    } else {
      echo "Variable \$ubicaciones no definida";
    }
    ?>
  </pre>
</div>

<script>
// Funcionalidad del sidebar
document.addEventListener('DOMContentLoaded', function() {
   const sidebar = document.getElementById('sidebar');
   const mainContent = document.getElementById('mainContent');
   const toggleBtn = document.getElementById('toggleSidebar');
   
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
       recordsPerPage = Number.parseInt(this.value);
       currentPage = 1; // Volver a la primera página
       displayRows();
   });
   
   // Mostrar la primera página al cargar
   displayRows();
   
   // Hacer filaSeleccionada global para que sea accesible desde jQuery
   window.filaSeleccionada = null;
   
   // Permitir seleccionar filas de la tabla
   const tabla = document.getElementById('tablainventario');
   tabla.addEventListener('click', function(e) {
       const fila = e.target.closest('tr');
       if (!fila || fila.parentElement.tagName === 'THEAD') return;
       
       // Deseleccionar la fila anterior si existe
       if (window.filaSeleccionada) {
           window.filaSeleccionada.classList.remove('table-active');
       }
       
       // Seleccionar la nueva fila
       fila.classList.add('table-active');
       window.filaSeleccionada = fila;
       console.log('Fila seleccionada:', window.filaSeleccionada);
   });
});
</script>

<script>
// Funcionalidad unificada para los botones de acción y formularios
$(document).ready(function() {
   // Referencias a los botones
   const btnModificar = $('.action-buttons .btn-warning');
   const btnDevolver = $('.action-buttons .btn-devolver');
   
   // Referencias a los modales
   const modificarModal = new bootstrap.Modal(document.getElementById('modificarModal'));
   const devolverModal = new bootstrap.Modal(document.getElementById('devolverModal'));
   
   // Evento para el botón Modificar
   btnModificar.click(function() {
       if (!window.filaSeleccionada) {
           alert('Por favor, seleccione un artículo para modificar');
           return;
       }
       
       // Obtener el ID del artículo seleccionado (inventario_interno)
       const id_articulo = window.filaSeleccionada.cells[0].textContent;
       console.log('ID del artículo seleccionado:', id_articulo);
       
       // Limpiar el formulario antes de cargar nuevos datos
       $('#formModificar')[0].reset();
       
       // Realizar petición AJAX para obtener los datos del artículo
       $.ajax({
           url: '<?= base_url('index.php/inventario/obtener_articulo/'); ?>' + id_articulo,
           type: 'GET',
           dataType: 'json',
           beforeSend: function() {
               console.log('Enviando solicitud para obtener artículo con ID:', id_articulo);
           },
           success: function(articulo) {
               console.log('Datos recibidos del servidor:', articulo);
               
               // Verificar que los datos sean válidos
               if (!articulo || typeof articulo !== 'object') {
                   console.error('Datos de artículo inválidos:', articulo);
                   alert('Los datos recibidos del servidor no son válidos');
                   return;
               }
               
               try {
                   // Llenar el formulario con los datos del artículo
                   $('#id_articulo').val(articulo.inventario_interno || '');
                   $('#mod_inventario_interno').val(articulo.inventario_interno || '');
                   $('#mod_nro_inventario').val(articulo.nro_inventario || '');
                   $('#mod_nroserie').val(articulo.nroserie || '');
                   $('#mod_descripcion').val(articulo.descripcion || '');
                   $('#mod_modelo').val(articulo.modelo || '');
                   $('#mod_observacion').val(articulo.observacion || '');
                   $('#mod_id_marca').val(articulo.id_marca || '');
                   $('#mod_id_familia').val(articulo.id_familia || '');
                   $('#mod_motivo_retiro').val(articulo.motivo_retiro || '');
                   
                   // Mostrar el modal
                   modificarModal.show();
               } catch (e) {
                   console.error('Error al procesar los datos del artículo:', e);
                   alert('Error al procesar los datos del artículo: ' + e.message);
               }
           },
           error: function(xhr, status, error) {
               console.error('Error al obtener el artículo:', error);
               console.error('Estado HTTP:', status);
               console.error('Respuesta del servidor:', xhr.responseText);
               alert('Ocurrió un error al obtener los datos del artículo. Consulte la consola para más detalles.');
           }
       });
   });
   
   // Evento para el botón Devolver a Inventario
   btnDevolver.click(function() {
       if (!window.filaSeleccionada) {
           alert('Por favor, seleccione un artículo para devolver a inventario');
           return;
       }
       
       // Obtener el ID del artículo seleccionado
       const id_articulo = window.filaSeleccionada.cells[0].textContent;
       console.log('ID del artículo a devolver:', id_articulo);
       
       // Establecer el ID en el formulario de devolución
       $('#dev_id_articulo').val(id_articulo);
       
       // Mostrar el modal de devolución
       devolverModal.show();
   });
   
   // Manejar el envío del formulario de modificar con AJAX
   $('#formModificar').on('submit', function(e) {
       e.preventDefault();
       
       // Mostrar los datos que se están enviando para depuración
       console.log('Enviando datos de modificación:', $(this).serialize());
       
       $.ajax({
           url: $(this).attr('action'),
           type: 'POST',
           data: $(this).serialize(),
           dataType: 'json',
           success: function(response) {
               console.log('Respuesta del servidor:', response);
               if (response.success) {
                   // Cerrar el modal de modificar
                   $('#modificarModal').modal('hide');
                   
                   // Mostrar el modal de éxito
                   $('#successMessage').text('El artículo ha sido modificado correctamente.');
                   $('#successModal').fadeIn(300);
                   
                   // Recargar la tabla después de un breve retraso
                   setTimeout(function() {
                       location.reload();
                   }, 1500);
               } else {
                   alert('Error al modificar el artículo: ' + (response.message || 'Error desconocido'));
               }
           },
           error: function(xhr, status, error) {
               console.error('Error en la solicitud AJAX:', error);
               console.error('Respuesta del servidor:', xhr.responseText);
               alert('Ocurrió un error al procesar la solicitud. Por favor, inténtelo de nuevo.');
           }
       });
   });
   
   // Manejar el envío del formulario de devolución con AJAX
   $('#formDevolver').on('submit', function(e) {
       e.preventDefault();
       
       // Verificar que se haya seleccionado un artículo
       if (!$('#dev_id_articulo').val()) {
           alert('Error: No se ha seleccionado un artículo para devolver');
           return;
       }
       
       // Verificar que se haya seleccionado una ubicación
       if (!$('#dev_id_ubicacion').val()) {
           alert('Error: Debe seleccionar una ubicación');
           return;
       }
       
       // Mostrar los datos que se están enviando para depuración
       const formData = $(this).serialize();
       console.log('Enviando datos de devolución:', formData);
       
       $.ajax({
           url: $(this).attr('action'),
           type: 'POST',
           data: formData,
           dataType: 'json',
           success: function(response) {
               console.log('Respuesta del servidor:', response);
               if (response.success) {
                   // Cerrar el modal de devolución
                   $('#devolverModal').modal('hide');
                   
                   // Mostrar el modal de éxito
                   $('#successMessage').text('El artículo ha sido devuelto al inventario correctamente.');
                   $('#successModal').fadeIn(300);
                   
                   // Recargar la tabla después de un breve retraso
                   setTimeout(function() {
                       location.reload();
                   }, 1500);
               } else {
                   console.error('Error en la respuesta:', response);
                   alert('Error al devolver el artículo: ' + (response.message || response.errors || 'Error desconocido'));
               }
           },
           error: function(xhr, status, error) {
               console.error('Error en la solicitud AJAX:', error);
               console.error('Estado HTTP:', xhr.status);
               console.error('Respuesta del servidor:', xhr.responseText);
               
               try {
                   const response = JSON.parse(xhr.responseText);
                   alert('Error al procesar la solicitud: ' + (response.message || response.errors || error));
               } catch (e) {
                   alert('Ocurrió un error al procesar la solicitud. Por favor, inténtelo de nuevo.');
               }
           }
       });
   });
   
   // Funcionalidad para el filtrado
   $('#btnFiltrar').click(function() {
       const marca = $('#filtroMarca').val();
       const familia = $('#filtroFamilia').val();
       const busqueda = $('#filtroBusqueda').val();
       
       // Realizar la petición AJAX para filtrar
       $.ajax({
           url: '<?= base_url('index.php/inventario/filtrar_retirados'); ?>',
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
                   $('#tableBody').append('<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>');
                   return;
               }
               
               // Agregar los resultados a la tabla
               $.each(response.articulos, function(index, articulo) {
                   let fila = '<tr>';
                   fila += '<td>' + (articulo.inventario_interno || 'N/A') + '</td>';
                   fila += '<td>' + (articulo.nro_inventario || 'N/A') + '</td>';
                   fila += '<td>' + (articulo.marca_nombre || 'N/A') + '</td>';
                   fila += '<td>' + (articulo.familia_nombre || 'N/A') + '</td>';
                   
                   // Formatear la fecha de retiro
                   let fechaRetiro = 'N/A';
                   if (articulo.fecha_retiro) {
                       const fecha = new Date(articulo.fecha_retiro);
                       fechaRetiro = fecha.toLocaleDateString('es-ES');
                   }
                   fila += '<td class="fecha-retiro">' + fechaRetiro + '</td>';
                   
                   fila += '<td class="motivo-retiro">' + (articulo.motivo_retiro || 'No especificado') + '</td>';
                   fila += '</tr>';
                   
                   $('#tableBody').append(fila);
               });
               
               // Actualizar la paginación
               if (typeof displayRows === 'function') {
                   displayRows();
               }
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
   
   // Cerrar el modal de éxito al hacer clic en Aceptar
   $('#btnSuccessOk').on('click', function() {
       $('#successModal').fadeOut(300);
       location.reload(); // Recargar la página para mostrar los cambios
   });
   
   // Botón para exportar artículos filtrados
   $('#btnExportarFiltradosExcel').click(function() {
       exportarFiltrados('excel');
   });
   
   // Exportar filtrados a PDF
   $('#btnExportarFiltradosPDF').click(function() {
       exportarFiltrados('pdf');
   });
});

// Función común para exportar filtrados
function exportarFiltrados(formato) {
    const marca = $('#filtroMarca').val();
    const familia = $('#filtroFamilia').val();
    const busqueda = $('#filtroBusqueda').val();
    
    // Crear un formulario temporal para enviar los parámetros
    const form = $('<form>', {
        'method': 'post',
        'action': '<?= base_url('index.php/inventario/exportar_retirados_filtrados/'); ?>' + formato,
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
};

// Código para depuración
$(document).ajaxError(function(event, jqXHR, settings, thrownError) {
   console.error('Error AJAX en:', settings.url);
   console.error('Estado HTTP:', jqXHR.status);
   console.error('Texto de estado:', jqXHR.statusText);
   console.error('Respuesta:', jqXHR.responseText);
});


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
$(document).ready(() => {
  preventDevTools();
});

// También iniciar la protección cuando se cargue el DOM
document.addEventListener("DOMContentLoaded", preventDevTools);
</script>
</body>
</html>
