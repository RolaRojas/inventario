<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
    <title>Restablecer Contraseña</title>
</head>

<body>
    <div class="login-container">
        <h2>Restablecer Contraseña</h2>
        <p>Ingresa tu nueva contraseña.</p>
        
        <form action="<?= base_url('auth/update_password') ?>" method="post">
            <input type="hidden" name="token" value="<?= $token ?>">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
            
            <div>
                <input type="password" id="password" name="password" placeholder="Nueva contraseña" required>
            </div>
            
            <div>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required>
            </div>
            
            <div class="options">
                <label for="visible">
                    <input type="checkbox" id="visible" name="visible">
                    Mostrar Contraseña
                </label>
            </div>
            
            <button type="submit">Cambiar Contraseña</button>
            
            <?php
            if (isset($_GET['error']) && $_GET['error'] == 1) {
                echo '<p style="color: red;">Las contraseñas no coinciden.</p>';
            }
            ?>
        </form>
    </div>

    <script>
        // Script para mostrar/ocultar contraseña
        document.getElementById('visible').addEventListener('change', function() {
            var passwordInput = document.getElementById('password');
            var confirmInput = document.getElementById('confirm_password');
            
            if (this.checked) {
                passwordInput.type = 'text';
                confirmInput.type = 'text';
            } else {
                passwordInput.type = 'password';
                confirmInput.type = 'password';
            }
        });
        
        // Validación de contraseñas coincidentes
        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>