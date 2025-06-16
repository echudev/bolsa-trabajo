/**
 * @file admin-filtros.js
 * @description
 * Funciones para manejar la lógica de filtros y selección masiva en la sección de administración,
 * incluyendo actualización del texto de resumen en los dropdowns y manejo del checkbox
 * para seleccionar/desseleccionar todos los elementos.
 *
 * @see admin_ofertas.php
 * @see admin_usuarios.php
 */

document.addEventListener('DOMContentLoaded', function () {
    // “Seleccionar todos”
    const maestro = document.getElementById('seleccionar-todos');
    const checkboxes = document.querySelectorAll('input[name="seleccionados[]"]');

    if (maestro) {
        /**
         * Escucha el cambio en el checkbox maestro y marca/desmarca
         * todos los checkboxes individuales.
         *
         * @param {Event} _event - Evento de cambio en el checkbox maestro.
         */
        maestro.addEventListener('change', () => {
            checkboxes.forEach((chk) => {
                chk.checked = maestro.checked;
            });
        });
    }

    // Actualiza el texto de los dropdowns según lo seleccionado.
    actualizarResumenSeleccion();

    // Cada vez que cambia un checkbox dentro de un dropdown, actualiza el resumen.
    document.querySelectorAll('.dropdown-opciones input[type="checkbox"]').forEach(chk => {
        chk.addEventListener('change', actualizarResumenSeleccion);
    });

    /**
     * Recorre todos los contenedores con clase `.filtro-dropdown` y actualiza
     * el texto del título según los ítems seleccionados.
     *
     * - Si no hay seleccionados, muestra el texto base del atributo `data-base`.
     * - Si hay 1 o 2 elementos, los concatena con “, ”.
     * - Si hay más de 2, muestra los primeros 2 y la cantidad restante con “+N más”.
     *
     * @returns {void}
     */
    function actualizarResumenSeleccion() {
        document.querySelectorAll('.filtro-dropdown').forEach(dropdown => {
            // Obtener todos los labels de los checkboxes marcados dentro del dropdown.
            const seleccionados = Array.from(
                dropdown.querySelectorAll('.dropdown-opciones input[type="checkbox"]:checked')
            ).map(cb => cb.parentElement.textContent.trim());

            const titulo = dropdown.querySelector('.dropdown-titulo');
            // Texto base si no hay nada seleccionado.
            const baseTexto = titulo.getAttribute('data-base') || 'Seleccionar';
            let resumen = '';
            if (seleccionados.length === 0) {
                resumen = baseTexto;
            } else if (seleccionados.length <= 2) {
                resumen = seleccionados.join(', ');
            } else {
                resumen = `${seleccionados.slice(0, 2).join(', ')} +${seleccionados.length - 2} más`;
            }

            // Actualizar el texto visible y el atributo title (tooltip).
            titulo.childNodes[0].textContent = resumen + ' ';
            titulo.setAttribute('title', seleccionados.join(', '));
        });
    }

    /**
     * Abre o cierra un dropdown de filtros. Cierra todos los demás dropdowns
     * que estén abiertos antes de alternar el seleccionado.
     *
     * @param {HTMLElement} el - El elemento `.dropdown-titulo` clickeado.
     * @returns {void}
     */
    window.toggleDropdown = function (el) {
        const parent = el.parentElement;
        document.querySelectorAll('.filtro-dropdown').forEach(d => {
            if (d !== parent) d.classList.remove('abierto');
        });
        parent.classList.toggle('abierto');
    };


    /**
   * Escucha clics en todo el documento: si se hace clic fuera de cualquier
   * `.filtro-dropdown`, cierra todos los dropdowns abiertos.
   *
   * @param {MouseEvent} e - Evento de clic en el documento.
   * @returns {void}
   */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.filtro-dropdown')) {
            document.querySelectorAll('.filtro-dropdown').forEach(d => d.classList.remove('abierto'));
        }
    });

    /**
     * Valida que se haya seleccionado al menos un elemento y una acción válida.
     * En el contexto de administración de ofertas:
     * - Si se selecciona la acción "reactivar", se muestra una advertencia sobre la eliminación de la fecha de fin.
     * - Si se selecciona la acción "eliminar", se muestra una advertencia sobre la eliminación permanente.
     */
    document.querySelectorAll('form[action*="accion-masiva"]').forEach(formEl => {
        formEl.addEventListener('submit', function (e) {
            // Obtener todos los checkboxes seleccionados
            const seleccionados = formEl.querySelectorAll('input[name="seleccionados[]"]:checked');
            // Obtener el valor del select de acción
            const accionSelect = formEl.querySelector('select[name="accion"]');
            const accionValue = accionSelect ? accionSelect.value : '';
            const context = formEl.getAttribute('data-context');

            if (seleccionados.length === 0 || !accionValue) {
                e.preventDefault();
                alert('Se debe seleccionar al menos un elemento y una acción válida.');
                return;
            } 
            if (context === 'admin-ofertas') {
                if (accionValue === 'reabrir') {
                    const confirmacion = confirm("Si las ofertas seleccionadas tenían una fecha de finalización pasada, esta se eliminará. ¿Desea continuar?");
                    if (!confirmacion) {
                        e.preventDefault();
                    }
                } else if (accionValue === 'eliminar') {
                    const confirmacion = confirm("Las ofertas seleccionadas serán eliminadas y ya no estarán disponibles. ¿Desea continuar?");
                    if (!confirmacion) {
                        e.preventDefault();
                    }
                }
            }
        });
    });

});
