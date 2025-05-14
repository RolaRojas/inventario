<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/inicio.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Estilo inline para asegurar que el texto INICIO se oculte cuando el sidebar está colapsado -->
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

        /* Corregir la posición de la flecha en el selector */
        .form-select-sm {
            padding-right: 25px;
            background-position: right 5px center;
        }

        .chart-container {
            position: sticky;
            top: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            z-index: 10;
            height: 400px;
        }

        @media (max-width: 768px) {
            .chart-container {
                position: relative;
                top: 0;
                height: auto;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">INICIO</div>
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
            <a href="<?= site_url('ubicaciones'); ?>" class="nav-btn" title="Retirados">
                <i class="bi bi-geo-alt-fill"></i> <span>Ubicaciones</span>
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
            <div class="col-md-7">
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
                <div class="table-container">
                    <table id="tablainicio" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Id_Interno</th>
                                <th>Nro. Serie</th>
                                <th style="width: 30%;">Familia</th>
                                <th>Marca</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php foreach ($articulos as $articulo): ?>
                                <tr>
                                    <td><?= $articulo->inventario_interno; ?></td>
                                    <td><?= $articulo->nroserie; ?></td>
                                    <td><?= $articulo->familia; ?></td>
                                    <td><?= $articulo->marca; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="page-info">Mostrando <spasn id="startRecord">1</spasn> a <span id="endRecord">10</span> de <span id="totalRecords"><?= count($articulos); ?></span> registros</div>
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </div>

            <!-- Gráfico -->
            <div class="col-md-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Gráfico</h2>
                    <div>
                        <button class="btn btn-outline-primary me-2" onclick="mostrarGrafico('bar')">
                            <i class="bi bi-bar-chart me-1"></i> Barras
                        </button>
                        <button class="btn btn-outline-success" onclick="mostrarGrafico('pie')">
                            <i class="bi bi-pie-chart me-1"></i> Torta
                        </button>
                    </div>
                </div>
                <canvas id="graficoInventario" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('graficoInventario').getContext('2d');
        let chart;
        let familiaActual = 'bar';

        function cargarDatos(familia) {
            fetch('<?= base_url("index.php/Inventario/datos_grafico") ?>')
                .then(response => response.json())
                .then(datos => {
                    if (chart) chart.destroy();

                    chart = new Chart(ctx, {
                        type: familia,
                        data: {
                            labels: datos.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: datos.data,
                                backgroundColor: ["#205781", "#4F959D", "#98D2C0", "#F6F8D5",
                                    "#2C6B8E", "#3F7C9C", "#1A4763", "#608EA3",
                                    "#6FA9AE", "#7DC3B5", "#AEDDCB", "#8AC4B1",
                                    "#B6E1D1", "#F1F6C6", "#EBF0B5", "#FBFBE0",
                                    "#E8EDB3", "#F9FBD3", "#E0F1F1", "#D1E4DF",
                                    "#F5FAF8"
                                ]
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });
                });
        }

        function mostrarGrafico(familia) {
            familiaActual = familia;
            cargarDatos(familia);
        }

        cargarDatos(familiaActual);
    </script>

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
                toggleBtn.disabled = true; // Deshabilitar el botón en móviles
            } else {
                toggleBtn.disabled = false; // Habilitar el botón en pantallas de escritorio
            }
        }

        // Ejecutar la función de ajustar el sidebar en load y resize
        window.addEventListener('load', ajustarSidebar);
        window.addEventListener('resize', ajustarSidebar);

        // Habilitar la funcionalidad del toggle solo si no estamos en un dispositivo móvil
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 768) { // Solo en pantallas grandes funciona
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');

                // Ocultar/mostrar el texto INICIO con JavaScript también
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

            // Asegurarse de que el texto INICIO esté oculto si el sidebar está colapsado
            if (sidebar.classList.contains('collapsed')) {
                brandText.style.display = 'none';
            }
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
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        // Iniciar la protección cuando el documento esté listo
        document.addEventListener("DOMContentLoaded", preventDevTools);
    </script>

</body>

</html>