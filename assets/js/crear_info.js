$(document).ready(function() {
    // Variables globales
    let tipoActual = 'marcas';
    let elementoSeleccionado = null;
    let modoEdicion = false;
    
    // Detectar cambio de pestaña
    $('#inventarioTabs button').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('id');
        
        if (target === 'marcas-tab') {
            tipoActual = 'marcas';
        } else if (target === 'familias-tab') {
            tipoActual = 'familias';
        } else if (target === 'ubicaciones-tab') {
            tipoActual = 'ubicaciones';
        }
        
        // Resetear selección
        elementoSeleccionado = null;
        actualizarBotonesAccion();
    });
    
    // Seleccionar fila de tabla
    $('.table-excel tbody').on('click', 'tr', function() {
        const $this = $(this);
        
        // Deseleccionar fila anterior
        $this.closest('tbody').find('tr.selected').removeClass('selected');
        
        // Seleccionar nueva fila
        $this.addClass('selected');
        
        // Guardar referencia al elemento seleccionado
        elementoSeleccionado = {
            id: $this.data('id'),
            nombre: $this.find('td:eq(1)').text(),
            tipo: tipoActual
        };
        
        // Actualizar estado de botones
        actualizarBotonesAccion();
    });
    
    // Actualizar estado de botones de acción
    function actualizarBotonesAccion() {
        if (elementoSeleccionado) {
            $(`#editar${capitalizarPrimeraLetra(tipoActual.slice(0, -1))}`).prop('disabled', false);
            $(`#eliminar${capitalizarPrimeraLetra(tipoActual.slice(0, -1))}`).prop('disabled', false);
        } else {
            $(`#editar${capitalizarPrimeraLetra(tipoActual.slice(0, -1))}`).prop('disabled', true);
            $(`#eliminar${capitalizarPrimeraLetra(tipoActual.slice(0, -1))}`).prop('disabled', true);
        }
    }
    
    // Función para capitalizar primera letra
    function capitalizarPrimeraLetra(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    // Filtrado de tablas
    $('#buscarMarca').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaMarcas tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });
    
    $('#buscarFamilia').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaFamilias tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });
    
    $('#buscarUbicacion').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaUbicaciones tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });
    
    // Abrir modal para agregar
    $('#agregarMarca').click(function() {
        abrirModal('marca', false);
    });
    
    $('#agregarFamilia').click(function() {
        abrirModal('familia', false);
    });
    
    $('#agregarUbicacion').click(function() {
        abrirModal('ubicacion', false);
    });
    
    // Abrir modal para editar
    $('#editarMarca').click(function() {
        abrirModal('marca', true);
    });
    
    $('#editarFamilia').click(function() {
        abrirModal('familia', true);
    });
    
    $('#editarUbicacion').click(function() {
        abrirModal('ubicacion', true);
    });
    
    // Función para abrir modal
    function abrirModal(tipo, editar) {
        modoEdicion = editar;
        
        // Resetear formulario
        $(`#${tipo}Form`)[0].reset();
        
        // Cambiar título del modal
        const accion = editar ? 'Editar' : 'Agregar';
        $(`#${tipo}ModalLabel`).text(`${accion} ${capitalizarPrimeraLetra(tipo)}`);
        
        // Si es edición, cargar datos
        if (editar && elementoSeleccionado) {
            $(`#${tipo}_id`).val(elementoSeleccionado.id);
            $(`#${tipo}_nombre`).val(elementoSeleccionado.nombre);
        } else {
            $(`#${tipo}_id`).val('');
        }
        
        // Mostrar modal
        $(`#${tipo}Modal`).modal('show');
    }
    
    // Guardar elemento
    $('#guardarMarca').click(function() {
        guardarElemento('marca');
    });
    
    $('#guardarFamilia').click(function() {
        guardarElemento('familia');
    });
    
    $('#guardarUbicacion').click(function() {
        guardarElemento('ubicacion');
    });
    
    // Función para guardar elemento
    function guardarElemento(tipo) {
        // Validar formulario
        if (!$(`#${tipo}_nombre`).val()) {
            mostrarAlerta('Por favor, ingrese un nombre.', 'warning');
            return;
        }
        
        // Obtener datos del formulario
        const id = $(`#${tipo}_id`).val();
        const nombre = $(`#${tipo}_nombre`).val();
        
        // Preparar datos para enviar
        const datos = {
            id: id,
            nombre: nombre
        };
        
        // URL de la acción
        const url = modoEdicion 
            ? `${base_url}inventario/actualizar_${tipo}`
            : `${base_url}inventario/agregar_${tipo}`;
        
        // Enviar datos al servidor
        $.ajax({
            url: url,
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.exito) {
                    // Cerrar modal
                    $(`#${tipo}Modal`).modal('hide');
                    
                    // Mostrar mensaje de éxito
                    mostrarAlerta(respuesta.mensaje, 'success');
                    
                    // Recargar página para ver cambios
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarAlerta(respuesta.mensaje, 'danger');
                }
            },
            error: function() {
                mostrarAlerta('Error al procesar la solicitud.', 'danger');
            }
        });
    }
    
    // Abrir modal para confirmar eliminación
    $('#eliminarMarca, #eliminarFamilia, #eliminarUbicacion').click(function() {
        if (!elementoSeleccionado) return;
        
        // Ocultar mensaje de validación
        $('#mensajeValidacion').addClass('d-none');
        
        // Mostrar modal de confirmación
        $('#confirmarEliminarModal').modal('show');
    });
    
    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        if (!elementoSeleccionado) return;
        
        // Verificar si el elemento está siendo utilizado
        $.ajax({
            url: `${base_url}inventario/verificar_uso_${elementoSeleccionado.tipo.slice(0, -1)}/${elementoSeleccionado.id}`,
            type: 'GET',
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.enUso) {
                    // Mostrar mensaje de validación
                    $('#mensajeValidacion').removeClass('d-none');
                    $('#confirmarEliminar').prop('disabled', true);
                } else {
                    // Eliminar elemento
                    eliminarElemento();
                }
            },
            error: function() {
                mostrarAlerta('Error al verificar el uso del elemento.', 'danger');
                $('#confirmarEliminarModal').modal('hide');
            }
        });
    });
    
    // Función para eliminar elemento
    function eliminarElemento() {
        if (!elementoSeleccionado) return;
        
        $.ajax({
            url: `${base_url}inventario/eliminar_${elementoSeleccionado.tipo.slice(0, -1)}/${elementoSeleccionado.id}`,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                // Cerrar modal
                $('#confirmarEliminarModal').modal('hide');
                
                if (respuesta.exito) {
                    // Mostrar mensaje de éxito
                    mostrarAlerta(respuesta.mensaje, 'success');
                    
                    // Recargar página para ver cambios
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarAlerta(respuesta.mensaje, 'danger');
                }
            },
            error: function() {
                $('#confirmarEliminarModal').modal('hide');
                mostrarAlerta('Error al eliminar el elemento.', 'danger');
            }
        });
    }
    
    // Resetear modal de confirmación al cerrarlo
    $('#confirmarEliminarModal').on('hidden.bs.modal', function () {
        $('#mensajeValidacion').addClass('d-none');
        $('#confirmarEliminar').prop('disabled', false);
    });
    
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo) {
        if (window.mostrarAlerta) {
            window.mostrarAlerta(mensaje, tipo);
        } else {
            const alertaHTML = `
                <div class="custom-alert alert alert-${tipo} alert-dismissible fade show">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#alertContainer').append(alertaHTML);
            
            // Auto-cerrar después de 5 segundos
            setTimeout(function() {
                $('.custom-alert').alert('close');
            }, 5000);
        }
    }
});