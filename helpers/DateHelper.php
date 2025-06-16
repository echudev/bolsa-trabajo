<?php
/**
 * Devuelve una fecha con formato "dd-mm-aaaa". 
 * Si $fechaMod es nula o vacía, usa $fechaCreac; en caso contrario toma $fechaMod.
 * Si la fecha no es válida, devuelve cadena vacía.
 *
 * @param string|null $fechaMod       Cadena de la fecha de modificación (ej. "2023-05-10 14:23:45")
 * @param string      $fechaCreac     Cadena de la fecha de creación (ej. "2023-03-01 09:12:00")
 * @return string                     Fecha en formato "dd-mm-aaaa", o cadena vacía si ambas son inválidas o no son fechas válidas.
 */
function formatearFechaUltimaModificacion(?string $fechaMod, string $fechaCreac): string
{
    // Se elige la fecha a mostrar: primero fecha_modificacion si no es null, 
    // sino fecha_creacion (que siempre existe).
    $fecha = $fechaMod !== null && $fechaMod !== '' 
             ? $fechaMod 
             : $fechaCreac;

    if (empty($fecha)) {
        return '';
    }

    // Convertir a timestamp y devolvemos en formato día-mes-año
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return '';
    }

    return date('d-m-Y', $timestamp);
}
