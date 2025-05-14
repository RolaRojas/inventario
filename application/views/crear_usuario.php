<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="<?= base_url('assets/css/general.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="brand">CREAR USUARIO</div>
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
                    <a href="<?= site_url('crear_usuario'); ?>" class="nav-btn active" title="Crear Usuario">
                        <i class="bi bi-person-fill-add"></i> <span>Crear Usuario</span>
                    </a>
                    <a href="<?= site_url('crear_info'); ?>" class="nav-btn" title="Crear Informacion">
                        <i class="bi bi-geo-alt-fill"></i> <span>Crear Informacion</span>
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
                            <h1 class="h3">Crear Usuario</h1>
                        </div>
                    </div>
                    
                    <!-- Aquí va el contenido específico de la página -->
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Formulario para crear usuario -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"></h5>
                                    <!-- Aquí iría tu formulario -->
                                </div>
                            </div>
                        </div>
                    </div>
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
</body>

</html>