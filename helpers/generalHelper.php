<?php
/**
 * Helpers generales para la aplicación
 */


/**
 * Redirige a una URL, opcionalmente con un mensaje flash.
 * Si la petición es POST, regenera el token CSRF tras la redirección.
 * 
 * @param string $ruta Ruta a la que redirigir (relativa o absoluta)
 * @param string|null $mensaje Mensaje flash a mostrar (opcional)
 * @param string $tipo Tipo de mensaje ('error', 'exito', 'advertencia', etc.)
 * @return void
 */
function redirigir($ruta, $mensaje = null, $tipo = 'error') {
    // Establecer mensaje si se proporciona
    if ($mensaje !== null) {
        setMensaje($tipo, $mensaje);
    }
    
    // Si es POST, regenerar token CSRF después de un redirigir exitoso
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        regenerarTokenCSRF();
    }

    // Si la ruta ya es una URL completa, redirigir directamente
    if (filter_var($ruta, FILTER_VALIDATE_URL)) {
        header("Location: $ruta");
        exit;
    }
    
    // Si la ruta comienza con 'http', asume que es una URL completa
    if (strpos($ruta, 'http') === 0) {
        header("Location: $ruta");
        exit;
    }
    
    // Si la ruta comienza con '?', agregar al script actual
    if (strpos($ruta, '?') === 0) {
        $ruta = 'index.php' . $ruta;
    }
    
    // Si la ruta no comienza con '/' ni con 'index.php', asume que es relativa al directorio base
    if (strpos($ruta, '/') !== 0 && strpos($ruta, 'index.php') !== 0) {
        $ruta = 'index.php?action=' . $ruta;
    }
    
    // Construir la URL base
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Construir la URL final
    $baseUrl = rtrim($scriptPath === '/' ? '' : $scriptPath, '/');
    $url = "$protocol://$host$baseUrl/$ruta";
    
    // Redirigir
    header("Location: $url");
    exit;
}

/**
 * Redirige a una acción con URL absoluta, opcionalmente con un mensaje flash.
 * Si la petición es POST, regenera el token CSRF tras la redirección.
 * 
 * (para flujos como restablecimiento de contraseña o correos, donde se necesita una URL completa).
 * 
 * @param string $action Acción a la que redirigir (por ejemplo 'login', 'olvido-password', etc.)
 * @param string|null $mensaje Mensaje a mostrar (opcional)
 * @param string $tipo Tipo de mensaje (por defecto: 'error', puede ser 'exito', 'advertencia', etc.)
 * @return void
 */
function redirigirAbsoluto($action, $mensaje = null, $tipo = 'error') {
    // Establecer mensaje si se proporciona
    if ($mensaje !== null) {
        setMensaje($tipo, $mensaje);
    }

    // Construir la URL base absoluta
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = rtrim($scriptPath === '/' ? '' : $scriptPath, '/');

    // Construir la URL final
    $url = "$protocol://$host$baseUrl/index.php?action=$action";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        regenerarTokenCSRF();
    }

    // Redirigir
    header("Location: $url");
    exit;
}


/**
 * Establece un mensaje flash en la sesión
 * 
 * @param string $tipo Tipo de mensaje ('error', 'exito', 'advertencia', etc.)
 * @param string $mensaje Mensaje a mostrar
 * @return void
 */
function setMensaje($tipo, $mensaje) {
    // Aseguramos que el tipo sea uno de los valores esperados
    $tiposPermitidos = ['error', 'exito', 'advertencia', 'info', 'mensaje'];
    $tipo = in_array($tipo, $tiposPermitidos) ? $tipo : 'mensaje';
    
    $_SESSION[$tipo] = $mensaje;
}



/**
 * Escapa un valor para uso seguro en HTML
 * 
 * @param string $valor Valor a escapar
 * @return string Valor escapado
 */
function e($valor) {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}


