<?php

/**
 * Sanitiza contenido HTML permitiendo solo un conjunto limitado de etiquetas seguras,
 * y eliminando atributos potencialmente peligrosos (on*, style, class, id, data-*, aria-*, target, href, src).
 * 
 * Se usa para limpiar contenido editable por el usuario (descripciones de ofertas).
 * 
 * @param string $html Contenido HTML a sanitizar.
 * @return string HTML limpio con solo las etiquetas y atributos permitidos.
 */
function limpiarHTMLSeguro(string $html): string {
    // Lista de etiquetas permitidas
    $etiquetas_permitidas = ['b', 'strong', 'i', 'em', 'u', 'ul', 'ol', 'li', 'p', 'br', 'div', '\n'];
    
    // Quitar tags no permitidas
    $html = strip_tags($html, '<' . implode('><', $etiquetas_permitidas) . '>');

    // Remover atributos peligrosos (como onmouseover, onclick, style, etc)
    $html = preg_replace_callback(
        '/<([a-z]+)([^>]*)>/i',
        function ($matches) use ($etiquetas_permitidas) {
            $tag = strtolower($matches[1]);

            // Asegurar que sea una etiqueta válida
            if (!in_array($tag, $etiquetas_permitidas)) return '';

            // Quitar atributos peligrosos
            $attrs = preg_replace('/\s*(on\w+|style|class|id|data-[^=]+|aria-[^=]+|target|href|src)\s*=\s*"[^"]*"/i', '', $matches[2]);

            return "<$tag$attrs>";
        },
        $html
    );

    return $html;
}
