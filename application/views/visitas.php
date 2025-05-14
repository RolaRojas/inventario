<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/inicio.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="brand">INICIO</span>
        <button id="toggleSidebar" class="toggle-btn">&#9776;</button>
    </div>
    <nav class="sidebar-links">
        <a href="http://localhost/inventario/index.php/inicio">Inicio</a>
        <a href="http://localhost/inventario/index.php/ingresar">Ingreso</a>
        <a href="http://localhost/inventario/index.php/retirados">Retirados</a>
    </nav>
    <div class="logout">
        <a href="<?= base_url('login'); ?>" class="btn btn-danger w-100">Cerrar Sesión</a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <div class="row">
        <!-- Lista de artículos -->
        <div class="col-md-7">
            <h2>Lista de Artículos</h2>
            <table id="tablainicio" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nro. Serie</th>
                        <th style="width: 30%;">Familia</th>
                        <th>marca</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articulos as $articulo):?>
                        <tr>
                            <td><?= $articulo->id; ?></td>
                            <td><?= $articulo->nroserie; ?></td>
                            <td><?= $articulo->familia; ?></td>
                            <td><?= $articulo->marca; ?></td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Gráfico -->
        <div class="col-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Gráfico</h2>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="mostrarGrafico('bar')">
                        <i class=" bi bi-bar-chart me-m1"></i> Barras
                    </button>
                    <button class="btn btn-outline-success" onclick="mostrarGrafico('pie')">
                        <i class="bi bi-pie-chart me-1"></i>Torta
                    </button>
                </div>
            </div>
            <canvas id="graficoInventario" width="400" height="300"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('graficoInventario').getContext('2d');
    let tipoActual = 'bar';
    const data = {
        labels: ['Laptop', 'Silla','otro','pantalla'],
        datasets: [{
            label: 'Cantidad',
            data: [5, 10,20,5],
            backgroundColor: ['#0d6efd', '#198754','#198756','#198736']
        }]
    };

    let chart = new Chart(ctx, {
        type: tipoActual,
        data: data
    });

    function mostrarGrafico(tipo) {
        chart.destroy();
        chart = new Chart(ctx, {
            type: tipo,
            data: data
        });
    }

    document.getElementById('toggleSidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('mainContent').classList.toggle('expanded');
    });

    document.addEventListener("keydown", function(event) {
    if (event.key === "F12") {
        event.preventDefault(); // Previene la ejecución del evento F12
    }
}, false);
</script>

</body>
</html>