<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Sistema de Inventario</h3>
                <p class="mb-0">Inicia sesión para continuar</p>
            </div>
            <div class="card-body p-4">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(validation_errors()): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= validation_errors(); ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= site_url('login/validar_login'); ?>" method="post" id="loginForm">
                    <div class="mb-3">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" class="form-control" id="rut" name="rut" 
                               placeholder="Ingresa tu RUT" required 
                               maxlength="9" 
                               pattern="[0-9kK]{1,9}"
                               title="Ingresa un RUT válido (máximo 9 caracteres, solo números y la letra k)">
                        <div class="invalid-feedback">
                            Ingresa un RUT válido (máximo 9 caracteres, solo números y la letra k).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del RUT
        document.getElementById('rut').addEventListener('input', function(e) {
            // Convertir a minúsculas y eliminar caracteres no permitidos
            let value = e.target.value.toLowerCase().replace(/[^0-9k]/g, '');
            
            // Limitar a 9 caracteres
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            
            // Actualizar el valor del campo
            e.target.value = value;
            
            // Validar el patrón
            const isValid = /^[0-9k]{1,9}$/.test(value);
            
            if (!isValid) {
                e.target.classList.add('is-invalid');
                e.target.nextElementSibling.style.display = 'block';
            } else {
                e.target.classList.remove('is-invalid');
                e.target.nextElementSibling.style.display = 'none';
            }
        });
        
        // Validación del formulario antes de enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const rutInput = document.getElementById('rut');
            const rutValue = rutInput.value.toLowerCase();
            
            // Verificar que el RUT sea válido
            const isValid = /^[0-9k]{1,9}$/.test(rutValue);
            
            if (!isValid) {
                e.preventDefault(); // Detener el envío del formulario
                rutInput.classList.add('is-invalid');
                rutInput.nextElementSibling.style.display = 'block';
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>