<?php

require_once __DIR__ . '/../config/constantes.php';

class PaginadorHelper
{
    /**
     * Calcula el offset a utilizar en la consulta paginada.
     *
     * @param int $pagina_actual Página actual.
     * @param int $por_pagina Cantidad de resultados por página.
     * @return int Offset correspondiente.
     */
    public static function calcularOffset(int $pagina_actual, int $por_pagina): int
    {
        return ($pagina_actual > 1) ? ($pagina_actual - 1) * $por_pagina : 0;
    }

    /**
     * Calcula el total de páginas necesarias.
     *
     * @param int $total_items Total de elementos.
     * @param int $por_pagina Cantidad de resultados por página.
     * @return int Total de páginas.
     */
    public static function totalPaginas(int $total_items, int $por_pagina): int
    {
        return (int) ceil($total_items / $por_pagina);
    }

    /**
     * Configura la paginación para listados.
     * 
     * @param array $params Parámetros de la petición ($_GET)
     * @param int $limiteDefault Límite por defecto de resultados por página
     * @param int $limiteMin Límite mínimo permitido
     * @param int $limiteMax Límite máximo permitido
     * @return array Array con los datos de paginación
     */
    public static function configurarPaginacion($params, $limiteDefault = 20, $limiteMin = 5, $limiteMax = 100)
    {
        $paginaActual = max(1, (int)($params['pagina'] ?? 1));
        $limite = min(max($limiteMin, (int)($params['limite'] ?? $limiteDefault)), $limiteMax);

        return [
            'pagina_actual' => $paginaActual,
            'limite' => $limite,
            'offset' => self::calcularOffset($paginaActual, $limite)
        ];
    }

    /**
     * Configura el ordenamiento para listados.
     * 
     * @param array $params Parámetros de la petición ($_GET)
     * @param array $columnasPermitidas Mapa de columnas permitidas [nombre => campo_bd]
     * @param string $ordenDefault Orden por defecto (ASC o DESC)
     * @return array Array con los datos de ordenamiento
     */
    public static function configurarOrdenamiento($params, $columnasPermitidas, $ordenDefault = 'ASC')
    {
        $columna = $params['sort'] ?? '';
        $orden = strtoupper($params['order'] ?? $ordenDefault);

        $resultado = [];

        // Si la columna es válida, guardar tanto la clave
        if (isset($columnasPermitidas[$columna])) {
            $resultado['sort'] = $columnasPermitidas[$columna];  // Valor para la consulta SQL
            $resultado['sort_key'] = $columna;  // Clave para generar URLs
        }

        $resultado['order'] = ($orden === 'DESC') ? 'DESC' : 'ASC';

        return $resultado;
    }

    /**
     * Construye una URL a partir de los parámetros actuales de $_GET combinados con parámetros adicionales.
     * 
     * Se usa para mantener filtros/paginación/orden al navegar o aplicar acciones.
     * 
     * @param array $params_extra Parámetros adicionales o modificados a incluir en la URL.
     * @return string URL resultante (ejemplo: 'index.php?pagina=2&orden=asc').
     */
    public static function construirURL(array $params_extra = []): string
    {
        $params = array_merge($_GET, $params_extra);
        return 'index.php?' . http_build_query($params);
    }

    /**
     * Renderiza la barra de paginación (HTML) para el listado actual.
     * 
     * Puede renderizarse en modo "inferior" (con flechas << >>) o "superior" (compacta, sin flechas).
     * 
     * @param int|null $paginaActual Número de página actual.
     * @param int $totalPaginas Total de páginas disponibles.
     * @param string $modo 'inferior' o 'superior' (compacto).
     * @return string HTML con la paginación.
     */
    public static function renderizarPaginacion(?int $paginaActual, int $totalPaginas, string $modo = 'inferior'): string
    {
        if ($totalPaginas <= 1) return '';

        // Si no hay página en URL, debe ser 1
        if ($paginaActual == null || $paginaActual < 1) {
            $paginaActual = 1;
        }
        if ($paginaActual > $totalPaginas) {
            $paginaActual = $totalPaginas;
        }

        $esCompacta = $modo === 'superior';
        $claseContenedor = $esCompacta ? 'paginacion paginacion-compacta' : 'paginacion';
        $maxVisible = PAGINADOR_MAX_VISIBLE_PAGINAS;

        $html = "<nav class=\"$claseContenedor\"><ul>";

        // Flecha izquierda (solo en inferior)
        if (!$esCompacta && $paginaActual > 1) {
            $html .= '<li><a href="' . self::construirURL(['pagina' => 1]) . '">&laquo;</a></li>';
        }

        // Mostrar página 1
        if ($paginaActual == 1) {
            $html .= $esCompacta ? '<li><strong>1</strong></li>' : '<li class="actual"><a href="#">1</a></li>';
        } else {
            $html .= '<li><a href="' . self::construirURL(['pagina' => 1]) . '">1</a></li>';
        }

        // Calcular bloque de páginas intermedias
        $half = floor(($maxVisible - 2) / 2);
        $desde = max(2, $paginaActual - $half);
        $hasta = min($totalPaginas - 1, $paginaActual + $half);

        if ($paginaActual <= $half + 2) {
            $desde = 2;
            $hasta = min($totalPaginas - 1, $maxVisible - 1);
        } elseif ($paginaActual >= $totalPaginas - $half - 1) {
            $desde = max(2, $totalPaginas - ($maxVisible - 2));
            $hasta = $totalPaginas - 1;
        }

        // FORZAR que la página actual esté en el rango
        if ($paginaActual >= 2 && $paginaActual <= $totalPaginas - 1) {
            $desde = min($desde, $paginaActual);
            $hasta = max($hasta, $paginaActual);
        }

        // Puntos suspensivos después del 1
        if ($desde > 2) {
            $html .= '<li><span class="puntos">…</span></li>';
        }

        // Páginas intermedias
        for ($i = $desde; $i <= $hasta; $i++) {
            if ($i == $paginaActual) {
                $html .= $esCompacta
                    ? "<li><strong>$i</strong></li>"
                    : "<li class=\"actual\"><a href=\"#\">$i</a></li>";
            } else {
                $html .= '<li><a href="' . self::construirURL(['pagina' => $i]) . "\">$i</a></li>";
            }
        }

        // Puntos suspensivos antes del final
        if ($hasta < $totalPaginas - 1) {
            $html .= '<li><span class="puntos">…</span></li>';
        }

        // Última página (solo si es mayor que 1)
        if ($totalPaginas > 1) {
            if ($paginaActual == $totalPaginas) {
                $html .= $esCompacta
                    ? "<li><strong>$totalPaginas</strong></li>"
                    : "<li class=\"actual\"><a href=\"#\">$totalPaginas</a></li>";
            } else {
                $html .= '<li><a href="' . self::construirURL(['pagina' => $totalPaginas]) . "\">$totalPaginas</a></li>";
            }
        }

        // Flecha derecha (solo en inferior)
        if (!$esCompacta && $paginaActual < $totalPaginas) {
            $html .= '<li><a href="' . self::construirURL(['pagina' => $totalPaginas]) . '">&raquo;</a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }
}
