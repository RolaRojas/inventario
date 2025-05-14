<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Informacion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    


    <link href="<?= base_url('assets/css/general.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/crear_info.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <div class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="brand">INFORMACION</div>
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
                    <a href="<?= site_url('crear_usuario'); ?>" class="nav-btn" title="Crear Usuario">
                        <i class="bi bi-person-fill-add"></i> <span>Crear Usuario</span>
                    </a>
                    <a href="<?= site_url('inventario/crear_info'); ?>" class="nav-btn active" title="Crear Informacion">
                        <i class="bi bi-grid-3x3-gap-fill"></i> <span>Crear Informacion</span>
                    </a>
                </nav>
                <div class="logout">
                    <a href="<?= site_url('login/cerrar_sesion'); ?>" title="Cerrar Sesión" class="btn btn-danger">
                        <i class="bi bi-power"></i> <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="main-content" id="mainContent">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="h3">Agregar informacion</h1>
                        </div>
                    </div>

                    <!-- Pestañas tipo Excel -->
                    <ul class="nav nav-tabs excel-tabs" id="inventarioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="marcas-tab" data-bs-toggle="tab" data-bs-target="#marcas" type="button" role="tab" aria-controls="marcas" aria-selected="true">
                                <i class="bi bi-tag"></i> Marcas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="familias-tab" data-bs-toggle="tab" data-bs-target="#familias" type="button" role="tab" aria-controls="familias" aria-selected="false">
                                <i class="bi bi-diagram-3"></i> Familias
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ubicaciones-tab" data-bs-toggle="tab" data-bs-target="#ubicaciones" type="button" role="tab" aria-controls="ubicaciones" aria-selected="false">
                                <i class="bi bi-geo-alt"></i> Ubicaciones
                            </button>
                        </li>
                    </ul>

                    

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content" id="inventarioTabsContent">
                        <!-- Pestaña de Marcas -->
                        <div class="tab-pane fade show active" id="marcas" role="tabpanel" aria-labelledby="marcas-tab">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2 mb-md-0">
                                        <button type="button" class="btn btn-primary btn-sm" id="agregarMarca">
                                            <i class="bi bi-plus-circle"></i> Agregar
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" id="editarMarca" disabled>
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="eliminarMarca" disabled>
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <label for="buscarMarca" class="me-2 d-none d-sm-block">Buscar:</label>
                                        <input type="text" class="form-control form-control-sm" id="buscarMarca" placeholder="Buscar marca...">
                                    </div>
                                </div>
                            </div>

                            <div class="table-container">
                                <table class="table table-excel" id="tablaMarcas">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($marcas) && !empty($marcas)): ?>
                                            <?php foreach ($marcas as $marca): ?>
                                                <tr data-id="<?= $marca->id ?>">
                                                    <td><?= $marca->id ?></td>
                                                    <td><?= $marca->nombre ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No hay marcas registradas</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pestaña de Familias -->
                        <div class="tab-pane fade" id="familias" role="tabpanel" aria-labelledby="familias-tab">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" class="btn btn-primary btn-sm" id="agregarFamilia">
                                            <i class="bi bi-plus-circle"></i> Agregar
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" id="editarFamilia" disabled>
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="eliminarFamilia" disabled>
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <label for="buscarFamilia" class="me-2">Buscar:</label>
                                        <input type="text" class="form-control form-control-sm" id="buscarFamilia" placeholder="Buscar familia...">
                                    </div>
                                </div>
                            </div>

                            <div class="table-container">
                                <table class="table table-excel" id="tablaFamilias">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($familias) && !empty($familias)): ?>
                                            <?php foreach ($familias as $familia): ?>
                                                <tr data-id="<?= $familia->id ?>">
                                                    <td><?= $familia->id ?></td>
                                                    <td><?= $familia->nombre ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No hay familias registradas</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pestaña de Ubicaciones -->
                        <div class="tab-pane fade" id="ubicaciones" role="tabpanel" aria-labelledby="ubicaciones-tab">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" class="btn btn-primary btn-sm" id="agregarUbicacion">
                                            <i class="bi bi-plus-circle"></i> Agregar
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" id="editarUbicacion" disabled>
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="eliminarUbicacion" disabled>
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <label for="buscarUbicacion" class="me-2">Buscar:</label>
                                        <input type="text" class="form-control form-control-sm" id="buscarUbicacion" placeholder="Buscar ubicación...">
                                    </div>
                                </div>
                            </div>

                            <div class="table-container">
                                <table class="table table-excel" id="tablaUbicaciones">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($ubicaciones) && !empty($ubicaciones)): ?>
                                            <?php foreach ($ubicaciones as $ubicacion): ?>
                                                <tr data-id="<?= $ubicacion->id ?>">
                                                    <td><?= $ubicacion->id ?></td>
                                                    <td><?= $ubicacion->nombre ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No hay ubicaciones registradas</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal para agregar/editar marca -->
    <div class="modal fade" id="marcaModal" tabindex="-1" aria-labelledby="marcaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="marcaModalLabel">Agregar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="marcaForm">
                        <input type="hidden" id="marca_id" name="id">

                        <div class="mb-3">
                            <label for="marca_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="marca_nombre" name="nombre" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarMarca">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar familia -->
    <div class="modal fade" id="familiaModal" tabindex="-1" aria-labelledby="familiaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="familiaModalLabel">Agregar Familia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="familiaForm">
                        <input type="hidden" id="familia_id" name="id">

                        <div class="mb-3">
                            <label for="familia_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="familia_nombre" name="nombre" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarFamilia">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar ubicación -->
    <div class="modal fade" id="ubicacionModal" tabindex="-1" aria-labelledby="ubicacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubicacionModalLabel">Agregar Ubicación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ubicacionForm">
                        <input type="hidden" id="ubicacion_id" name="id">

                        <div class="mb-3">
                            <label for="ubicacion_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="ubicacion_nombre" name="nombre" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarUbicacion">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este elemento? Esta acción no se puede deshacer.</p>
                    <p id="mensajeValidacion" class="text-danger d-none">Este elemento no se puede eliminar porque está siendo utilizado por otros registros.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor para alertas -->
    <div class="alert-container" id="alertContainer"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/general.js') ?>"></script>
    <script src="<?= base_url('assets/js/crear_info.js') ?>"></script>
</body>

</html>