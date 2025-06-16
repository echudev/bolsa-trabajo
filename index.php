<?php
require_once 'modelos/OfertaModel.php';
require_once 'helpers/authHelper.php';
require_once 'controladores/controlador_oferta.php';
require_once 'controladores/controlador_usuario.php';
require_once 'controladores/controlador_admin.php';


// Enrutamiento
$action = $_GET['action'] ?? 'listado';


switch ($action) {
    // Acciones de OFERTAS
    case 'listado':
    case 'ver-oferta':
    case 'nueva-oferta':
    case 'editar-oferta':
    case 'eliminar-oferta':
        controladorOferta($action);
        break;

    // Acciones de USUARIOS
    case 'login':
    case 'procesar-login':
    case 'logout':
    case 'registro':
    case 'procesar-registro':
    case 'perfil':
    case 'procesar-perfil':
    case 'olvido-pass':
    case 'procesar-olvido-pass':
    case 'restablecer-pass':
    case 'procesar-restablecer-pass':
        controladorUsuario($action);
        break;

    // Acciones administrativas (usuarios, ofertas, configuración)
    case 'admin-panel':
    case 'admin-usuarios':
        //case 'editar-usuario':
    case 'cambiar-rol':
    case 'desactivar-usuario':
    case 'reactivar-usuario':
    case 'actualizar-estado-aprobacion-usuario':
    case 'accion-masiva-usuarios':
    case 'actualizar-estado-aprobacion-oferta':
    case 'accion-masiva-ofertas':
    case 'actualizar-configuracion':
    case 'eliminar-oferta-admin':
    case 'desactivar-oferta':
    case 'reactivar-oferta':
        //require_once 'controlador_admin.php';
        controladorAdmin($action);
        break;

    default:
        echo "Acción no reconocida.";
        break;
}
