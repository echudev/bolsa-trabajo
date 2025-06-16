<?php

/**
 * vistas/partials/mensajes.php
 * 
 * Muestra mensajes flash almacenados en la sesión.
 * Los mensajes pueden ser de tipo 'error', 'exito', 'info', 'advertencia' o 'mensaje' (por defecto).
 * 
 * La sesión ya debe estar iniciada antes de incluir este archivo.
 */

// Definir los tipos de mensajes que se maneja
$tiposMensajes = [
    'error' => 'mensaje-error',
    'exito' => 'mensaje-confirmacion',
    'advertencia' => 'mensaje-advertencia',
    'info' => 'mensaje-info',
    'mensaje' => 'mensaje',
];

// Mostrar cada tipo de mensaje si existe
foreach ($tiposMensajes as $tipo => $clase) {
    if (isset($_SESSION[$tipo]) && !empty($_SESSION[$tipo])) {
        // Si hay múltiples mensajes como array, se unen
        $mensaje = is_array($_SESSION[$tipo])
            ? implode('<br>', array_map('htmlspecialchars', $_SESSION[$tipo]))
            : htmlspecialchars($_SESSION[$tipo]);

        // Mostrar el mensaje
        echo "<div class='$clase mensaje-dismissible'>
                <span class='cerrar-mensaje' onclick='this.parentElement.style.display=\"none\";'>&times;</span>
                " . nl2br(htmlspecialchars($mensaje)) . "
                </div>";
        // Eliminar el mensaje para que no se muestre nuevamente
        unset($_SESSION[$tipo]);
    }
}
