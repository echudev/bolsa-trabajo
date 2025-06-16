<?php

// Seguridad y login
define('MAX_INTENTOS_LOGIN', 5);
define('TIEMPO_BLOQUEO_LOGIN', 1800); // en segundos (30 minutos)

// Sesiones
define('TIEMPO_EXPIRACION_SESION', 8 * 60 * 60); // en segundos (8 horas)

// Paginación
define('PAGINADOR_MAX_VISIBLE_PAGINAS', 5);
