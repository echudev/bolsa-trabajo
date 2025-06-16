<?php
// helpers/OrdenHelper.php

/**
 * OrdenHelper.php
 * 
 * Contiene funciones auxiliares para gestionar el ordenamiento de listados, 
 * generar enlaces de orden dinámico y flechas indicadoras (▲ / ▼)
 * que permiten al usuario ordenar listados en la interfaz.
 * 
 * Uso típico:
 * - Al renderizar una tabla en el listado, cada encabezado de columna tiene un enlace generado por este helper.
 * - El helper preserva todos los filtros activos en la URL.
 * 
 * Actualmente expone:
 * - generarUrlsYFlechas(): genera enlaces y flechas para todas las columnas configuradas.
 *
 * @package Helpers
 */


/**
 * Dado el query actual, la columna y dirección de orden actuales, 
 * y el listado de columnas permitidas, genera para cada clave:
 *   - un enlace que establece sort=<columna> y order=<ASC|DESC>,
 *   - una flecha (▲ para ASC, ▼ para DESC, '' para el resto).
 *
 * @param array $queryActual         Array con todos los parámetros de $_GET.
 * @param string $sortActual         Columna actualmente ordenada (p.ej. 'puesto').
 * @param string $orderActual        Dirección actual ('ASC' o 'DESC').
 * @param array $columnasPermitidas  Mapa asociativo: clave → “expresión SQL”.
 *                                   Ejemplo: ['puesto'=>'o.puesto', 'empresa'=>'o.empresa', …]
 *
 * @return array  [
 *   'links'  => [ 'puesto'=>'index.php?…&sort=puesto&order=ASC', … ],
 *   'flechas'=> [ 'puesto'=>'▲', 'empresa'=>'', … ]
 * ]
 */
function generarUrlsYFlechas(array $queryActual, string $sortActual, string $orderActual, array $columnasPermitidas): array
{
    // Asegurar que $orderActual esté en mayúsculas y sea 'ASC' o 'DESC'.
    $orderActual = strtoupper($orderActual) === 'DESC' ? 'DESC' : 'ASC';
    
    // Prepar dos arrays vacíos:
    $enlaces = [];
    $flechas = [];

    // Recorrer cada “alias” definido en $columnasPermitidas
    foreach ($columnasPermitidas as $alias => $_sqlExpression) {
        // Partir de la query existente (preservar todos los filtros):
        $params = $queryActual;

        // Si ya hay un sort=… en la URL, se reemplaza:
        $params['sort'] = $alias;

        // Definir la dirección inversa para este alias:
        if ($sortActual === $alias && $orderActual === 'ASC') {
            // Si YA está ordenado ASC por esta columna, ahora va  a DESC
            $params['order'] = 'DESC';
            $flechas[$alias] = '▲';  // flecha hacia arriba (indica orden ASC actual)
        } elseif ($sortActual === $alias && $orderActual === 'DESC') {
            // Si YA está ordenado DESC, al hacer clic pasar a ASC
            $params['order'] = 'ASC';
            $flechas[$alias] = '▼';  // flecha hacia abajo (indica orden DESC actual)
        } else {
            // Para las columnas que no están actualmente ordenadas:
            $params['order'] = 'ASC';
            $flechas[$alias] = '';   // sin flecha
        }

        // Reconstruir la query string (omitir claves con valor null o vacías)
        $qsParts = [];
        foreach ($params as $k => $v) {
            if ($v === null || $v === '') continue;
            // Si $v es array, generar múltiples “key[]=value”
            if (is_array($v)) {
                foreach ($v as $item) {
                    $qsParts[] = urlencode($k) . '[]=' . urlencode($item);
                }
            } else {
                $qsParts[] = urlencode($k) . '=' . urlencode($v);
            }
        }
        $qs = implode('&', $qsParts);

        // Guardar el enlace para este alias
        $enlaces[$alias] = 'index.php?' . $qs;
    }

    return [
        'links'   => $enlaces,
        'flechas' => $flechas,
    ];
}
