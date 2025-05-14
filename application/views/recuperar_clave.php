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
document.addEventListener("DOMContentLoaded", preventDevTools);
    </script>
</body>
</html>
