/**
 * @file admin-usuarios-updates.js
 * @description
 * Funciones para manejar la lógica de actualización de datos en la sección de administración,
 * incluyendo actualización de roles de usuarios, activación y desactivación de usuarios,
 * y actualización de estado de aprobación de usuarios.
 * 
 * Se encarga de:
 * - Cambiar el rol de un usuario.
 * - Actualizar el estado de aprobación de un usuario.
 * - Desactivar un usuario.
 * - Reactivar un usuario.
 * 
 * De esta manera se evita tener forms anidados y se puede usar el mismo formulario para actualizar
 * varios usuarios a la vez (acciones masivas).
 */

function cambiarRolUsuario(idUsuario) {
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';
    const select = document.getElementById('nuevo_rol_' + idUsuario);
    const nuevoRol = select ? select.value : '';

    fetch('index.php?action=cambiar-rol', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_usuario=${idUsuario}&nuevo_rol=${encodeURIComponent(nuevoRol)}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        mostrarToast(`Rol actualizado a "${nuevoRol}"`);
    })
    .catch(() => {
        mostrarToast('Error al actualizar el rol.', true);
    });
}

function actualizarEstadoAprobacionUsuario(idUsuario, nuevoEstado) {
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    fetch('index.php?action=actualizar-estado-aprobacion-usuario', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_usuario=${idUsuario}&nuevo_estado_aprobacion=${encodeURIComponent(nuevoEstado)}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', `Estado del usuario cambiado a "${nuevoEstado}"`);
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al actualizar el estado.', true);
    });
}

function desactivarUsuario(idUsuario) {
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    fetch('index.php?action=desactivar-usuario', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_usuario=${idUsuario}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', 'Usuario desactivado');
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al desactivar el usuario.', true);
    });
}

function reactivarUsuario(idUsuario) {
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    fetch('index.php?action=reactivar-usuario', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_usuario=${idUsuario}&csrf_token=${encodeURIComponent(csrfToken)}`
    })
    .then(() => {
        sessionStorage.setItem('toastMsg', 'Usuario reactivado');
        sessionStorage.setItem('toastError', 'false');
        location.reload();
    })
    .catch(() => {
        mostrarToast('Error al reactivar el usuario.', true);
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