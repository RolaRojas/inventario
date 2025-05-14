<!DOCTYPE html>
<html>
<head>
    <title>Importar Excel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { margin-top: 30px; }
        .card { margin-bottom: 20px; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Importar Artículos desde Excel</h1>
        
        <?php if($this->session->flashdata('mensaje')): ?>
            <div class="alert alert-<?php echo $this->session->flashdata('tipo_mensaje'); ?>">
                <?php echo $this->session->flashdata('mensaje'); ?>
            </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('errores')): ?>
            <div class="alert alert-danger">
                <h4>Se encontraron los siguientes errores:</h4>
                <ul>
                    <?php foreach($this->session->flashdata('errores') as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="m-0">Subir archivo Excel</h3>
                    </div>
                    <div class="card-body">
                        <?php echo form_open_multipart('inventario/procesar_importacion'); ?>
                            <div class="form-group">
                                <label for="archivo_excel">Seleccionar archivo Excel:</label>
                                <input type="file" name="archivo_excel" class="form-control-file" required>
                                <small class="form-text text-muted">Formatos permitidos: .xlsx, .xls</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="hoja">Nombre de la hoja (opcional):</label>
                                <input type="text" name="hoja" class="form-control" placeholder="Dejar en blanco para usar la hoja activa">
                            </div>
                            
                            <div class="form-group">
                                <label for="fila_inicio">Fila de inicio (opcional):</label>
                                <input type="number" name="fila_inicio" class="form-control" value="2" min="1">
                                <small class="form-text text-muted">Por defecto: 2 (asumiendo que la fila 1 contiene encabezados)</small>
                            </div>

                            <div class="form-check">
    <input class="form-check-input" type="radio" name="modo_importacion" id="modo_solo_nuevos" value="true" checked>
    <label class="form-check-label" for="modo_solo_nuevos">
        Solo crear nuevos artículos
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="modo_importacion" id="modo_actualizar" value="actualizar">
    <label class="form-check-label" for="modo_actualizar">
        Crear nuevos y actualizar existentes
    </label>
</div>
                            
                            <div class="form-group">
                                <label>Modo de importación:</label>
                                <div class="form-check">
    <input class="form-check-input" type="radio" name="modo_importacion" id="modo_solo_nuevos" value="true" checked>
    <label class="form-check-label" for="modo_solo_nuevos">
        Solo crear nuevos artículos
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="modo_importacion" id="modo_actualizar" value="actualizar">
    <label class="form-check-label" for="modo_actualizar">
        Crear nuevos y actualizar existentes
    </label>
</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Importar</button>
                            <a href="<?php echo site_url('descargar_plantilla'); ?>" class="btn btn-secondary">Descargar Plantilla</a>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            
            <?php if($this->session->flashdata('resumen')): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="m-0">Resumen de importación</h3>
                    </div>
                    <div class="card-body">
                        <?php $resumen = $this->session->flashdata('resumen'); ?>
                        <div class="alert alert-info">
                            <p><strong>Total de registros procesados:</strong> <?php echo $resumen['total']; ?></p>
                            <p><strong>Registros exitosos:</strong> <?php echo $resumen['exitosos']; ?></p>
                            <p><strong>Registros con errores:</strong> <?php echo $resumen['errores']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <a href="<?php echo base_url('ingresar'); ?>" class="btn btn-outline-secondary">Volver a Inventario</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>