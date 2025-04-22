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
    <title>Recuperar Contraseña</title>
</head>

<body>
    <div class="login-container">
        <h2>Recuperar Contraseña</h2>
        <p>Ingresa tu RUT para recibir un correo con instrucciones para restablecer tu contraseña.</p>
        
        <form action="<?= base_url('olvide_clave.php') ?>" method="post">
            <div>
                <input type="text" id="rut" name="rut" pattern="[0-9]{8,9}" maxlength="9" placeholder="Rut (sin guion)" required>
            </div>
            
            <button type="submit">Enviar Instrucciones</button>
            
            <div class="options" style="justify-content: center; margin-top: 20px;">
                <a href="<?= base_url('login') ?>">Volver al inicio de sesión</a>
            </div>
            
            <?php
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 1) {
                    echo '<p style="color: red;">No se encontró ningún usuario con ese RUT.</p>';
                } elseif ($_GET['error'] == 2) {
                    echo '<p style="color: red;">Error al enviar el correo. Intenta nuevamente.</p>';
                }
            }
            ?>
        </form>
    </div>

    <script>
        // Validación de formato RUT (solo números)
        document.getElementById('rut').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 9);
        });
    </script>
</body>
</html>