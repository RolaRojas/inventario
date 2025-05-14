<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña - Sistema de Inventario</title>
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
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: #007bff;
      color: white;
      text-align: center;
      border-radius: 10px 10px 0 0;
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
        <h3 class="mb-0">Recuperar Contraseña</h3>
        <p class="mb-0">Ingresa tu RUT para recibir instrucciones</p>
      </div>
      <div class="card-body p-4">
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger" role="alert">
            <?php if ($_GET['error'] == 1): ?>
              No se encontró ningún usuario con ese RUT.
            <?php elseif ($_GET['error'] == 2): ?>
              Error al enviar el correo. Intenta nuevamente.
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('olvide_clave.php') ?>" method="post" id="recoveryForm">
          <div class="mb-3">
            <label for="rut" class="form-label">RUT</label>
            <input type="text" class="form-control" id="rut" name="rut" 
              placeholder="Ingresa tu RUT (sin guion)" required
              maxlength="9"
              pattern="[0-9]{7,8}[0-9kK]{1}"
              title="Ingresa un RUT válido (8 o 9 dígitos, solo números y posiblemente k como último dígito)">
            <div class="invalid-feedback">
              Ingresa un RUT válido (8 o 9 dígitos, solo números y posiblemente k como último dígito).
            </div>
          </div>
          
          <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-envelope me-2"></i>Enviar Instrucciones
            </button>
          </div>
          
          <div class="text-center">
            <a href="<?= site_url('login/index') ?>" class="text-decoration-none">
              <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
            </a>
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
      // Convertir a minúsculas
      let value = e.target.value.toLowerCase();
      
      // Eliminar caracteres no permitidos (solo permitir números y la letra k)
      const originalValue = value;
      value = value.replace(/[^0-9k]/g, '');
      
      // Si se eliminaron caracteres no permitidos, mostrar error
      if (value !== originalValue && originalValue.length > 0) {
        showError(this, true);
      } else {
        showError(this, false);
      }
      
      // Asegurarse de que solo haya una 'k' y que esté al final
      if (value.indexOf('k') !== -1) {
        // Si hay una 'k', eliminar todas las k
        value = value.replace(/k/g, '');
        // Y añadir una sola 'k' al final si es necesario
        if (value.length < 9) {
          value = value + 'k';
        }
      }
      
      // Limitar a 9 caracteres
      if (value.length > 9) {
        value = value.substring(0, 9);
      }
      
      // Actualizar el valor del campo
      e.target.value = value;
    });
    
    // Validación cuando el campo pierde el foco
    document.getElementById('rut').addEventListener('blur', function() {
      validateRut(this);
    });
    
    // Función para validar el RUT
    function validateRut(input) {
      const value = input.value.toLowerCase();
      
      // No validar si está vacío
      if (value.length === 0) {
        showError(input, false);
        return;
      }
      
      // Validar el patrón: 7-8 números seguidos de un número o k
      const isValid = /^[0-9]{7,8}([0-9]|k)$/.test(value);
      
      // Mostrar error solo si el campo no está vacío y el formato es incorrecto
      showError(input, !isValid);
    }
    
    // Función para mostrar u ocultar el mensaje de error
    function showError(input, show) {
      if (show) {
        input.classList.add('is-invalid');
        input.nextElementSibling.style.display = 'block';
      } else {
        input.classList.remove('is-invalid');
        input.nextElementSibling.style.display = 'none';
      }
    }

    // Validación del formulario antes de enviar
    document.getElementById('recoveryForm').addEventListener('submit', function(e) {
      const rutInput = document.getElementById('rut');
      const rutValue = rutInput.value.toLowerCase();
      
      // Verificar que el RUT sea válido: 7-8 números seguidos de un número o k
      const isValid = /^[0-9]{7,8}([0-9]|k)$/.test(rutValue);
      
      if (!isValid) {
        e.preventDefault(); // Detener el envío del formulario
        showError(rutInput, true);
        return false;
      }
      
      return true;
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

    // También iniciar con jQuery si está disponible
    if (typeof jQuery !== 'undefined') {
      jQuery(document).ready(function() {
        preventDevTools();
      });
    }
  </script>
</body>

</html>
