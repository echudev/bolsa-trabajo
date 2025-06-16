<?php
require_once 'helpers/FormHelper.php';
require_once 'helpers/DateHelper.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de ofertas</title>
    <link rel="stylesheet" href="assets/css/paginacion.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

    <script src="assets/js/admin-ofertas-updates.js"></script>
</head>

<body>
    <main class="admin-container">
        <?php include 'vistas/partials/admin_tabs.php'; ?>

        <p><a href="index.php?action=listado" class="volver-link">← Volver a ofertas</a></p>

        <?php include 'partials/mensajes.php'; ?>

        <section class="admin-box">
            <h2>Administración de ofertas</h2>

            <form method="get" action="index.php" class="formulario-filtros">
                <input type="hidden" name="action" value="admin-panel">
                <input type="hidden" name="seccion" value="ofertas">

                <div class="grupo-filtro">
                    <label for="filtro-busqueda" class="sr-only">Buscar por puesto o descripción</label>
                    <input id="filtro-busqueda" type="text" name="busqueda" placeholder="Puesto o descripción" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">

                    <label for="usuario" class="sr-only">Buscar por usuario</label>
                    <input id="usuario" type="text" name="usuario" placeholder="Publicada por" value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>">
                </div>

                <div class="filtro-dropdown">
                    <div class="dropdown-titulo" onclick="toggleDropdown(this)" data-base="Aprobación">
                        Aprobación <span class="flecha">▼</span>
                    </div>
                    <div class="dropdown-opciones">
                        <?php foreach (['pendiente', 'aprobado', 'rechazado'] as $estado): ?>
                            <label>
                                <input type="checkbox" name="estado_aprobacion[]" value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>" <?= in_array($estado, $_GET['estado_aprobacion'] ?? []) ? 'checked' : '' ?>>
                                <?= htmlspecialchars(ucfirst($estado), ENT_QUOTES, 'UTF-8') ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <select name="estado">
                    <option value="">Estado</option>
                    <option value="1" <?= ($_GET['estado'] ?? '') === '1' ? 'selected' : '' ?>>Abiertas</option>
                    <option value="0" <?= ($_GET['estado'] ?? '') === '0' ? 'selected' : '' ?>>Finalizadas</option>
                </select>



                <div class="botones-filtro">
                    <button type="submit">Filtrar</button>
                    <a href="index.php?action=admin-panel&seccion=ofertas" class="btn-limpiar">Limpiar</a>
                </div>
            </form>
        </section>

        <hr>



        <nav class="paginacion-superior-wrapper">
            <div class="paginacion-superior-inner">
                <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'superior') ?>
            </div>
        </nav>

        <?php if (empty($ofertas)): ?>
            <p>No se encontraron ofertas.</p>
        <?php else: ?>


            <section class="tabla-admin">
                <form method="POST" action="index.php?action=accion-masiva-ofertas" data-context="admin-ofertas">
                    <?= campoCSRF() ?>
                    <?= generarInputsOcultos($_GET) ?>
                    <div class="acciones-masivas">
                        <div class="acciones-izquierda">
                            <label for="limite">Mostrar:</label>
                            <select id="limite" name="limite" onchange="this.form.submit()">
                                <?php foreach ([10, 20, 50, 100] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $limite == $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="acciones-derecha">
                            <select name="accion">
                                <option value="">-- Acción masiva --</option>
                                <option value="aprobar">Aprobar</option>
                                <option value="rechazar">Rechazar</option>
                                <option value="eliminar">Eliminar</option>
                                <option value="finalizar">Finalizar</option>
                                <option value="reabrir">Reabrir</option>
                            </select>
                            <button type="submit">Aplicar</button>
                        </div>
                    </div>




                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="seleccionar-todos"></th>
                                <th>
                                    <a href="<?= $orden['links']['puesto'] ?>">
                                        Puesto<?= $orden['flechas']['puesto'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-mediana">
                                    <a href="<?= $orden['links']['empresa'] ?>">
                                        Empresa<?= $orden['flechas']['empresa'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-mediana">
                                    <a href="<?= $orden['links']['publicada_por'] ?>">
                                        Publicado por<?= $orden['flechas']['publicada_por'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-mediana">
                                    <a href="<?= $orden['links']['ultima_modificacion'] ?>">
                                        Última modificación<?= $orden['flechas']['ultima_modificacion'] ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= $orden['links']['activa'] ?>">
                                        Abierta<?= $orden['flechas']['activa'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-tableta">
                                    <a href="<?= $orden['links']['estado_aprobacion'] ?>">
                                        Aprobación<?= $orden['flechas']['estado_aprobacion'] ?>
                                    </a>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ofertas as $oferta): ?>
                                <tr>
                                    <td><input type="checkbox" name="seleccionados[]" value="<?= $oferta['id_oferta'] ?>"></td>
                                    <td><a href="index.php?action=ver-oferta&id_oferta=<?= htmlspecialchars($oferta['id_oferta'], ENT_QUOTES, 'UTF-8') ?>" target="blank"><?= htmlspecialchars($oferta['puesto'], ENT_QUOTES, 'UTF-8') ?></a>
                                        - <a href="index.php?action=editar-oferta&id_oferta=<?= htmlspecialchars($oferta['id_oferta'], ENT_QUOTES, 'UTF-8') ?>"
                                            target="_blank"
                                            class="icono-editar"
                                            title="Editar oferta">
                                            ✏️
                                        </a>

                                    </td>
                                    <td class="col-oculta-mediana"><?= htmlspecialchars($oferta['empresa']) ?></td>
                                    <td class="col-oculta-mediana"><?= htmlspecialchars($oferta['nombre'] . ' ' . $oferta['apellido']) ?> (<?= $oferta['rol'] ?>)</td>
                                    <td class="col-oculta-mediana">
                                        <?= formatearFechaUltimaModificacion(
                                            $oferta['fecha_modificacion'],
                                            $oferta['fecha_creacion']
                                        )
                                        ?>
                                    </td>
                                    <td data-label="Activo"><?= $oferta['activa'] ? 'Abierta' : 'Finalizada' ?></td>
                                    <td class="col-oculta-tableta">
                                        <?php if (($oferta['estado_aprobacion'] ?? '') === 'pendiente'): ?>
                                            <button type="button" class="btn-aprobar" onclick="actualizarEstadoAprobacionOferta(<?= $oferta['id_oferta'] ?>, 'aprobado')">Aprobar</button>
                                            <button type="button" class="btn-rechazar" onclick="actualizarEstadoAprobacionOferta(<?= $oferta['id_oferta'] ?>, 'rechazado')">Rechazar</button>
                                        <?php else: ?>
                                            <?= ucfirst($oferta['estado_aprobacion'] ?? '-') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-rechazar" onclick="eliminarOferta(<?= $oferta['id_oferta'] ?>)">Eliminar</button>

                                        <?php if ($oferta['activa']): ?>

                                            <button type="button" class="btn-rechazar" onclick="finalizarOferta(<?= $oferta['id_oferta'] ?>)">Finalizar</button>

                                        <?php else: ?>
                                            <button type="button" class="btn-aprobar" onclick="reactivarOferta(<?= $oferta['id_oferta'] ?>, '<?= $oferta['fecha_fin'] ?>')">Reabrir</button>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
                <nav class="paginacion-inferior-wrapper">
                    <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'inferior') ?>
                </nav>

            </section>
        <?php endif; ?>
    </main>


    <script src="assets/js/admin-filtros.js"></script>
    <script src="assets/js/scroll-y.js?v=<?= time(); ?>"></script>


</body>

</html>