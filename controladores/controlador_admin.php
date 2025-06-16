<?php

/**
 * Controlador para el panel de administración.
 * Maneja todas las acciones relacionadas con la administración del sistema.
 */

require_once 'modelos/UsuarioModel.php';
require_once 'modelos/OfertaModel.php';
require_once 'modelos/ConfiguracionSistemaModel.php';
require_once 'helpers/authHelper.php';
require_once 'helpers/PaginadorHelper.php';
require_once 'helpers/OrdenHelper.php';
require_once 'helpers/generalHelper.php';

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =============================================
// 1. CONTROLADOR PRINCIPAL
// =============================================

/**
 * Controlador principal del panel de administración.
 * 
 * @param string $accion Acción a ejecutar
 * @return void
 */
function controladorAdmin($accion)
{
    // Inicializar modelos
    $usuarioModel = new UsuarioModel();
    $configModel = new ConfiguracionSistemaModel();
    $ofertaModel = new OfertaModel();

    // Verificar permisos
    accesoSoloAdmin();

    switch ($accion) {

        // Panel general (con tabs)
        case 'admin-panel':
            $seccion = $_GET['seccion'] ?? 'usuarios';
            return mostrarPanelAdmin($seccion, $usuarioModel, $ofertaModel, $configModel);

            // Acciones de configuración
        case 'actualizar-configuracion':
            return actualizarConfiguracion($configModel);

            // Acciones de usuario
        case 'cambiar-rol':
            return cambiarRol($usuarioModel);

        case 'desactivar-usuario':
            return ejecutarAccionSimple(
                $usuarioModel,
                'id_usuario',
                'desactivar',
                null,
                "Usuario desactivado correctamente.",
                'admin-panel&seccion=usuarios'
            );

        case 'reactivar-usuario':
            return ejecutarAccionSimple(
                $usuarioModel,
                'id_usuario',
                'reactivar',
                null,
                'Usuario reactivado correctamente.',
                'admin-panel&seccion=usuarios'
            );

        case 'actualizar-estado-aprobacion-usuario':
            return ejecutarAccionSimple(
                $usuarioModel,
                'id_usuario',
                'actualizarEstadoAprobacion',
                $_POST['nuevo_estado_aprobacion'] ?? null,
                "Usuario actualizado correctamente.",
                'admin-panel&seccion=usuarios'
            );

        case 'accion-masiva-usuarios':
            $ids    = $_POST['seleccionados'] ?? [];
            $acc    = $_POST['accion']      ?? '';
            $mapUsr = [
                'aprobar'                => ['actualizarEstadoAprobacion', 'aprobado'],
                'rechazar'               => ['actualizarEstadoAprobacion', 'rechazado'],
                'desactivar'             => ['desactivar', null],
                'cambiar-rol-estudiante' => ['cambiarRol', 'estudiante'],
                'cambiar-rol-profesor'   => ['cambiarRol', 'profesor'],
                'cambiar-rol-administrativo' => ['cambiarRol', 'administrativo'],
            ];
            return procesaAccionMasiva(
                $usuarioModel,
                $ids,
                $acc,
                $mapUsr,
                'admin-panel&seccion=usuarios',
                'Acción masiva sobre usuarios aplicada.'
            );


            // Acciones de ofertas
        case 'actualizar-estado-aprobacion-oferta':
            return ejecutarAccionSimple(
                $ofertaModel,
                'id_oferta',
                'actualizarEstadoAprobacion',
                $_POST['nuevo_estado_aprobacion'] ?? null,
                "Oferta actualizada correctamente.",
                'admin-panel&seccion=ofertas'
            );

        case 'accion-masiva-ofertas':
            $ids    = $_POST['seleccionados'] ?? [];
            $acc    = $_POST['accion']      ?? '';
            $mapOf = [
                'aprobar'  => ['actualizarEstadoAprobacion', 'aprobado'],
                'rechazar' => ['actualizarEstadoAprobacion', 'rechazado'],
                'eliminar'  => ['eliminar', null],
                'finalizar' => ['desactivar', null],
                'reabrir'   => ['reactivar', null],

            ];
            return procesaAccionMasiva(
                $ofertaModel,
                $ids,
                $acc,
                $mapOf,
                'admin-panel&seccion=ofertas',
                'Acción masiva sobre ofertas aplicada.'
            );

        case 'eliminar-oferta-admin':
            return ejecutarAccionSimple(
                $ofertaModel,
                'id_oferta',
                'eliminar',
                null,
                'Oferta eliminada correctamente.',
                'admin-panel&seccion=ofertas'
            );

        case 'desactivar-oferta':
            return ejecutarAccionSimple(
                $ofertaModel,
                'id_oferta',
                'desactivar',
                null,
                'Oferta desactivada correctamente.',
                'admin-panel&seccion=ofertas'
            );

        case 'reactivar-oferta':
            return ejecutarAccionSimple(
                $ofertaModel,
                'id_oferta',
                'reactivar',
                null,
                'Oferta reactivada correctamente.',
                'admin-panel&seccion=ofertas'
            );

        default:
            setMensaje('error', 'Acción administrativa no reconocida.');
            redirigirConFiltros('admin-panel&seccion=usuarios');
    }
}


/**
 * Muestra el panel de administración según la sección solicitada.
 *
 * @param string $seccion Sección a mostrar (usuarios, ofertas, configuracion)
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuarios
 * @param OfertaModel $ofertaModel Instancia del modelo de ofertas
 * @param ConfiguracionSistemaModel $configModel Instancia del modelo de configuración
 * @return void
 */
function mostrarPanelAdmin($seccion, $usuarioModel, $ofertaModel, $configModel)
{
    accesoSoloAdmin();
    // Cargar vistas y lógica según la sección
    switch ($seccion) {
        case 'usuarios':
            return adminUsuarios($usuarioModel);

        case 'ofertas':
            return adminOfertas($ofertaModel);

        case 'configuracion':
            return adminConfiguracion($configModel);

        default:
            setMensaje('error', 'Sección no válida');
            redirigirConFiltros('admin-panel&seccion=usuarios');
    }
}


/**
 * Muestra el listado de usuarios con opciones de filtrado, paginación y ordenamiento.
 * 
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuarios
 * @return void
 */
function adminUsuarios($usuarioModel)
{
    // Construir filtros básicos
    $filtrosConfig = [
        'nombre' => ['validador' => 'texto', 'max' => 150, 'requerido' => false],
        'email' => ['validador' => 'texto', 'max' => 150, 'requerido' => false], // no necesariamente se busca el mail entero, puede ser solo parte
        'rol' => ['validador' => 'rol'],
        'estado' => ['validador' => 'entero', 'min' => 0, 'max' => 1],
        'estado_aprobacion' => ['validador' => 'estadoAprobacion']
    ];

    $errores_filtros = [];
    $filtros = procesarFiltrosValidados($filtrosConfig, $errores_filtros);

    if (!empty($errores_filtros)) {
        setMensaje('error', 'Se detectaron errores en los filtros aplicados.');
    }

    $filtros['excluir_id'] = idUsuario();

    // Paginación
    $paginacion = PaginadorHelper::configurarPaginacion($_GET, 20, 5, 100);

    // Agregar a los filtros para la consulta
    $filtros['limite'] = $paginacion['limite'];
    $filtros['offset'] = $paginacion['offset'];

    // Ordenamiento dinámico
    $columnasPermitidas = [
        // clave URL => expresión SQL (columna real en la BD)
        'nombre'            => 'apellido',            // Ordenar por apellido (ver si luego incluir nombre)
        'email'             => 'email',
        'rol'               => 'rol',
        'activo'            => 'activo',
        'estado_aprobacion' => 'estado_aprobacion',
        'fecha_creacion'    => 'fecha_creacion',
        'fecha_modificacion' => 'fecha_modificacion',
    ];

    $ordenamiento = PaginadorHelper::configurarOrdenamiento($_GET, $columnasPermitidas, 'DESC');

    $filtros = array_merge($filtros, $ordenamiento); // Agregar a los filtros

    // Extraer valores para la vista y generarUrlsYFlechas
    $sort = $_GET['sort'] ?? '';
    $order = strtoupper($_GET['order'] ?? 'DESC');  // Orden actual (ASC/DESC)
    $pagina_actual = $paginacion['pagina_actual'];
    $limite = $paginacion['limite'];

    // Consultar modelo
    $total_usuarios = $usuarioModel->contarUsuarios($filtros);
    $total_paginas = PaginadorHelper::totalPaginas($total_usuarios, $limite);
    $usuarios = $usuarioModel->obtenerTodos($filtros);

    // Generar URLs y flechas de orden
    $orden = generarUrlsYFlechas($_GET, $sort, $order, $columnasPermitidas);

    // Incluir vista
    include 'vistas/admin_usuarios.php';
}

/**
 * Muestra el listado de ofertas con opciones de filtrado, paginación y ordenamiento.
 * 
 * @param OfertaModel $ofertaModel Instancia del modelo de ofertas
 * @return void
 */
function adminOfertas($ofertaModel)
{
    // Actualiza el estado de ofertas vencidas  (con fecha de finalización anterior a la actual) antes de cargarlas
    $ofertaModel->finalizarOfertasVencidas();

    // Construir array de filtros básicos
    $filtrosConfig = [
        'busqueda' => [
            'validador' => 'texto',
            'max' => 150,
            'requerido' => false
        ],
        'usuario' => [
            'validador' => 'texto',
            'max' => 150,
            'requerido' => false
        ],
        'estado_aprobacion' => [
            'validador' => 'estadoAprobacion'
        ],
        'estado' => [  // este es el campo GET estado=0 o estado=1
            'validador' => 'entero',
            'min' => 0,
            'max' => 1,
            'requerido' => false
        ]
    ];
    $errores_filtros = [];
    $filtros = procesarFiltrosValidados($filtrosConfig, $errores_filtros);

    if (!empty($errores_filtros)) {
        setMensaje('error', 'Se detectaron errores en los filtros aplicados.');
    }

    // Manejo de casos especiales de filtros
    $filtros['ver_inactivas'] = true;  // Siempre permitir ver las “inactivas” (para no ocultar las finalizadas), pero si vienen en $_GET['estado'] se define exactamente activo=0 o activo=1:

    if (isset($_GET['estado']) && ($_GET['estado'] === '0' || $_GET['estado'] === '1')) {
        $filtros['activa'] = (int) $_GET['estado'];
    }

    // Paginación
    $paginacion = PaginadorHelper::configurarPaginacion($_GET, 20, 5, 100);

    // Agregar a los filtros para la consulta
    $filtros['limite'] = $paginacion['limite'];
    $filtros['offset'] = $paginacion['offset'];

    // Ordenamiento dinámico
    // Validar parámetros de ordenamiento 
    $columnasPermitidas = [
        'puesto'            => 'o.puesto',
        'empresa'           => 'o.empresa',
        'publicada_por'     => 'u.apellido, u.nombre',
        'activa'            => 'o.activa',
        'estado_aprobacion' => 'o.estado_aprobacion',
        'fecha_creacion'    => 'o.fecha_creacion',
        'fecha_modificacion' => 'o.fecha_modificacion',
        'ultima_modificacion' => 'IFNULL(o.fecha_modificacion, o.fecha_creacion)',
    ];

    $ordenamiento = PaginadorHelper::configurarOrdenamiento($_GET, $columnasPermitidas, 'DESC');

    $filtros = array_merge($filtros, $ordenamiento); // Agregar a los filtros

    // Extraer valores para la vista y generarUrlsYFlechas
    $sort = $_GET['sort'] ?? '';  // Clave (ej: 'puesto')
    $order = strtoupper($_GET['order'] ?? 'DESC');  // Orden actual (ASC/DESC)
    $pagina_actual = $paginacion['pagina_actual'];
    $limite = $paginacion['limite'];

    // Consultar el modelo
    $total_ofertas = $ofertaModel->contarOfertas($filtros);
    $total_paginas = PaginadorHelper::totalPaginas($total_ofertas, $limite);
    $ofertas = $ofertaModel->obtenerOfertas($filtros);

    // Generar enlaces y flechas de orden
    $orden = generarUrlsYFlechas($_GET, $sort, $order, $columnasPermitidas);     // Pasar todo $_GET para preservar cualquier filtro adicional


    // Incluir la vista, pasando variables limpias
    include 'vistas/admin_ofertas.php';
}

/**
 * Muestra el formulario de configuración del sistema.
 * 
 * @param ConfiguracionSistemaModel $configModel Instancia del modelo de configuración
 * @return void
 */
function adminConfiguracion($configModel)
{
    $configuraciones = $configModel->obtenerTodas();
    include 'vistas/admin_configuracion.php';
}


/**
 * Procesa el formulario de actualización de configuración.
 * 
 * @param ConfiguracionSistemaModel $configModel Instancia del modelo de configuración
 * @return void
 */
function actualizarConfiguracion($configModel)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clavesEsperadas = ['aprobar_ofertas', 'aprobar_registros'];

        foreach ($clavesEsperadas as $clave) {
            // Asume 'true' si está presente, 'false' si no
            $valor = isset($_POST[$clave]) ? 'true' : 'false';
            $configModel->actualizarValor($clave, $valor);
        }

        // Usar el helper para el mensaje y redirección
        redirigir('index.php?action=admin-panel&seccion=configuracion', 'Configuración actualizada correctamente.', 'exito');
    }

    // Si no es POST, redirigir al panel de administración
    redirigir('index.php?action=admin-panel');
}


/**
 * Ejecuta una acción masiva sobre múltiples registros (usuarios u ofertas).
 *
 * @param object $model Modelo a utilizar (UsuarioModel u OfertaModel).
 * @param array $ids IDs de los registros seleccionados.
 * @param string $accion Acción solicitada (clave del mapa).
 * @param array $mapaAcciones Mapa con acciones posibles y sus métodos/valores.
 * @param string $redirBase URL base para redirección.
 * @param string $mensajeOK Mensaje de éxito si al menos una acción se ejecuta.
 * @return void
 */
function procesaAccionMasiva(
    $model,
    array $ids,
    string $accion,
    array $mapaAcciones,    // ej. ['aprobar'=> ['actualizarEstadoAprobacion','aprobado'], ... ]
    string $redirBase,
    string $mensajeOK       // texto genérico final si al menos 1 se procesó
) {
    // Validación CSRF
    validarCSRF();
    if (empty($ids) || !$accion || !isset($mapaAcciones[$accion])) {
        setMensaje('error', 'Debe seleccionar elementos y una acción válida.');
        redirigirConFiltros($redirBase);
    }

    $info       = $mapaAcciones[$accion];
    $metodo     = $info[0];
    $paramExtra = $info[1] ?? null;
    $alMenosUno = false;

    foreach ($ids as $id) {
        $errorId = validarId($id);
        if ($errorId !== null) continue;

        $registro = $model->obtenerPorId($id);
        if (!$registro) continue;

        if ($paramExtra !== null) {
            $model->$metodo($id, $paramExtra);
        } else {
            $model->$metodo($id);
        }
        $alMenosUno = true;
    }

    if ($alMenosUno) {
        setMensaje('exito', $mensajeOK);
    } else {
        setMensaje('error', 'No se pudo procesar ningún registro.');
    }

    redirigirConFiltros($redirBase);
}

/**
 * Ejecuta una acción individual sobre un registro (usuario u oferta).
 *
 * @param object $model Modelo a utilizar.
 * @param string $campoId Clave POST del ID (por ejemplo 'id_usuario').
 * @param string $metodoModelo Método a invocar en el modelo.
 * @param string|null $nuevoEstado Parámetro adicional si aplica (por ejemplo, estado de aprobación).
 * @param string $exitoMensaje Mensaje a mostrar si la acción tiene éxito.
 * @param string $redirBase URL base para redirección.
 * @return void
 */
function ejecutarAccionSimple(
    $model,
    string $campoId,
    string $metodoModelo,
    ?string $nuevoEstado,
    string $exitoMensaje,
    string $redirBase
) {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigirConFiltros($redirBase);
    }

    // Validación CSRF
    validarCSRF();

    $id = $_POST[$campoId] ?? null;
    $errorId = validarId($id);
    if ($errorId !== null) {
        setMensaje('error', $errorId);
        redirigirConFiltros($redirBase);
    }

    $registro = $model->obtenerPorId($id);
    if (!$registro) {
        $tipo = $campoId === 'id_usuario' ? 'Usuario' : 'Oferta';
        setMensaje('error', "$tipo no encontrado.");
        redirigirConFiltros($redirBase);
    }

    if ($campoId === 'id_usuario' && $metodoModelo === 'desactivar' && $id == idUsuario()) {
        setMensaje('error', 'No podés desactivarte a vos mismo.');
        redirigirConFiltros($redirBase);
    }

    // Si la acción es “actualizarEstadoAprobacion”, pasar el estado:
    if ($metodoModelo === 'actualizarEstadoAprobacion') {
        if (!$nuevoEstado || !in_array($nuevoEstado, ['aprobado', 'rechazado'])) {
            setMensaje('error', 'Estado inválido.');
            redirigirConFiltros($redirBase);
        }
        $exito = $model->$metodoModelo($id, $nuevoEstado);
    } else {
        // Métodos simples: desactivar, reactivar, eliminar (sin segundo parámetro).
        $exito = $model->$metodoModelo($id);
    }

    if ($exito) {
        setMensaje('exito', $exitoMensaje);
    } else {
        setMensaje('error', 'No se pudo completar la acción.');
    }

    redirigirConFiltros($redirBase);
}


/**
 * Cambia el rol de un usuario.
 * 
 * @param UsuarioModel $usuarioModel Instancia del modelo de usuarios
 * @return void
 */
function cambiarRol($usuarioModel)
{
    //accesoSoloAdmin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validación CSRF
        validarCSRF();
        $id = $_POST['id_usuario'] ?? null;
        $nuevoRol = $_POST['nuevo_rol'] ?? '';

        // Validación de entrada
        $errorId = validarId($id);
        if ($errorId) {
            setMensaje('error', $errorId);
            redirigirConFiltros('admin-panel&seccion=usuarios');
        }

        $errorRol = validarRol($nuevoRol);
        if ($errorRol) {
            setMensaje('error', $errorRol);
            redirigirConFiltros('admin-panel&seccion=usuarios');
        }

        $exito = $usuarioModel->cambiarRol($id, $nuevoRol);
        setMensaje(
            $exito ? 'exito' : 'error',
            $exito ? "Rol actualizado correctamente a \"$nuevoRol\"."
                : 'No se pudo actualizar el rol. Verificá el ID.'
        );

        redirigirConFiltros('admin-panel&seccion=usuarios');
    }
    redirigirConFiltros();
}


// Métodos auxiliares



/**
 * Redirige a una URL incluyendo los filtros activos desde POST, GET o SESSION.
 *
 * @param string $baseAction Acción base (por ejemplo 'admin-panel&seccion=usuarios').
 * @return void
 */
function redirigirConFiltros($baseAction = 'admin-panel&seccion=usuarios')
{
    $filtros_permitidos = [];

    if ($baseAction === 'admin-panel&seccion=usuarios') {
        $filtros_permitidos = ['nombre', 'email', 'rol', 'estado', 'estado_aprobacion', 'limite', 'pagina'];
        $session_key = 'filtros_usuarios';
    } elseif (strpos($baseAction, 'admin-panel') !== false) {
        // Manejar tanto 'admin-panel&seccion=ofertas' como 'admin-panel' con parámetros
        $filtros_permitidos = ['busqueda', 'estado_aprobacion', 'limite', 'pagina', 'seccion', 'estado'];
        $session_key = 'filtros_ofertas';
    } else {
        // Si no está contemplado, redirige sin filtros
        header("Location: index.php?action=admin-panel&seccion=usuarios");
        exit;
    }

    $filtros = [];
    foreach ($filtros_permitidos as $clave) {
        if (isset($_POST[$clave])) {
            $filtros[$clave] = $_POST[$clave];
        } elseif (isset($_GET[$clave])) {
            $filtros[$clave] = $_GET[$clave];
        } elseif (isset($_SESSION[$session_key][$clave])) {
            $filtros[$clave] = $_SESSION[$session_key][$clave];
        }
    }

    $query = http_build_query($filtros);
    header("Location: index.php?action=$baseAction" . ($query ? "&$query" : ''));
    exit;
}
