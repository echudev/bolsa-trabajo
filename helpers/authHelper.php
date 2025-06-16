<?php
// Incluir helpers generales
require_once __DIR__ . '/generalHelper.php';
require_once __DIR__ . '/../config/constantes.php';

// Iniciar la sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    /**
     * Configuración de seguridad para las cookies de sesión
     * 
     * session.cookie_httponly = 1: Impide que JavaScript acceda a la cookie (protección XSS)
     * session.use_only_cookies = 1: Asegura que solo se usen cookies para almacenar el ID de sesión
     * session.cookie_secure: Solo envía la cookie a través de HTTPS si está disponible
     * session.cookie_samesite = 'Lax': Protege contra ataques CSRF
     */
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.cookie_samesite', 'Lax');

    // Iniciar la sesión
    session_start();
}

// $_SESSION['intentos_login'] = 0; DEBUG, PARA INICIAR SESIÓN DESPUÉS DE USAR TODOS LOS INTENTOS. SACAR

/**
 * Limpia los intentos de inicio de sesión fallidos después de 30 minutos
 * Esto evita bloqueos prolongados por intentos fallidos
 */
if (isset($_SESSION['intentos_login']) && isset($_SESSION['ultimo_intento'])) {
    if (time() - $_SESSION['ultimo_intento'] > TIEMPO_BLOQUEO_LOGIN) {
        unset($_SESSION['intentos_login']);
        unset($_SESSION['ultimo_intento']);
    }
}


// 1. GESTIÓN DE SESIÓN

/**
 * Inicia una nueva sesión de usuario con las credenciales proporcionadas.
 * Regenera el ID de sesión para evitar ataques de fijación de sesión.
 * También inicializa el tiempo de inactividad permitido.
 *
 * @param array $usuario Array con los datos del usuario (id_usuario, rol, nombre)
 */
function iniciarSesion($usuario)
{
    // Asegurarse de que la sesión esté iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Regenera el ID de sesión para prevenir fijación de sesión (ataque donde un atacante fuerza a una víctima a usar un ID de sesión conocido)
    session_regenerate_id(true);

    // Almacenar información básica del usuario en la sesión
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];

    // Limpiar contador de intentos fallidos si existe
    if (isset($_SESSION['intentos_login'])) {
        unset($_SESSION['intentos_login']);
        unset($_SESSION['ultimo_intento']);
    }

    // Configurar tiempo de inactividad para la sesión
    $_SESSION['ultima_actividad'] = time();
    $_SESSION['tiempo_expiracion'] = TIEMPO_EXPIRACION_SESION;
}

/**
 * Cierra la sesión actual, limpia las variables de sesión,
 * elimina la cookie de sesión y destruye la sesión.
 * También regenera el token CSRF para prevenir reuso de tokens.
 *
 * @return void
 */
function cerrarSesion()
{
    // Limpiar todas las variables de sesión
    $_SESSION = [];

    // eliminar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destruir la sesión
    session_destroy();

    // Regenerar token CSRF tras logout, por seguridad
    regenerarTokenCSRF();
}

/**
 * Verifica si el usuario actual tiene una sesión activa (logueado).
 *
 * @return bool true si está logueado, false en caso contrario.
 */
function estaLogueado()
{
    return isset($_SESSION['usuario_id']);
}


/**
 * Redirige al usuario a una página determinada si está logueado.
 *
 * @param string $destino Ruta de redirección
 */
function redirigirSiYaEstaLogueado(string $destino = 'index.php?action=listado'): void {
    if (estaLogueado()) {
        redirigir($destino);
    }
}

/**
 * Obtiene el ID del usuario actual de la sesión.
 *
 * @return int|null ID del usuario o null si no está logueado.
 */
function idUsuario()
{
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el tipo de usuario actual basado en la sesión.
 *
 * @return string Rol del usuario actual o 'invitado' si no está autenticado
 */
function tipoUsuario()
{
    return $_SESSION['usuario_rol'] ?? 'invitado';
}

/**
 * Función  para verificar si la sesión ha expirado por inactividad
 * 
 * @return void
 */
function verificarSesionExpirada() {
    // Solo verificar si existen los datos necesarios en la sesión
    if (isset($_SESSION['ultima_actividad'], $_SESSION['tiempo_expiracion'])) {
        $tiempo_transcurrido = time() - $_SESSION['ultima_actividad'];

        // Si ha pasado más tiempo del permitido sin actividad
        if ($tiempo_transcurrido > $_SESSION['tiempo_expiracion']) {
            // Cerrar la sesión actual
            cerrarSesion();

            // Redirigir al login con  mensaje de error
            redirigir('index.php?action=login', 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.', 'error');
        } else {
            // Actualizar el tiempo de última actividad
            $_SESSION['ultima_actividad'] = time();
        }
    }
};

// 2. CONTROL DE ACCESO

/**
 * Verifica si el usuario está autenticado y si su sesión sigue siendo válida.
 * Si no lo está, o si la sesión expiró por inactividad, redirige al formulario de login con un mensaje de error.
 *
 * @return void
 */
function accesoSoloLogueado()
{
    // Verificar si el usuario está logueado
    if (!estaLogueado()) {
        // Guardar la URL actual para redirigir después del login
        // Solo para peticiones GET que no sean acciones específicas
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
            $_SESSION['url_anterior'] = $_SERVER['REQUEST_URI'];
        }

        // Mensaje de error para mostrar en el formulario de login y Redirigir al login
        redirigir('index.php?action=login', 'Debés iniciar sesión para acceder a esa página.', 'error');
    } else {
        // Si el usuario está logueado, verificar tiempo de inactividad
        verificarSesionExpirada();
    }
}

/**
 * Verifica que el usuario actual sea administrador.
 * Si no lo es, redirige al inicio con un mensaje de error.
 *
 * @return void
 */
function accesoSoloAdmin()
{
    verificarSesionExpirada();
    if (!esAdmin()) {
        redirigir('index.php', 'Acceso denegado.', 'error');
    }
}

// 3. ROLES

function esAdmin()
{
    return tipoUsuario() === 'administrativo';
}

function esProfesor()
{
    return tipoUsuario() === 'profesor';
}

function esEstudiante()
{
    return tipoUsuario() === 'estudiante';
}

/**
 * Verifica si el usuario actual es el usuario con el ID especificado.
 *
 * @param int $id ID a comparar con el usuario actual.
 * @return bool true si corresponde al usuario actual, false en caso contrario.
 */
function esElUsuarioActual($id)
{
    return isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id;
}

// 4. PERMISOS SOBRE OFERTAS

/**
 * Verifica si el usuario actual tiene permiso para crear ofertas.
 *
 * @return bool true si puede crear ofertas, false en caso contrario.
 */
function puedeCrearOferta()
{
    return in_array(tipoUsuario(), ['profesor', 'estudiante', 'administrativo']);
}

/**
 * Verifica si el usuario actual puede editar la oferta especificada.
 * Solo administradores o el autor de la oferta pueden editarla.
 *
 * @param array $oferta Datos de la oferta.
 * @return bool true si puede editarla, false en caso contrario.
 */
function puedeEditarOferta($oferta)
{
    if (!estaLogueado()) return false;

    return esAdmin() || (
        in_array(tipoUsuario(), ['profesor', 'estudiante']) &&
        isset($oferta['publicada_por']) &&
        $oferta['publicada_por'] == idUsuario()
    );
}

/**
 * Verifica si el usuario actual puede eliminar la oferta especificada.
 * Por consistencia, reutiliza la lógica de puedeEditarOferta.
 *
 * @param array $oferta Datos de la oferta.
 * @return bool true si puede eliminarla, false en caso contrario.
 */
function puedeEliminarOferta($oferta)
{
    return puedeEditarOferta($oferta);
}


// 5. PROTECCIÓN CSRF

/**
 * Genera un nuevo token CSRF y lo almacena en la sesión si no existe.
 * Si ya existe un token, lo devuelve sin modificarlo.
 *
 * @return string Token CSRF generado o existente
 */
function generarTokenCSRF()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida un token CSRF comparándolo con el almacenado en la sesión.
 *
 * @param string $token Token a validar
 * @return bool true si el token es válido, false en caso contrario
 */
function validarTokenCSRF($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Genera un campo de formulario oculto que contiene el token CSRF.
 * Este campo debe incluirse en todos los formularios que realicen acciones que modifiquen datos.
 *
 * @return string Código HTML del campo oculto con el token CSRF
 */
function campoCSRF()
{
    $token = generarTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Valida el token CSRF enviado en una petición POST.
 * Si la validación falla, redirige a la página anterior mostrando un mensaje de error.
 * Esta función debe llamarse al inicio de cualquier controlador que procese formularios POST.
 *
 * @return void
 */
function validarCSRF()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validarTokenCSRF($token)) {
            redirigir($_SERVER['HTTP_REFERER'] ?? 'index.php', 'Token CSRF inválido. Por favor, intente nuevamente.', 'error');
        }
    }
}

/**
 * Fuerza la regeneración del token CSRF (para invalidarlo después de un POST exitoso).
 *
 * @return void
 */
function regenerarTokenCSRF()
{
    unset($_SESSION['csrf_token']);
}
