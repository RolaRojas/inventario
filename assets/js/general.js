// Variable global para la URL base
const base_url = document.querySelector('base')?.href || window.location.origin + '/';

// Funcionalidad del sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    // Crear overlay para cerrar el sidebar en móviles
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Función para mostrar/ocultar overlay
    function toggleOverlay(show) {
        if (show) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    }

    function ajustarSidebar() {
        // En dispositivos móviles, siempre mantener el sidebar colapsado
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            sidebar.classList.remove('expanded'); // Asegurar que no tenga la clase expanded
            toggleOverlay(false);
        } else {
            // En escritorio, usar la preferencia guardada o el estado por defecto
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        }
    }

    // Ejecutar la función de ajustar el sidebar en load y resize
    ajustarSidebar();
    
    // Ajustar sidebar cuando cambia el tamaño de la ventana
    window.addEventListener('resize', function() {
        ajustarSidebar();
        
        // Si estamos pasando de móvil a escritorio, asegurar que el overlay esté oculto
        if (window.innerWidth > 768) {
            toggleOverlay(false);
        }
    });

    // Habilitar la funcionalidad del toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Evitar que el clic se propague
            
            if (window.innerWidth <= 768) {
                // En móviles, solo alternar la clase collapsed
                sidebar.classList.toggle('collapsed');
                
                // Si el sidebar está abierto, mostrar overlay
                if (!sidebar.classList.contains('collapsed')) {
                    toggleOverlay(true);
                } else {
                    toggleOverlay(false);
                }
            } else {
                // En escritorio, usar collapsed/expanded normalmente
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Guardar preferencia
                if (sidebar.classList.contains('collapsed')) {
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }
            }
        });
    }
    
    // Cerrar sidebar al hacer clic en el overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.add('collapsed');
        toggleOverlay(false);
    });

    // Cerrar sidebar al hacer clic en enlaces (en móviles)
    const navLinks = document.querySelectorAll('.sidebar .nav-btn');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                toggleOverlay(false);
            }
        });
    });
    
    // Cerrar sidebar al hacer clic fuera de él (en móviles)
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleBtn = toggleBtn.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggleBtn && !sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('collapsed');
                toggleOverlay(false);
            }
        }
    });
});

// Función para mostrar alertas
window.mostrarAlerta = function(mensaje, tipo) {
    const alertaHTML = `
        <div class="custom-alert alert alert-${tipo} alert-dismissible fade show">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML += alertaHTML;
        
        // Auto-cerrar después de 5 segundos
        setTimeout(function() {
            const alertas = document.querySelectorAll('.custom-alert');
            if (alertas.length > 0) {
                alertas[0].classList.remove('show');
                setTimeout(() => alertas[0].remove(), 150);
            }
        }, 5000);
    }
};