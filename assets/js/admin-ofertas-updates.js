/**
 * @file admin-ofertas-updates.js
 * @description
 * Funciones para manejar la lógica de actualización de datos en las ofertas en la sección de administración,
 * 
 * Se encarga de:
 * - Actualizar el estado de aprobación de una oferta.
 * - Finalizar una oferta.
 * - Reabrir una oferta.
 * - Eliminar una oferta
 * 
 * De esta manera se evita tener forms anidados y se puede usar el mismo formulario para actualizar
 * varias ofertas a la vez (acciones masivas).
 */


function actualizarEstadoAprobacionOferta(idOferta, nuevoEstado) {
     // Buscar el input hidden del token CSRF en la página
     const csrfInput = document.querySelector('input[name="csrf_token"]');
     const csrfToken = csrfInput ? csrfInput.value : '';
     
    fetch('index.php?action=actualizar-estado-aprobacion-oferta', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_oferta=${idOferta}&nuevo_estado_aprobacion=${encodeURIComponent(nuevoEstado)}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', `Estado de la oferta actualizado a "${nuevoEstado}"`);
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al actualizar el estado de la oferta.', true);
    });
}

function finalizarOferta(idOferta) {
    // Buscar el input hidden del token CSRF en la página
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    fetch('index.php?action=desactivar-oferta', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_oferta=${idOferta}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', 'Oferta finalizada');
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al finalizar la oferta.', true);
    });
}

function reactivarOferta(idOferta, fechaFin) {
    // Buscar el input hidden del token CSRF en la página
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    // Validar si la fecha de fin es anterior a hoy
    if (fechaFin) {
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0); // sólo comparar fecha, no hora
        const fecha = new Date(fechaFin);

        if (fecha < hoy) {
            const confirmacion = confirm("La fecha de finalización ya pasó. Al reactivar esta oferta, la fecha se eliminará. ¿Desea continuar?");
            if (!confirmacion) return;
        }
    }

    fetch('index.php?action=reactivar-oferta', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_oferta=${idOferta}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', 'Oferta reactivada');
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al reactivar la oferta.', true);
    });
}

function eliminarOferta(idOferta) {
    // Buscar el input hidden del token CSRF en la página
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    // agregar alert javascript de confirmación 
    if (!confirm('¿Seguro que querés eliminar esta oferta? Ya no será accesible')) {
        return;
    }   

    fetch('index.php?action=eliminar-oferta-admin', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_oferta=${idOferta}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', 'Oferta eliminada');
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al eliminar la oferta.', true);
    });
}

function mostrarToast(mensaje, esError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast' + (esError ? ' error' : ' success');
    toast.textContent = mensaje;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    const mensaje = sessionStorage.getItem('toastMsg');
    const esError = sessionStorage.getItem('toastError') === 'true';

    if (mensaje) {
        mostrarToast(mensaje, esError);
        sessionStorage.removeItem('toastMsg');
        sessionStorage.removeItem('toastError');
    }
});