<?php
/**
 * ValidadorHelper
 *
 * Este archivo contiene funciones auxiliares para validar datos ingresados por el usuario
 * antes de procesarlos o guardarlos en la base de datos. Incluye validaciones comunes como:
 * - Verificación de formato de email
 * - Validación de URLs
 * - Validación de campos requeridos
 * - Validación de longitud mínima o máxima
 * - Validación de caracteres permitidos en campos de texto
 * - Validación de ID numérico
 * - Validación de fechas (YYYY-MM-DD)
 * - Validación de números enteros con rango opcional
 * - Validación contra listas de opciones permitidas
 * - Validación de contraseñas con requisitos mínimos
 * - Validación de roles de usuario
 * - Validación de estados de aprobación
 * - Detección de contenido vacío en HTML
 *
 * Estas funciones pueden ser reutilizadas por distintos controladores (ofertas, usuarios, etc.)
 * para mejorar la seguridad y la consistencia de las validaciones.
 */


/**
 * Valida un campo de texto.
 *
 * @param string $valor Texto a validar.
 * @param int $longitud_max Longitud máxima permitida.
 * @param bool $requerido Si es true, el campo no puede estar vacío.
 * @param bool $validarCaracteres Si es true, solo permite caracteres alfanuméricos, puntuación básica y tildes.
 * @return ?string null si el texto es válido, mensaje de error en caso contrario.
 */
function validarTexto(string $valor, int $longitud_max = 255, bool $requerido = false, bool $validarCaracteres = false): ?string
{
    $valor = trim($valor);
    if ($requerido && $valor === '') return 'Campo requerido.';
    if (strlen($valor) > $longitud_max) return "Máximo {$longitud_max} caracteres.";
    
    if ($validarCaracteres && !preg_match('/^[a-zA-Z0-9\s.,;:()!?@\/\\\\áéíóúÁÉÍÓÚñÑ\'\"&+ -]*$/u', $valor)) {
        return 'El texto contiene caracteres no permitidos.';
    }
    
    return null;
}

/**
 * Valida un número de teléfono argentino.
 *
 * Acepta formatos como: +54 11 1234-5678, (011) 1234-5678, 11-1234-5678, 1123456789
 *
 * @param string $valor Número de teléfono a validar.
 * @param bool $requerido Si es true, el campo no puede estar vacío.
 * @return ?string null si el teléfono es válido, mensaje de error en caso contrario.
 */
function validarTelefono(string $valor, bool $requerido = false): ?string
{
    $valor = trim($valor);

    if($requerido && $valor === '') {
        return 'Campo requerido.';
    }

    if($valor === '') {
        return null;
    }

    // Mismo patrón que el frontend actualizado
    if(!preg_match('/^(\+54\s?)?(\(?\d{2,4}\)?[\s\-]?)?[\d\s\-]{6,12}$/', $valor)) {
        return 'Formato de teléfono inválido. Ejemplos válidos: +54 11 1234-5678, 11-1234-5678, 1123456789';
    }

    // Validación adicional de longitud - solo dígitos
    $soloNumeros = preg_replace('/[^\d]/', '', $valor);
    if(strlen($soloNumeros) < 8 || strlen($soloNumeros) > 15) {
        return 'El teléfono debe tener entre 8 y 15 dígitos.';
    }

    return null;
}

/**
 * Valida un ID numérico
 * 
 * @param mixed $id ID a validar
 * @return ?string null si es un ID válido, mensaje de error en caso contrario
 */
function validarId($id): ?string {
    if (filter_var($id, FILTER_VALIDATE_INT) === false || $id <= 0) { // En este caso, es redundante usar REGEX. se cubre con FILTER_VALIDATE_INT

    //if (!is_numeric($id) || $id <= 0 || !preg_match('/^\d+$/', (string)$id)) {
        return 'ID inválido.';
    }
    return null;
}

/**
 * Verifica si el contenido HTML ingresado está vacío de texto visible (por ejemplo, si solo tiene <br> o <p></p>).
 *
 * @param string $html Contenido HTML a evaluar.
 * @return bool true si no tiene texto visible, false en caso contrario.
 */
function esContenidoVacioHTML(string $html): bool {
    $texto = trim(strip_tags($html));
    return $texto === '';
}


/**
 * Valida un campo de email
 * 
 * @param string $valor Email a validar
 * @param bool $requerido Si es true, el campo no puede estar vacío
 * @return string|null Mensaje de error o null si la validación es exitosa
 */
function validarEmail(string $valor, bool $requerido = false): ?string
{
    $valor = trim($valor);
    
    // Validar campo requerido
    if ($requerido && $valor === '') {
        return 'Campo requerido.';
    }
    
    // Si el campo no es requerido y está vacío, es válido
    if ($valor === '') {
        return null;
    }
    
    // Validar formato de email
    if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
        return 'El formato del email no es válido.';
    }
    
    return null;
}

/**
 * Valida una URL.
 *
 * @param string $valor URL a validar.
 * @param bool $requerido Si es true, el campo no puede estar vacío.
 * @return ?string null si la URL es válida, mensaje de error en caso contrario.
 */
function validarURL(string $valor, bool $requerido = false): ?string
{
    $valor = trim($valor);
    if ($requerido && $valor === '') return 'Campo requerido.';
    if ($valor !== '' && !filter_var($valor, FILTER_VALIDATE_URL)) {
        return 'URL inválida.';
    }
    return null;
}

/**
 * Valida un campo numérico entero
 * 
 * @param mixed $valor Valor a validar
 * @param bool $requerido Si es true, el campo no puede estar vacío
 * @param int|null $min Valor mínimo permitido (inclusive)
 * @param int|null $max Valor máximo permitido (inclusive)
 * @return string|null Mensaje de error o null si la validación es exitosa
 */
function validarEntero($valor, bool $requerido = false, ?int $min = null, ?int $max = null): ?string
{
    // Validar campo requerido
    if ($valor === '' || $valor === null) {
        return $requerido ? 'Campo requerido.' : null;
    }
    
    // Validar que sea un entero
    if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
        return 'Debe ser un número entero válido.';
    }
    
    $valor = (int)$valor; // Asegurarse de que sea un entero
    
    // Validar valor mínimo
    if ($min !== null && $valor < $min) {
        return "El valor debe ser mayor o igual a {$min}.";
    }
    
    // Validar valor máximo
    if ($max !== null && $valor > $max) {
        return "El valor debe ser menor o igual a {$max}.";
    }
    
    return null;
}

/**
 * Valida una fecha en formato YYYY-MM-DD.
 *
 * @param mixed $valor Fecha a validar.
 * @param bool $requerido Si es true, el campo no puede estar vacío.
 * @return ?string null si la fecha es válida, mensaje de error en caso contrario.
 */
function validarFecha($valor, bool $requerido = false): ?string
{
    if ($valor === '' || $valor === null) {
        return $requerido ? 'Campo requerido.' : null;
    }
    $fecha = DateTime::createFromFormat('Y-m-d', $valor);
    if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
        return 'Fecha inválida.';
    }
    return null;
}

function validarEnOpciones($valor, array $opciones, bool $requerido = false): ?string
{
    if ($valor === '' || $valor === null) {
        return $requerido ? 'Campo requerido.' : null;
    }
    if (!in_array($valor, $opciones)) return 'Valor no permitido.';
    return null;
}

/**
 * Valida que un campo no esté vacío (requerido).
 *
 * @param string $valor Valor a validar.
 * @return ?string null si el campo no está vacío, mensaje de error en caso contrario.
 */
function validarCampoRequerido(string $valor): ?string
{
    return trim($valor) === '' ? 'Campo requerido.' : null;
}

/**
 * Valida una contraseña.
 *
 * @param string $valor Contraseña a validar.
 * @param bool $requerido Si es true, el campo no puede estar vacío.
 * @return ?string null si la contraseña es válida, mensaje de error en caso contrario.
 */
function validarPassword(string $valor, bool $requerido = true): ?string
{
    $valor = trim($valor);
    $errores = [];

    if ($requerido && $valor === '') {
        return 'Campo requerido.';
    }

    if ($valor !== '') {

        if (strlen($valor) < 8) {
            $errores[] = 'Debe tener al menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $valor)) {
            $errores[] = 'Debe incluir al menos una letra mayúscula.';
        }

        if (!preg_match('/[a-z]/', $valor)) {
            $errores[] = 'Debe incluir al menos una letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $valor)) {
            $errores[] = 'Debe incluir al menos un número.';
        }

    }

    if (empty($errores)) {
        return null;
    } else {
        return implode(' ', $errores);
    }
}

/**
 * Valida un rol de usuario
 * 
 * @param string $rol Rol a validar
 * @return ?string null si el rol es válido, mensaje de error en caso contrario
 */
function validarRol(string $rol): ?string {
    if (!preg_match('/^(estudiante|profesor|administrativo)$/', $rol)) {
        return 'Rol no válido';
    }
    return null;
}

/**
 * Valida un estado de aprobación
 * 
 * @param string $estado Estado a validar
 * @return ?string null si el estado es válido, mensaje de error en caso contrario
 */
function validarEstadoAprobacion(string $estado): ?string {
    if (!preg_match('/^(pendiente|aprobado|rechazado)$/i', $estado)) {
        return 'Estado de aprobación no válido';
    }
    return null;
}

/**
 * Valida que un token sea un string hexadecimal de longitud 64.
 * 
 * @param string $token Token a validar.
 * @return ?string null si es válido, mensaje de error en caso contrario.
 */
function validarTokenHex(string $token): ?string
{
    return preg_match('/^[a-f0-9]{64}$/', $token) ? null : 'Token inválido.';
}


/**
 * Procesa filtros GET aplicando validaciones según configuración.
 *
 * Recorre cada filtro configurado en $configFiltros. Si el filtro está presente en $_GET y no es vacío:
 * - Si es un array, valida cada elemento individualmente.
 * - Si es un valor simple, valida ese valor.
 * 
 * Devuelve un array de filtros validados (seguros para pasar al modelo) y
 * devuelve por referencia un array de errores encontrados en $errores_filtros.
 *
 * Ejemplo de uso:
 * $config = [
 *   'nombre' => ['validador' => 'texto', 'max' => 150],
 *   'estado_aprobacion' => ['validador' => 'estadoAprobacion']
 * ];
 *
 * @param array $configFiltros Configuración de los filtros a procesar.
 * @param array &$errores_filtros Array donde se devuelven los errores encontrados.
 * @return array Array de filtros validados.
 */
function procesarFiltrosValidados(array $configFiltros, array &$errores_filtros = [])
{
    $filtros = [];

    foreach ($configFiltros as $filtro => $config) {
        if (isset($_GET[$filtro]) && $_GET[$filtro] !== '') {
            $valor = $_GET[$filtro];

            // Soporte para filtros múltiples (arrays)
            if (is_array($valor)) {
                $filtros_validos = [];
                foreach ($valor as $v) {
                    $v = trim($v);
                    $error = validarFiltroIndividual($v, $config);
                    if ($error !== null) {
                        $errores_filtros[$filtro] = $error;
                        // Si hay error, no seguir validando el resto
                        break;
                    } else {
                        $filtros_validos[] = $v;
                    }
                }
                // Solo si no hubo error, se agrega
                if (!isset($errores_filtros[$filtro])) {
                    $filtros[$filtro] = $filtros_validos;
                }
            } else {
                $valor = trim($valor);
                $error = validarFiltroIndividual($valor, $config);
                if ($error !== null) {
                    $errores_filtros[$filtro] = $error;
                } else {
                    // si en el config se pide forzar array, devuelve array
                    if (!empty($config['forzarArray'])) {
                        $filtros[$filtro] = [$valor];
                    } else {
                        $filtros[$filtro] = $valor;
                    }
                }
            }
        }
    }

    return $filtros;
}

/**
 * Valida un valor individual de un filtro según su configuración.
 * 
 * Se utiliza internamente por procesarFiltrosValidados para validar cada valor,
 * sea parte de un filtro múltiple o un valor simple.
 * 
 * @param mixed $valor Valor del filtro a validar.
 * @param array $config Configuración del filtro (debe incluir al menos 'validador').
 * @return string|null Retorna null si el valor es válido, o un mensaje de error si es inválido.
 */
function validarFiltroIndividual($valor, $config)
{
    $validador = $config['validador'] ?? 'texto';

    switch ($validador) {
        case 'texto':
            return validarTexto(
                $valor,
                $config['max'] ?? 255,
                $config['requerido'] ?? false,
                $config['validarCaracteres'] ?? true
            );

        case 'email':
            return validarEmail($valor, $config['requerido'] ?? false);

        case 'rol':
            return validarRol($valor);

        case 'entero':
            return validarEntero(
                $valor,
                $config['requerido'] ?? false,
                $config['min'] ?? null,
                $config['max'] ?? null
            );

        // validación de opciones
        case 'opciones':
            return validarEnOpciones(
                $valor,
                $config['opciones'] ?? [],
                $config['requerido'] ?? false
            );


        case 'estadoAprobacion':
            return validarEstadoAprobacion($valor);

        default:
            return "Validador desconocido para el filtro '{$validador}'.";
    }
}
