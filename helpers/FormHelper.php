<?php

/**
 * FormHelper.php
 *
 * Contiene funciones auxiliares para generar elementos de formulario recurrentes,
 * como inputs ocultos para mantener filtros al paginar o realizar acciones.
 *
 * @package Helpers
 */

/**
 * Genera una serie de inputs ocultos (<input type="hidden">) a partir de un array
 * de parámetros. Sirve para conservar valores de GET (filtros, paginación, etc.)
 * al enviar un formulario por POST.
 *
 * @param array $parametros  Array asociativo de parámetros a transformar en inputs ocultos.
 *                           - Clave: nombre del parámetro.
 *                           - Valor: string o array de strings (múltiples valores).
 *                           No incluye claves 'action' ni 'pagina'.
 *
 * @return string Cadena HTML con todos los inputs ocultos correspondientes.
 */
function generarInputsOcultos(array $parametros): string
{
    $html = '';
    foreach ($parametros as $clave => $valor) {
        if (in_array($clave, ['action','pagina'], true)) continue;
        if (is_array($valor)) {
            foreach ($valor as $item) {
                $html .= '<input type="hidden" name="' . htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') . '[]" value="' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '">' . "\n";
            }
        } else {
            $html .= '<input type="hidden" name="' . htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        }
    }
    return $html;
}
