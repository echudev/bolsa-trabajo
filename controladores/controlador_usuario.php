<?php

/**
 * Controlador de Usuarios
 * 
 * Maneja todas las acciones relacionadas con usuarios: login, registro, perfil, etc.
 */

// Cargar modelos
require_once 'modelos/UsuarioModel.php';
require_once 'modelos/ConfiguracionSistemaModel.php';

// Cargar helpers
require_once 'helpers/authHelper.php';
require_once 'helpers/PaginadorHelper.php';
require_once 'helpers/MailerHelper.php';
require_once 'helpers/generalHelper.php';

// Cargar constantes
require_once 'config/constantes.php';


/**
 * Controlador principal de acciones relacionadas con usuarios.
 * 
 * @param string $accion Acción solicitada (login, registro, perfil, etc.)
 * @return void
 */
function controladorUsuario($accion)
{
    $usuarioModel = new UsuarioModel();

    switch ($accion) {
        case 'login':
            redirigirSiYaEstaLogueado();
            include 'vistas/login.php';
            return;

        case 'procesar-login':
            procesarLogin($usuarioModel);
            return;

        case 'logout':
            cerrarSesion();
            redirigir('index.php', 'Sesión cerrada correctamente.', 'exito');
            return;

        case 'registro':
            redirigirSiYaEstaLogueado();
            include 'vistas/registro.php';
            return;

        case 'procesar-registro':
            procesarRegistro($usuarioModel);
            return;

        case 'perfil':
            mostrarFormularioPerfil($usuarioModel);
            return;

        case 'procesar-perfil':
            procesarPerfil($usuarioModel);
            return;

        case 'olvido-pass':
            redirigirSiYaEstaLogueado();
            include 'vistas/user_olvido_pass.php';
            return;

        case 'procesar-olvido-pass':
            procesarOlvidoPass($usuarioModel);
            return;

        case 'restablecer-pass':
            mostrarFormularioRestablecerPass($usuarioModel);
            return;

        case 'procesar-restablecer-pass':
            procesarrestablecerPass($usuarioModel);
            return;

        default:
            echo "Acción de usuario no reconocida.";
    }
}

/**
 * Procesa el formulario de inicio de sesión
 * 
 * Valida los datos ingresados por el usuario (email y contraseña), verifica los intentos fallidos para prevenir ataques de fuerza bruta,
 * comprueba las credenciales contra la base de datos, e inicia sesión si son correctas.
 * Si la cuenta no está aprobada o las credenciales son incorrectas, informa el error correspondiente.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario
 */
function procesarLogin($usuarioModel)
{
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('index.php?action=login');
    }
    // Validación CSRF
    validarCSRF();

    // Inicializar contador de intentos si no existe / Bloquear después de 5 intentos fallidos por 30 minutos
    verificarIntentosLogin();

    // Obtener y validar datos del formulario
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar email
    $mensajeEmail = validarEmail($email, true);
    if ($mensajeEmail !== null) {
        redirigir('index.php?action=login', $mensajeEmail, 'error');
    }
    // Validar que password no esté vacía
    $mensajePassword = validarCampoRequerido($password);
    if ($mensajePassword !== null) {
        redirigir('index.php?action=login', $mensajePassword, 'error');
    }

    // Obtener usuario por email
    $usuario = $usuarioModel->obtenerPorEmail($email);
    $hashPassword = $usuario ? $usuario['password'] : null; // para evitar ataques de tiempo si no existe el usuario

    // Verificar credenciales con tiempo constante para evitar ataques de tiempo
    $credencialesCorrectas = verificarCredenciales($password, $hashPassword);

    if ($credencialesCorrectas) {
        // Verificar si la cuenta está aprobada
        if (isset($usuario['estado_aprobacion']) && $usuario['estado_aprobacion'] !== 'aprobado') {
            $mensaje = $usuario['estado_aprobacion'] === 'rechazado'
                ? 'Tu cuenta fue rechazada por el administrador.'
                : 'Tu cuenta aún no fue aprobada por un administrador.';
            redirigir('index.php?action=login', $mensaje, 'error');
        }

        // Credenciales correctas, iniciar sesión
        iniciarSesion($usuario);

        // Redirigir según el rol del usuario
        $ruta = esAdmin() ? 'index.php?action=admin-panel&seccion=usuarios' : 'index.php?action=listado';
        redirigir($ruta);
    } else {
        sumarIntentoFallido(); // Manejar intento fallido

        $intentos_restantes = MAX_INTENTOS_LOGIN - $_SESSION['intentos_login'];

        $mensaje_error = 'Email o contraseña incorrectos.';

        if ($intentos_restantes <= 3 && $intentos_restantes > 0) {
            $mensaje_error .= " Te quedan $intentos_restantes intentos.";
        } elseif ($intentos_restantes <= 0) {
            $tiempo_restante = ceil((TIEMPO_BLOQUEO_LOGIN - (time() - $_SESSION['ultimo_intento'])) / 60);
            $mensaje_error = "Demasiados intentos fallidos. Por favor, intente nuevamente en $tiempo_restante minutos.";
        }

        redirigir('index.php?action=login', $mensaje_error, 'error');
    }
}

/**
 * Procesa el formulario de registro de nuevos usuarios.
 * 
 * Valida los datos ingresados (nombre, apellido, email, contraseña), verifica si el email ya está en uso,
 * y crea un nuevo usuario en la base de datos.
 * Redirige a la pantalla de login si el registro es exitoso, o muestra los errores en caso contrario.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 */
function procesarRegistro($usuarioModel)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validación CSRF
        validarCSRF();

        $errores = [];

        // Sanitización
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contrasena = $_POST['password'] ?? '';

        // Validar nombre, apellido, email
        validarDatosUsuario($nombre, $apellido, $email, $usuarioModel, $errores);

        // Validar password
        $mensajePassword = validarPassword($contrasena);
        if ($mensajePassword !== null) {
            $errores[] = $mensajePassword;
        }

        // Si hay errores → volver a registro
        if (!empty($errores)) {
            redirigir('index.php?action=registro', implode('<br>', $errores), 'error');
        }

        // Hashear password
        $opciones = ['cost' => 12];
        $passHash = password_hash($contrasena, PASSWORD_DEFAULT, $opciones);

        // Obtener config de sistema
        $configModel = new ConfiguracionSistemaModel();
        $requiere_aprobacion = $configModel->obtenerValor('aprobar_registros') === 'true';

        // Datos para crear
        $datos = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'password' => $passHash,
            'rol' => 'estudiante', // por defecto
            'estado_aprobacion' => $requiere_aprobacion ? 'pendiente' : 'aprobado'
        ];

        // Intentar crear usuario
        if ($usuarioModel->crear($datos)) {
            $mensaje = $requiere_aprobacion
                ? 'Registro enviado. Esperá aprobación del administrador.'
                : 'Registrado con éxito.';

            redirigir('index.php?action=login', $mensaje, 'exito');
        } else {

            redirigir('index.php?action=registro', 'No se pudo registrar el usuario (¿email repetido?).', 'error');
        }
    }
}

// ACCIONES DE PERFIL

/**
 * Muestra el formulario “Mi perfil” con los datos actuales del usuario.
 * Si el usuario no está autenticado, redirige a la página de inicio.
 * 
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario
 */
function mostrarFormularioPerfil(UsuarioModel $usuarioModel)
{
    // Verificar autenticación
    accesoSoloLogueado();

    // Obtener datos del usuario actual
    $id = idUsuario();
    $usuario = $usuarioModel->obtenerPorId($id);

    if (!$usuario) {
        redirigir('index.php', 'Usuario no encontrado.', 'error');
    }

    // Preparar datos para la vista
    $datosVista = [
        'titulo' => 'Editar mi perfil',
        'usuario' => [
            'id' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ]
    ];

    // Cargar la vista con los datos
    include 'vistas/perfil.php';
}

/**
 * Procesa el formulario de Perfil.
 * 
 * Valida que la contraseña actual sea correcta, valida los datos nuevos ingresados (nombre, apellido, email),
 * y opcionalmente actualiza la contraseña si se solicita.
 * 
 * Si hay errores, muestra los mensajes correspondientes. Si todo es correcto, actualiza los datos del perfil.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 */
function procesarPerfil(UsuarioModel $usuarioModel)
{
    accesoSoloLogueado();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('index.php?action=perfil');
    }

    // Validar token CSRF
    validarCSRF();

    $id               = idUsuario();
    $nombreNuevo      = trim($_POST['nombre']    ?? '');
    $apellidoNuevo    = trim($_POST['apellido']  ?? '');
    $emailNuevo       = trim($_POST['email']     ?? '');
    $passwordActual   = $_POST['password_actual']      ?? '';
    $nuevaPassword    = $_POST['nueva_password']   ?? '';
    $repitePassword   = $_POST['repite_password']  ?? '';

    $errores = [];

    // 1) Obtener usuario completo (con hash password)
    $usuarioBD = $usuarioModel->obtenerPorId($id);
    if (!$usuarioBD) {
        redirigir('index.php?action=perfil', 'Usuario no existe.', 'error');
    }

    // 2) Validar que password_actual no esté vacía
    $mensajePasswordActual = validarCampoRequerido($passwordActual);
    if ($mensajePasswordActual !== null) {
        $errores[] = $mensajePasswordActual;
    } else {
        // 3) Verificar que la contraseña actual coincida
        $credencialesCorrectas = verificarCredenciales($passwordActual, $usuarioBD['password']);
        if (!$credencialesCorrectas) {
            $errores[] = 'Contraseña actual incorrecta.';
        }
    }

    // 4) Validar nombre, apellido, email usando validarDatosUsuario:
    validarDatosUsuario($nombreNuevo, $apellidoNuevo, $emailNuevo, $usuarioModel, $errores, true, $id);

    // 4) Validar nueva contraseña (si se quiere cambiar)
    $hashNueva = null; // default → no actualizar password

    if ($nuevaPassword !== '' || $repitePassword !== '') {
        if ($nuevaPassword === '') {
            $errores[] = 'Si deseas cambiar la contraseña, la nueva no puede quedar vacía.';
        } elseif ($nuevaPassword !== $repitePassword) {
            $errores[] = 'La nueva contraseña y la confirmación no coinciden.';
        } else {
            // Si pasó validación de coincidencia, validar calidad de password:
            $mensajePassword = validarPassword($nuevaPassword);
            if ($mensajePassword !== null) {
                $errores[] = $mensajePassword;
            } else {
                $hashNueva = password_hash($nuevaPassword, PASSWORD_DEFAULT); // Si todo OK → generar hash
            }
        }
    }

    // 6) Si hay errores → volver a perfil con mensaje de error
    if (!empty($errores)) {
        redirigir('index.php?action=perfil', implode(' ', $errores), 'error');
    }

    // 7) Actualizar datos en modelo
    $exito = $usuarioModel->actualizarDatosPerfil(
        $id,
        $nombreNuevo,
        $apellidoNuevo,
        $emailNuevo,
        $hashNueva
    );

    if ($exito) {
        // Si cambió el nombre/apellido/email, actualizar la sesión:
        $_SESSION['usuario_nombre'] = $nombreNuevo;
        redirigir('index.php?action=perfil', 'Perfil actualizado correctamente.', 'exito');
    } else {
        redirigir('index.php?action=perfil', 'No se pudo actualizar el perfil. Probá más tarde.', 'error');
    }
}

// ACCIONES DE RECUPERACIÓN DE CONTRASEÑA

/**
 * Procesa la solicitud de recuperación de contraseña.
 * 
 * Valida el email ingresado, verifica si el usuario existe, y si es así genera un token de recuperación
 * que se envía por email al usuario.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 */
function procesarOlvidoPass(UsuarioModel $usuarioModel)
{
    // Validación CSRF
    validarCSRF();

    $email = trim($_POST['email'] ?? '');

    $mensajeEmail = validarEmail($email, true);
    if ($mensajeEmail !== null) {
        redirigir('index.php?action=olvido-pass', $mensajeEmail, 'error');
    }

    $usuario = $usuarioModel->obtenerPorEmail($email);

    if (!$usuario) {
        redirigir('index.php?action=olvido-pass', 'Email no encontrado.', 'error');
    }

    $token = bin2hex(random_bytes(32));
    $tokenExpiracion = time() + 3600;
    $usuarioModel->guardarToken($usuario['id_usuario'], $token, $tokenExpiracion);

    $host = $_SERVER['HTTP_HOST'];
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $url = "$protocolo://$host/bolsa-trabajo/index.php?action=restablecer-pass&token=" . urlencode($token);

    $mailer = new Mailer();
    $asunto = 'Restablecimiento de contraseña';
    $mensaje = "
        <html>
        <head>
            <title>Restablecimiento de contraseña</title>
        </head>
        <body>
            <p>Hola " . htmlspecialchars($usuario['nombre']) . ",</p>
            <p>Has solicitado restablecer tu contraseña. Por favor, haz clic en el siguiente enlace para continuar:</p>
            <p><a href='" . htmlspecialchars($url) . "' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Restablecer contraseña</a></p>
            <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
            <p>Este enlace expirará en 1 hora.</p>
        </body>
        </html>
    ";

    $resultadoEnvio = $mailer->enviar(
        $usuario['email'],
        $usuario['nombre'] . ' ' . $usuario['apellido'],
        $asunto,
        $mensaje
    );

    if (!$resultadoEnvio) {
        setMensaje('error', 'Error al enviar el correo: ');
    } else {
        setMensaje('exito', 'Se envió un correo con instrucciones para restablecer tu contraseña.');
    }

    redirigir('index.php?action=login');
}

/**
 * Muestra el formulario para restablecer la contraseña a partir de un token válido.
 * 
 * Valida que el token sea correcto y que no haya expirado.
 * Si el token es válido, muestra la vista correspondiente para ingresar la nueva contraseña.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 */
function mostrarFormularioRestablecerPass(UsuarioModel $usuarioModel)
{
    $token = $_GET['token'] ?? '';
    if ($token === '') {
        redirigir('index.php?action=login', 'Token inválido.', 'error');
    }

    $usuario = $usuarioModel->obtenerPorToken($token);

    if (!$usuario || $usuario['reset_expira'] < time()) {
        redirigir('index.php?action=login', 'Token inválido o expirado.', 'error');
    }

    include 'vistas/user_restablecer_pass.php';
}

/**
 * Procesa el restablecimiento de contraseña desde un token.
 *
 * Valida el token y las nuevas contraseñas ingresadas. Si todo es válido,
 * actualiza la contraseña del usuario e invalida el token.
 *
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 * @return void
 */
function procesarrestablecerPass(UsuarioModel $usuarioModel)
{
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigirAbsoluto('login');
    }

    // Validación CSRF
    validarCSRF();

    $token = $_POST['token'] ?? '';
    $password = $_POST['nueva_password'] ?? '';
    $confirm_password = $_POST['repite_password'] ?? '';

    $errores = [];

    // Validar token vacío
    $mensajeToken = validarCampoRequerido($token);
    if ($mensajeToken !== null) {
        $errores[] = $mensajeToken;
    } else {
        //  Validar que el token sea hexadecimal (64 chars)
        $mensajeFormatoToken = validarTokenHex($token);
        if ($mensajeFormatoToken !== null) {
            $errores[] = $mensajeFormatoToken;
        }
    }

    // Si ya hay errores en el token, redirigir directamente
    if (!empty($errores)) {
        redirigirAbsoluto('login', implode(' ', $errores));
    }

    // Obtener usuario por token para verificar validez
    $usuario = $usuarioModel->obtenerPorToken($token);
    if (!$usuario || $usuario['reset_expira'] < time()) {
        redirigirAbsoluto('olvido-pass', 'El enlace ha expirado o es inválido. Por favor, solicita un nuevo enlace.');
    }

    // Validar contraseñas
    // Validar que las contraseñas no estén vacías
    $mensajePassword = validarCampoRequerido($password);
    if ($mensajePassword !== null) {
        $errores[] = $mensajePassword;
    }

    $mensajeConfirmPassword = validarCampoRequerido($confirm_password);
    if ($mensajeConfirmPassword !== null) {
        $errores[] = $mensajeConfirmPassword;
    }

    // Validar que las contraseñas coincidan
    if ($mensajePassword === null && $mensajeConfirmPassword === null) {
        if ($password !== $confirm_password) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
    }

    // Validar fortaleza de la contraseña
    $mensajePasswordFuerte = validarPassword($password);
    if ($mensajePasswordFuerte !== null) {
        $errores[] = $mensajePasswordFuerte;
    }

    // Si hay errores, redirigir a la vista de restablecer
    if (!empty($errores)) {
        redirigir('index.php?action=restablecer-pass&token=' . urlencode($token), implode(' ', $errores), 'error');
    }

    // Verificar si la nueva contraseña es diferente a la anterior
    if (verificarCredenciales($password, $usuario['password'])) {
        redirigir('index.php?action=restablecer-pass&token=' . urlencode($token), 'La nueva contraseña no puede ser igual a la anterior.', 'error');
    }

    // Actualizar contraseña
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $resultado = $usuarioModel->actualizarPasswordPorToken($usuario['id_usuario'], $hashPassword);

    if ($resultado) {
        // El token se invalida automáticamente en actualizarPasswordPorToken
        redirigirAbsoluto('login', 'Tu contraseña ha sido actualizada correctamente. Por favor, inicia sesión.', 'exito');
    } else {
        redirigir('index.php?action=restablecer-pass&token=' . urlencode($token), 'Ocurrió un error al actualizar la contraseña. Por favor, inténtalo de nuevo.', 'error');
    }
}


// MÉTODOS AUXILIARES DEL CONTROLADOR

/**
 * Valida los campos básicos de un usuario: nombre, apellido y email.
 * 
 * Además, verifica si el email ingresado ya está en uso por otro usuario.
 * Los mensajes de error se agregan al array $errores pasado por referencia.
 *
 * @param string $nombre Nombre ingresado.
 * @param string $apellido Apellido ingresado.
 * @param string $email Email ingresado.
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuario.
 * @param array $errores Array de errores pasado por referencia.
 * @param bool $validarEmailDuplicado Indica si se debe verificar duplicación de email.
 * @param int|null $idUsuarioActual ID del usuario actual (en caso de edición de perfil).
 */
function validarDatosUsuario($nombre, $apellido, $email, UsuarioModel $usuarioModel, &$errores, $validarEmailDuplicado = true, $idUsuarioActual = null)
{
    // Validar nombre
    $mensajeNombre = validarTexto($nombre, 50, true);
    if ($mensajeNombre !== null) {
        $errores[] = $mensajeNombre;
    }

    // Validar apellido
    $mensajeApellido = validarTexto($apellido, 50, true);
    if ($mensajeApellido !== null) {
        $errores[] = $mensajeApellido;
    }

    // Validar email
    $mensajeEmail = validarEmail($email, true);
    if ($mensajeEmail !== null) {
        $errores[] = $mensajeEmail;
    }

    // Validar email duplicado solo si corresponde Y si el email es válido
    if ($validarEmailDuplicado && $mensajeEmail === null) {
        $emailExiste = $usuarioModel->existeEmail($email);

        if ($emailExiste) {
            if ($idUsuarioActual === null) {
                // Registro → cualquier email duplicado es error
                $errores[] = 'Ya existe un usuario registrado con ese email.';
            } else {
                // Editar perfil → permitir si es el mismo usuario
                $usuarioExistente = $usuarioModel->obtenerPorEmail($email);
                if ($usuarioExistente && $usuarioExistente['id_usuario'] != $idUsuarioActual) {
                    $errores[] = 'Ya existe un usuario registrado con ese email.';
                }
            }
        }
    }
}

/**
 * Verifica las credenciales comparando una contraseña en texto plano contra un hash de la base de datos.
 * Si no se provee hash (null), realiza una verificación simulada para proteger contra ataques de timing. (SI EL USUARIO NO EXISTE, también se verifica credenciales para que el tiempo de ejecución sea similar en ambos casos, y no se pueda descubrir qué emails ya existen)
 * 
 * @param string $passwordPlano Contraseña en texto plano ingresada por el usuario.
 * @param string|null $hashPasswordBD Hash de la contraseña almacenada en la base de datos, o null si el usuario no existe.
 * @return bool True si las credenciales son correctas, false en caso contrario.
 */
function verificarCredenciales(string $passwordPlano, ?string $hashPasswordBD): bool
{
    if ($hashPasswordBD !== null) {
        return password_verify($passwordPlano, $hashPasswordBD);
    } else {
        // Simulación para proteger contra timing attacks
        password_verify('password_falsa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
        return false;
    }
}

/**
 * Verifica si el usuario ha superado el límite de intentos de login permitidos.
 * Si el bloqueo sigue vigente, muestra mensaje y detiene el flujo actual.
 * Si ya expiró el tiempo de bloqueo, reinicia el contador.
 * 
 * @return void
 */
function verificarIntentosLogin(): void
{
    // Inicializar contador de intentos si no existe
    if (!isset($_SESSION['intentos_login'])) {
        $_SESSION['intentos_login'] = 0;
        $_SESSION['ultimo_intento'] = time();
    }

    // Bloquear después de 5 intentos fallidos por 30 minutos
    if ($_SESSION['intentos_login'] >= MAX_INTENTOS_LOGIN) {
        $tiempo_restante = ceil((TIEMPO_BLOQUEO_LOGIN - (time() - $_SESSION['ultimo_intento'])) / 60);

        if ($tiempo_restante > 0) {
            redirigir('index.php?action=login', "Demasiados intentos fallidos. Por favor, intente nuevamente en $tiempo_restante minutos.", 'error');
        } else {
            // Restablecer contador después del tiempo de bloqueo
            $_SESSION['intentos_login'] = 0;
        }
    }
}

/**
 * Incrementa el contador de intentos fallidos de login y actualiza la marca de tiempo.
 *
 * @return void
 */
function sumarIntentoFallido(): void
{
    $_SESSION['intentos_login']++;
    $_SESSION['ultimo_intento'] = time();
}
