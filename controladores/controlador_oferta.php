<?php
/**
 * Controlador para las ofertas publicadas en la bolsa de trabajo.
 *
 * Gestiona el flujo principal relacionado con las ofertas:
 * - Visualización de listados y detalle.
 * - Alta y edición de ofertas por parte de usuarios autenticados.
 * - Eliminación lógica de ofertas.
 *
 * Utiliza validaciones, control de acceso y helpers para sanitizar datos,
 * validar formularios y proteger contra CSRF.
 */

require_once 'modelos/OfertaModel.php';
require_once 'helpers/authHelper.php';
require_once 'helpers/PaginadorHelper.php';
require_once 'helpers/SanitizadorHelper.php';
require_once 'helpers/ValidadorHelper.php';
require_once 'helpers/generalHelper.php';


/**
 * Controlador principal de ofertas.
 *
 * @param string $accion Acción solicitada (listado, ver, nueva, editar, eliminar).
 * @return void
 */
function controladorOferta($accion)
{
    switch ($accion) {
        case 'listado':
            return mostrarListado();
        case 'ver-oferta':
            return mostrarDetalle();
        case 'nueva-oferta':
        case 'editar-oferta':
            return procesarFormulario();
        case 'eliminar-oferta':
            return eliminarOferta();
    }
}

/**
 * Muestra el listado de ofertas con filtros y paginación.
 * 
 * Actualiza el estado de ofertas vencidas antes de obtener los datos.
 * 
 * @return void
 */
function mostrarListado()
{
    $ofertaModel = new OfertaModel();

    // Actualiza el estado de ofertas vencidas  (con fecha de finalización anterior a la actual) antes de cargarlas
    $ofertaModel->finalizarOfertasVencidas();

    // Definir filtros validados
    $filtrosConfig = [
        'busqueda' => [
            'validador' => 'texto',
            'max' => 150,
            'requerido' => false
        ],
        'modalidad' => [
            'validador' => 'opciones',
            'opciones' => ['presencial', 'remoto', 'híbrido'],
            'requerido' => false
        ],
        'experiencia' => [
            'validador' => 'opciones',
            'opciones' => ['0', '1', '2', '3-5', '+5'],
            'requerido' => false
        ],
        'jornada' => [
            'validador' => 'opciones',
            'opciones' => ['completa', 'parcial'],
            'requerido' => false
        ],
        'estado_aprobacion' => [
            'validador' => 'estadoAprobacion',
            'forzarArray' => true
        ]
    ];

    $errores_filtros = [];
    $filtros = procesarFiltrosValidados($filtrosConfig, $errores_filtros);

    // Ajuste: pasar estado_aprobacion como array, si existe
    if (isset($filtros['estado_aprobacion']) && !is_array($filtros['estado_aprobacion'])) {
        $filtros['estado_aprobacion'] = [strtolower($filtros['estado_aprobacion'])];
    }

    // Filtros especiales
    if (!empty($_GET['ver_inactivas'])) {
        $filtros['ver_inactivas'] = true;
    }

    if (!empty($_GET['solo_propias']) && estaLogueado()) {
        $filtros['solo_propias'] = true;
    }

    // Mostrar solo ofertas aprobadas si:
    // - no se está viendo "solo_propias"
    // - no se está usando el filtro estado_aprobacion explícitamente
    // - el usuario no es admin
    if (
        !esAdmin() &&
        (empty($_GET['estado_aprobacion']) || $_GET['estado_aprobacion'] === '') &&
        (empty($filtros['estado_aprobacion']) || !is_array($filtros['estado_aprobacion']) || count(array_filter($filtros['estado_aprobacion'])) === 0) &&
        empty($filtros['solo_propias'])
    ) {
        $filtros['estado_aprobacion'] = ['aprobado'];
    }

    // Mostrar errores si hay
    if (!empty($errores_filtros)) {
        setMensaje('error', 'Se detectaron errores en los filtros aplicados.');
    }

    // Paginacion
    $por_pagina = 16;
    $paginacion = PaginadorHelper::configurarPaginacion($_GET, $por_pagina, 5, 100);
    $filtros['limite'] = $paginacion['limite'];
    $filtros['offset'] = $paginacion['offset'];
    $pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

    // Forzar ordenamiento por fecha de creación descendente
    $filtros['sort'] = 'o.fecha_creacion';
    $filtros['order'] = 'DESC';

    // consultas
    $total_ofertas = $ofertaModel->contarOfertas($filtros);
    $total_paginas = PaginadorHelper::totalPaginas($total_ofertas, $paginacion['limite']);
    $ofertas = $ofertaModel->obtenerOfertas($filtros);

    // Post procesamiento
    $permite_crear = puedeCrearOferta();
    foreach ($ofertas as &$oferta) {
        $oferta['puede_editar'] = puedeEditarOferta($oferta);
        $oferta['tiempo_publicacion'] = tiempoDesdePublicacion($oferta['fecha_creacion']);
    }
    unset($oferta);

    $filtros_contables = ['busqueda', 'modalidad', 'jornada', 'experiencia', 'estado_aprobacion', 'solo_propias', 'ver_inactivas'];
    $filtros_activos = 0;
    foreach ($filtros_contables as $filtro) {
        if (isset($_GET[$filtro]) && $_GET[$filtro] !== '') {
            if (in_array($filtro, ['solo_propias', 'ver_inactivas'])) {
                if (!empty($_GET[$filtro])) {
                    $filtros_activos++;
                }
            } else {
                $filtros_activos++;
            }
        }
    }
    include 'vistas/listado.php';
}

/**
 * Muestra el detalle de una oferta específica.
 *
 * Requiere un ID de oferta válido por GET. Si no se encuentra, redirige con mensaje de error.
 *
 * @return void
 */
function mostrarDetalle()
{
    $oferta = obtenerOfertaValida();

    // Solo admins y autores pueden ver ofertas no aprobadas
    if (
        $oferta['estado_aprobacion'] !== 'aprobado' &&
        !esAdmin() &&
        $oferta['publicada_por'] != idUsuario()
    ) {
        redirigir('index.php?action=listado', 'No tenés permiso para ver esta oferta.', 'error');
    }

    $oferta['tiempo_publicacion'] = tiempoDesdePublicacion($oferta['fecha_creacion']);
    $oferta['fecha_creacion'] = date('d-m-Y', strtotime($oferta['fecha_creacion']));
    if (!empty($oferta['fecha_modificacion'])) {
        $oferta['fecha_modificacion'] = date('d-m-Y', strtotime($oferta['fecha_modificacion']));
    }

    if (!empty($oferta['fecha_fin'])) {
        $oferta['fecha_fin'] = date('d-m-Y', strtotime($oferta['fecha_fin']));
    }

    $hayContacto = !empty($oferta['enlace']) || !empty($oferta['email_contacto']) || !empty($oferta['telefono_contacto']);
    $oferta['puede_editar'] = puedeEditarOferta($oferta);

    include 'vistas/detalle.php';
}

/**
 * Procesa tanto la creación como la edición de una oferta.
 *
 * Determina el modo ('editar' o 'crear') según el parámetro de entrada.
 * Valida los datos, guarda en la base, y redirige con mensajes apropiados.
 *
 * @return void
 */
function procesarFormulario()
{
    $ofertaModel = new OfertaModel();

    // Verificar permiso para publicar
    if (!puedeCrearOferta()) {
        redirigir('index.php?action=listado', 'No tenés permiso para acceder a esta página.', 'error');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validación CSRF
        validarCSRF();

        // Recolectar y validar datos del formulario
        $datos = [
            'puesto' => $_POST['puesto'] ?? '',
            'descripcion' => limpiarHTMLSeguro($_POST['descripcion']) ?? '',
            'empresa' => $_POST['empresa'] ?? '',
            'ubicacion' => $_POST['ubicacion'] ?? '',
            'modalidad' => $_POST['modalidad'] ?? 'presencial',
            'jornada' => $_POST['jornada'] ?? null,
            'horario' => $_POST['horario'] ?? '',
            'experiencia_requerida' => (isset($_POST['experiencia_requerida']) && $_POST['experiencia_requerida'] !== '')
                ? (int)$_POST['experiencia_requerida']
                : null,
            'enlace' => $_POST['enlace'] ?? '',
            'email_contacto' => $_POST['email_contacto'] ?? '',
            'telefono_contacto' => $_POST['telefono_contacto'] ?? '',
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'activa' => isset($_POST['activa']),
            'publicada_por' => idUsuario() ?? null
        ];

        // Validaciones según los campos requeridos en la base de datos
        $errores['puesto'] = validarTexto($datos['puesto'], 150, true, true);

        if (esContenidoVacioHTML($datos['descripcion'])) {
            $errores['descripcion'] = 'La descripción no puede estar vacía.';
        } else {
            $errores['descripcion'] = validarTexto($datos['descripcion'], 10000, true);
        }

        // Campos opcionales
        $errores['empresa'] = validarTexto($datos['empresa'], 100, false, true);
        $errores['ubicacion'] = validarTexto($datos['ubicacion'], 150, false, true);
        $errores['modalidad'] = validarEnOpciones($datos['modalidad'], ['presencial', 'remoto', 'híbrido']);
        $errores['jornada'] = validarEnOpciones($datos['jornada'], ['completa', 'parcial']);
        $errores['horario'] = validarTexto($datos['horario'], 100, false, true);
        $errores['experiencia_requerida'] = validarEntero($datos['experiencia_requerida'], false, 0);
        $errores['enlace'] = validarURL($datos['enlace']);
        $errores['email_contacto'] = validarEmail($datos['email_contacto']);
        $errores['telefono_contacto'] = validarTelefono($datos['telefono_contacto']);
        $errores['fecha_fin'] = validarFecha($datos['fecha_fin']);

        // Si la fecha de finalización ya pasó, forzar activa a false
        if (!empty($datos['fecha_fin']) && strtotime($datos['fecha_fin']) < strtotime(date('Y-m-d'))) {
            $datos['activa'] = 0;
        }

        // Limpieza de errores nulos
        $errores = array_filter($errores);

        // Si hay errores, volver al formulario
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $datos;
            $modo = $_POST['modo'] ?? 'crear';
            $oferta = $datos;
            include 'vistas/formulario.php';
            return;
        }

        $configModel = new ConfiguracionSistemaModel();
        $requiere_aprobacion = $configModel->obtenerValor('aprobar_ofertas');

        $datos['estado_aprobacion'] = calcularEstadoAprobacion($requiere_aprobacion);

        $modo = $_POST['modo'] === 'editar' ? 'editar' : 'crear';

        if ($modo === 'editar' && !empty($_POST['id_oferta'])) {
            $id = $_POST['id_oferta'];
            // Solo el admin o el autor puede editar
            $oferta = $ofertaModel->obtenerPorId($id);
            if (!$oferta || !puedeEditarOferta($oferta)) {
                redirigir('index.php?action=listado', 'No tenés permiso para editar esta oferta.', 'error');
            }

            // Si la oferta está pendiente, al editarla debe seguir pendiente
            if ($oferta['estado_aprobacion'] === 'pendiente') {
                $datos['estado_aprobacion'] = 'pendiente';
            } elseif (!esAdmin()) {
                // Si no es admin, cualquier cambio vuelve la oferta a pendiente si el sistema lo requiere
                $datos['estado_aprobacion'] = calcularEstadoAprobacion($requiere_aprobacion);
            } else {
                // Si es admin, no tocamos el estado de aprobación al editar
                unset($datos['estado_aprobacion']);
            }

            $exito = $ofertaModel->actualizar($id, $datos);
        } else {
            $id = $ofertaModel->crear($datos);
            $exito = $id !== false;
        }

        if ($exito) {
            if (!empty($datos['estado_aprobacion']) && $datos['estado_aprobacion'] === 'pendiente') {
                $mensaje = ($modo === 'editar')
                    ? 'La oferta fue editada y ahora está pendiente de aprobación por un administrador.'
                    : 'La oferta fue creada correctamente y está pendiente de aprobación por un administrador.';
            } else {
                $mensaje = ($modo === 'editar')
                    ? 'Oferta actualizada correctamente.'
                    : 'Oferta creada correctamente.';
            }

            redirigir('index.php?action=ver-oferta&id_oferta=' . $id, $mensaje, 'exito');
        } else {
            setMensaje('error', 'Hubo un problema al guardar la oferta.');
            $oferta = $datos; // para repoblar el form
            include 'vistas/formulario.php';
        }
    } else {
        // GET: mostrar formulario vacío o con datos
        $oferta = null;
        $modo = 'crear';
        // Si hay id, es edición; si no, alta
        if (!empty($_GET['id_oferta']) && is_numeric($_GET['id_oferta'])) {
            $modo = 'editar';
            $oferta = obtenerOfertaValida();

            if (!puedeEditarOferta($oferta)) {
                redirigir('index.php?action=listado', 'No tenés permiso para editar esta oferta.', 'error');
            }
        }

        include 'vistas/formulario.php';
    }
}

/**
 * Elimina lógicamente una oferta (establece fecha de eliminación y activa = 0).
 *
 * Requiere un ID válido por POST y protección CSRF.
 *
 * @return void
 */
function eliminarOferta()
{
    $ofertaModel = new OfertaModel();

    // Validación CSRF
    validarCSRF();

    $oferta = obtenerOfertaValida();
    if (!$oferta || !puedeEditarOferta($oferta)) {
        redirigir('index.php?action=listado', 'No tenés permiso para ver eliminar ofertas.', 'error');
    }
    $id = $_GET['id_oferta'];

    $exito = $ofertaModel->eliminar($id);

    if ($exito) {
        redirigir('index.php?action=listado', 'La oferta ha sido eliminada correctamente.', 'exito');
    } else {
        redirigir('index.php?action=listado', 'No se pudo eliminar la oferta.', 'error');
    }
    exit;
}

/**
 * Devuelve una representación legible del tiempo transcurrido desde una fecha dada.
 *
 * @param string $fechaPublicacion Fecha en formato válido (YYYY-MM-DD HH:MM:SS).
 * @return string Descripción del tiempo transcurrido.
 */
function tiempoDesdePublicacion(string $fecha_creacion): string
{
    $fecha = new DateTime($fecha_creacion);
    $ahora = new DateTime();
    $diferencia = $fecha->diff($ahora);

    if ($diferencia->days === 0) {
        $horas = $diferencia->h;
        if ($horas === 0) {
            return 'menos de 1 hora';
        }
        return "$horas " . ($horas === 1 ? 'hora' : 'horas');
    } else {
        $dias = $diferencia->days;
        return "$dias " . ($dias === 1 ? 'día' : 'días');
    }
}


/**
 * Valida la existencia de un ID de oferta en $_GET, verifica que sea numérico
 * y que exista en la base de datos. Si no es válido o no se encuentra, finaliza la ejecución.
 *
 * @param string $id_param Nombre del parámetro GET que contiene el ID de la oferta (por defecto 'id_oferta').
 * @return array Oferta obtenida desde la base de datos.
 */
function obtenerOfertaValida($id_param = 'id_oferta')
{
    $id = $_GET[$id_param] ?? null;

    $mensajeId = validarId($id);
    if ($mensajeId !== null) {
        redirigir('index.php?action=listado', $mensajeId, 'error');
    }
    $ofertaModel = new OfertaModel();
    $oferta = $ofertaModel->obtenerPorId($id);

    if (!$oferta) {
        redirigir('index.php?action=listado', 'Oferta no encontrada.', 'error');
    }

    return $oferta;
}

/**
 * Devuelve el estado de aprobación inicial de una oferta según la configuración del sistema
 * y el rol del usuario que está publicando.
 *
 * - Si el usuario es admin, siempre se publica como 'aprobado'.
 * - Si no es admin, se publica como 'pendiente' o 'aprobado' según la configuración.
 *
 * @param string $requiere_aprobacion Valor de configuración del sistema ('true' o 'false').
 * @return string 'pendiente' o 'aprobado'.
 */
function calcularEstadoAprobacion($requiere_aprobacion)
{
    if (esAdmin()) {
        return 'aprobado';
    }

    return $requiere_aprobacion === 'true' ? 'pendiente' : 'aprobado';
}
