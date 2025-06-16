<?php require_once 'helpers/SanitizadorHelper.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($oferta['puesto']) ?> | Detalle de oferta</title>
    <link rel="stylesheet" href="assets/css/bolsa_trabajo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/detalle_oferta.css?v=<?= time(); ?>">
</head>

<body>

    <main class="detalle-oferta detalle-card">

        <?php include 'partials/mensajes.php'; ?>

        <p><a href="index.php?action=listado" class="volver-a-listado superior">Volver</a></p>

        <header class="detalle-header">
            <h1 class="detalle-titulo"><?= htmlspecialchars($oferta['puesto']) ?></h1>
            <p class="detalle-subtitulo">
                <?= htmlspecialchars($oferta['empresa']) ?> · <?= htmlspecialchars($oferta['ubicacion']) ?>
            </p>
        </header>

        <section class="detalle-seccion">
            <h2 class="detalle-subseccion-titulo" onclick="toggleSeccion(this)">
                <span>Condiciones</span>
                <span class="detalle-icono-toggle">
                    <?php include __DIR__ . '/partials/icons/icon_arrow_down.php'; ?>
                </span>
            </h2>
            <div class="detalle-condiciones-grid detalle-seccion-contenido">
                <?php if ($oferta['jornada']): ?>
                    <div class="detalle-condicion">
                        <?php include __DIR__ . '/partials/icons/icon_jornada.php'; ?>
                        <span><strong>Jornada: </strong><?= htmlspecialchars(ucfirst($oferta['jornada']), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endif; ?>

                <div class="detalle-condicion">
                    <?php include __DIR__ . '/partials/icons/icon_modalidad.php'; ?>
                    <span><strong>Modalidad: </strong><?= htmlspecialchars(ucfirst($oferta['modalidad']), ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="detalle-condicion">
                    <?php include __DIR__ . '/partials/icons/icon_experiencia.php'; ?>
                    <?= $experienciaRequerida = null; ?>

                    <?php if (is_numeric($oferta['experiencia_requerida']) && (int)$oferta['experiencia_requerida'] == 0): ?>
                        <strong> Sin experiencia requerida </strong>
                    <?php elseif (is_numeric($oferta['experiencia_requerida']) && (int)$oferta['experiencia_requerida'] == 1): ?>
                        <strong> Experiencia requerida: </strong> 1 año
                    <?php elseif (is_numeric($oferta['experiencia_requerida'])): ?>
                        <strong> Experiencia requerida: </strong> <?= htmlspecialchars($oferta['experiencia_requerida']) ?> años
                    <?php else: ?>
                        <strong> Experiencia requerida: </strong> No especificado
                    <?php endif; ?>

                </div>

                <?php if ($oferta['horario']): ?>
                    <div class="detalle-condicion detalle-condicion-horario">
                        <?php include __DIR__ . '/partials/icons/icon_horario.php'; ?>
                        <span><strong>Horario: </strong><?= htmlspecialchars($oferta['horario']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="detalle-seccion">
            <h2 class="detalle-subseccion-titulo" onclick="toggleSeccion(this)">
                <span>Descripción</span>
                <span class="detalle-icono-toggle"><?php include __DIR__ . '/partials/icons/icon_arrow_down.php'; ?></span>
            </h2>
            <div class="detalle-seccion-contenido">
                <div><?= limpiarHTMLSeguro($oferta['descripcion']) ?></div>
            </div>
        </section>



        <section class="detalle-seccion">
            <h2 class="detalle-subseccion-titulo" onclick="toggleSeccion(this)">
                <span>Contacto</span>
                <span class="detalle-icono-toggle"><?php include __DIR__ . '/partials/icons/icon_arrow_down.php'; ?></span>

            </h2>
            <div class="detalle-seccion-contenido">
                <?php if ($hayContacto): ?>

                    <?php if (!empty($oferta['enlace'])): ?>

                        <div class="detalle-contacto-item">
                            <?php include __DIR__ . '/partials/icons/icon_link.php'; ?>
                            <a href="<?= htmlspecialchars($oferta['enlace']) ?>" target="_blank"><?= htmlspecialchars($oferta['enlace']) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($oferta['email_contacto'])): ?>
                        <div class="detalle-contacto-item">
                            <?php include __DIR__ . '/partials/icons/icon_mail.php'; ?>
                            <a href="mailto:<?= htmlspecialchars($oferta['email_contacto']) ?>"><?= htmlspecialchars($oferta['email_contacto']) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($oferta['telefono_contacto'])): ?>
                        <div class="detalle-contacto-item">
                            <?php include __DIR__ . '/partials/icons/icon_phone.php'; ?>
                            <a href="tel:<?= htmlspecialchars($oferta['telefono_contacto']) ?>"><?= htmlspecialchars($oferta['telefono_contacto']) ?></a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No se ha suministrado información de contacto.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="detalle-seccion">
            <h2 class="detalle-subseccion-titulo" onclick="toggleSeccion(this)">
                <span>Información adicional</span>
                <span class="detalle-icono-toggle"><?php include __DIR__ . '/partials/icons/icon_arrow_down.php'; ?></span>
            </h2>
            
            <div class="detalle-seccion-contenido">
                <p class="detalle-linea-con-badge">
                    <strong>Estado:</strong>
                    <span class="trabajo-card-badge <?= $oferta['activa'] ? 'estado-abierta' : 'estado-finalizada' ?>">
                        <?= $oferta['activa'] ? 'Abierta' : 'Finalizada' ?>
                    </span>
                </p>
                <p><strong>Publicado hace:</strong> <?= htmlspecialchars($oferta['tiempo_publicacion']) ?> (<?= htmlspecialchars($oferta['fecha_creacion']) ?>)</p>
                <?php if ($oferta['fecha_modificacion']): ?>
                    <p><strong>Última modificación:</strong> <?= htmlspecialchars($oferta['fecha_modificacion']) ?></p>
                <?php endif; ?>
                <?php if ($oferta['fecha_fin']): ?>
                    <p><strong>Finaliza el:</strong> <?= htmlspecialchars($oferta['fecha_fin']) ?></p>
                <?php endif; ?>

                <?php if (esAdmin() || $oferta['publicada_por'] == idUsuario()): ?>
                    <p><strong>Estado de aprobación:</strong>
                        <?php
                        switch ($oferta['estado_aprobacion']) {
                            case 'pendiente':
                                echo 'Pendiente de aprobación';
                                break;
                            case 'rechazado':
                                echo 'Rechazado por el administrador';
                                break;
                            case 'aprobado':
                            default:
                                echo 'Aprobado';
                                break;
                        }
                        ?>
                    </p>
                <?php endif; ?>

            </div>
        </section>

        <?php if (!empty($oferta['puede_editar'])): ?>
            <div class="detalle-acciones">
                <a href="index.php?action=eliminar-oferta&id_oferta=<?= $oferta['id_oferta'] ?>"
                    onclick="return confirm('¿Eliminar esta oferta?');"
                    class="btn-danger">Eliminar</a>
                <a href="index.php?action=editar-oferta&id_oferta=<?= $oferta['id_oferta'] ?>" class="hero-btn-primary">Editar</a>
            </div>
        <?php endif; ?>

        <p><a href="index.php?action=listado" class="volver-a-listado">Volver</a></p>


    </main>

    <script>
        function toggleSeccion(titulo) {
            const seccion = titulo.closest('.detalle-seccion');
            seccion.classList.toggle('colapsada');
        }
    </script>
    <script src="assets/js/volver.js?v=<?= time(); ?>"></script>

</body>

</html>