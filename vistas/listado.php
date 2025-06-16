<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolsa de Trabajo</title>
    <link rel="stylesheet" href="assets/css/bolsa_trabajo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/listado_ofertas.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/paginacion.css?v=<?= time(); ?>">

</head>

<body>
    <?php if (!estaLogueado()): ?>
        <section class="hero-container">
            <div class="hero-text">
                <h2 class="hero-title">Portal de Empleo y Pasantías</h2>
                <p class="hero-description">Encontrá oportunidades laborales que se ajusten a tus habilidades y experiencia. Desde el IFTS4 ponemos esta herramienta a disposición de la comunidad para facilitar el ingreso al sector IT.</p>
                <p class="hero-letrachica">La institución no se responsabiliza por los términos, condiciones, cumplimiento, ni veracidad de las propuestas laborales presentadas por terceros.</p>

                <a class="hero-btn-ingresar" href="index.php?action=login">Acceder</a>
                <p>¿No tenés cuenta? <a href="index.php?action=registro" class="hero-btn-registro">Registrate</a></p>

                <img src="assets/img/line.png" alt="wave-line" class="wave-line">
                <img src="assets/img/circle.png" alt="green-circle" class="green-circle">
                <img src="assets/img/circle.png" alt="green-circle" class="green-circle">
            </div>
            <img src="assets/img/hero.jpg" alt="hero" class="hero-image">
        </section>
    <?php else: ?>
        <section class="hero-container">
            <div class="hero-text">
                <h2 class="hero-title">Portal de Empleo</h2>
                <p class="hero-description">Encontrá oportunidades laborales que se ajusten a tus habilidades y experiencia. Desde el IFTS4 ponemos esta herramienta a disposición de la comunidad para facilitar el ingreso al sector IT.</p>
                <p class="hero-bienvenido">
                    Bienvenid@! <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></span>
                </p>
                <p class="hero-letrachica">La institución no se responsabiliza por los términos, condiciones, cumplimiento, ni veracidad de las propuestas laborales presentadas por terceros.</p>
                <div class="hero-buttons-container">
                    <a class="hero-btn-primary" href="index.php?action=nueva-oferta">Publicar</a>

                    <?php if (esAdmin()): ?>
                        <a class="hero-btn-secondary" href="index.php?action=admin-panel&seccion=usuarios">Panel de Administración</a>

                    <?php else: ?>
                        <a class="hero-btn-secondary" href="index.php?action=perfil">Actualizar perfil</a>

                    <?php endif; ?>

                </div>

                <a href="index.php?action=logout" class="hero-btn-cerrarsesion">Cerrar sesión</a>
                <img src="assets/img/line.png" alt="wave-line" class="wave-line">
                <img src="assets/img/circle.png" alt="green-circle" class="green-circle">
                <img src="assets/img/circle.png" alt="green-circle2" class="green-circle2">
            </div>
            <img src="assets/img/hero.jpg" alt="hero" class="hero-image">
        </section>
    <?php endif; ?>

    <?php
    // Mostrar mensajes de sesión
    include 'partials/mensajes.php';

    // Mostrar mensajes pasados por GET (para compatibilidad con código existente)
    if (!empty($_GET['mensaje'])) {
        echo '<p class="mensaje">' . htmlspecialchars($_GET['mensaje']) . '</p>';
    }
    ?>

    <!-- Formulario de filtros -->
    <form method="get" action="index.php" class="form-filtros">
        <input type="hidden" name="action" value="listado">

        <label class="label-busqueda" for="busqueda">Buscá el trabajo que querés</label>
        <p class="subtitle-busqueda">Navegá entre las diferentes ofretas laborales y encontrá la que más te interese.</p>
        <input class="input-busqueda" type="text" name="busqueda" placeholder="Buscar palabra clave" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
        <div class="divisor"></div>

        <div class="botonera-contenedor">
            <div class="grupo-filtros">
                <select class="input-select" name="modalidad">
                    <option value="">Modalidad</option>
                    <?php foreach (['presencial', 'remoto', 'híbrido'] as $opcion): ?>
                        <option value="<?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?>" <?= ($_GET['modalidad'] ?? '') === $opcion ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($opcion), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select class="input-select" name="jornada">
                    <option value="">Jornada</option>
                    <option value="completa" <?= ($_GET['jornada'] ?? '') === 'completa' ? 'selected' : '' ?>>Completa</option>
                    <option value="parcial" <?= ($_GET['jornada'] ?? '') === 'parcial' ? 'selected' : '' ?>>Parcial</option>
                </select>

                <select class="input-select" name="experiencia">
                    <option value="">Experiencia</option>
                    <option value="0" <?= ($_GET['experiencia'] ?? '') === '0' ? 'selected' : '' ?>>Sin experiencia</option>
                    <option value="1" <?= ($_GET['experiencia'] ?? '') === '1' ? 'selected' : '' ?>>1 año</option>
                    <option value="2" <?= ($_GET['experiencia'] ?? '') === '2' ? 'selected' : '' ?>>2 años</option>
                    <option value="3-5" <?= ($_GET['experiencia'] ?? '') === '3-5' ? 'selected' : '' ?>>De 3 a 5 años</option>
                    <option value="+5" <?= ($_GET['experiencia'] ?? '') === '+5' ? 'selected' : '' ?>>Más de 5 años</option>
                </select>

                <?php if (estaLogueado()): ?>
                    <label class="custom-checkbox">
                        <input type="checkbox" name="solo_propias" <?= !empty($_GET['solo_propias']) ? 'checked' : '' ?>>
                        <span>Mis ofertas</span>
                    </label>
                <?php endif; ?>

                <label class="custom-checkbox">
                    <input type="checkbox" name="ver_inactivas" <?= !empty($_GET['ver_inactivas']) ? 'checked' : '' ?>>
                    <span>Ver finalizadas</span>
                </label>

                <div class="filtrar-limpiar-contenedor">
                    <button class="btn-filtrar" type="submit">Filtrar</button>
                    <!-- <a class="btn-limpiar" href="index.php">Limpiar</a> -->

                    <a class="btn-limpiar" href="index.php" title="Limpiar filtros">
                        <span class="icono-limpiar">
                            <?php
                            //include __DIR__ . '/../assets/icons/icon_clear_filters.svg'; 
                            include __DIR__ . '/partials/icons/icon_clear_filters.php'; ?>
                            <?php if ($filtros_activos > 0): ?>
                                <span class="badge"><?= $filtros_activos ?></span>
                            <?php endif; ?>
                        </span>
                    </a>
                </div>
            </div>

        </div>
    </form>
    <div class="paginacion-superior-wrapper">
        <div class="paginacion-superior-inner">

            <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'superior') ?>
        </div>

    </div>

    <?php if (empty($ofertas)): ?>
        <div class="mensaje-vacio">
            <p>No hay ofertas que coincidan con los filtros seleccionados.</p>
            <a href="index.php?action=listado" class="btn-volver">Limpiar filtros</a>
        </div>
    <?php else: ?>
        <div class="trabajos-grid">
            <?php foreach ($ofertas as $oferta): ?>
                <div class="trabajo-card">
                    <a href="index.php?action=ver-oferta&id_oferta=<?= $oferta['id_oferta'] ?>" class="card-overlay-link" aria-label="Ver oferta completa" target="blank"></a>

                    <div class="trabajo-card-contenido">

                        <h3 class="trabajo-card-title"><?= htmlspecialchars($oferta['puesto']) ?></h3>
                        <p class="trabajo-card-empresa"><strong>Empresa:</strong> <?= htmlspecialchars($oferta['empresa']) ?></p>

                        <div class="trabajo-card-dato">
                            <!-- <strong>Ubicación:</strong> -->
                            <span> Ubicación: <?= empty($oferta['ubicacion']) ? 'No especificado' : htmlspecialchars($oferta['ubicacion']) ?></span>
                            <span class="trabajo-card-badge <?= 'modalidad-' . strtolower($oferta['modalidad']) ?>">
                                <?= ucfirst($oferta['modalidad']) ?>
                            </span>
                        </div>

                        <div class="trabajo-card-dato">
                            <?php if (is_numeric($oferta['experiencia_requerida']) && (int)$oferta['experiencia_requerida'] == 0): ?>
                                Sin experiencia requerida
                            <?php elseif (is_numeric($oferta['experiencia_requerida']) && (int)$oferta['experiencia_requerida'] == 1): ?>
                                Experiencia: 1 año
                            <?php elseif (is_numeric($oferta['experiencia_requerida'])): ?>
                                Experiencia: <?= htmlspecialchars($oferta['experiencia_requerida']) ?> años
                            <?php else: ?>
                                Experiencia: No especificado
                            <?php endif; ?>
                            </span>
                        </div>
                    </div>


                    <div class="trabajo-card-footer">
                        <!--<a class="trabajo-card-link" href="index.php?action=ver-oferta&id_oferta=<?= $oferta['id_oferta'] ?>">Ver detalles</a> -->
                        <?php
                        $estado = $oferta['activa'] ? '<span class="estado-abierta">Abierta</span>' : '<span class="estado-finalizada">Finalizada</span>';
                        ?>
                        <small class="trabajo-card-meta">
                            Publicado hace <?= htmlspecialchars($oferta['tiempo_publicacion']) ?> | Estado: <?= $estado ?>
                        </small>
                        <?php if ($oferta['puede_editar']): ?>
                            <div class="trabajo-card-actions">
                                <a href="index.php?action=editar-oferta&id_oferta=<?= $oferta['id_oferta'] ?>" title="Editar">
                                    <svg class="icon icon-edit" viewBox="0 0 24 24">
                                        <path d="M4 21h4l10-10-4-4L4 17v4zM20.7 7.3a1 1 0 0 0 0-1.4l-3.6-3.6a1 1 0 0 0-1.4 0l-1.8 1.8 5 5 1.8-1.8z" />
                                    </svg>
                                    Editar
                                </a>
                                <a href="index.php?action=eliminar-oferta&id_oferta=<?= $oferta['id_oferta'] ?>" onclick="return confirm('¿Estás seguro de que querés eliminar esta oferta?');" title="Eliminar">
                                    <svg class="icon icon-delete" viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                    Eliminar
                                </a>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="paginacion-inferior-wrapper">
            <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'inferior') ?>
        </div>

    <?php endif; ?>



    <script src="assets/js/scroll-y.js?v=<?= time(); ?>"></script>


</body>

</html>