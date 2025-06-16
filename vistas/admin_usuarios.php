<?php
require_once 'helpers/FormHelper.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de usuarios</title>
    <link rel="stylesheet" href="assets/css/paginacion.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

    <script src="assets/js/admin-usuarios-updates.js"></script>

</head>

<body>

    <main class="admin-container">

        <?php include 'vistas/partials/admin_tabs.php'; ?>


        <p><a href="index.php?action=listado" class="volver-link">← Volver a ofertas</a></p>

        <?php include 'partials/mensajes.php'; ?>

        <section class="admin-box">
            <h2>Administración de usuarios</h2>

            <form method="get" action="index.php" class="formulario-filtros">
                <input type="hidden" name="action" value="admin-panel">
                <input type="hidden" name="seccion" value="usuarios">

                <input type="text" name="nombre" placeholder="Nombre o apellido" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
                <input type="text" name="email" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">

                <div class="filtro-dropdown">
                    <div class="dropdown-titulo" onclick="toggleDropdown(this)" data-base="Rol">
                        Rol <?= !empty($_GET['rol']) ? '(' . count((array)$_GET['rol']) . ')' : '' ?>
                        <span class="flecha">▼</span>
                    </div>
                    <div class="dropdown-opciones">
                        <?php foreach (['estudiante', 'profesor', 'administrativo'] as $rol): ?>
                            <label>
                                <input type="checkbox" name="rol[]" value="<?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>" <?= in_array($rol, $_GET['rol'] ?? []) ? 'checked' : '' ?>>
                                <?= htmlspecialchars(ucfirst($rol), ENT_QUOTES, 'UTF-8') ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <select name="estado">
                    <option value="">Activación</option>
                    <option value="1" <?= ($_GET['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                    <option value="0" <?= ($_GET['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                </select>

                <div class="filtro-dropdown">
                    <div class="dropdown-titulo" onclick="toggleDropdown(this)" data-base="Aprobación">
                        Aprobación
                        <span class="flecha">▼</span>
                    </div>
                    <div class="dropdown-opciones">
                        <label><input type="checkbox" name="estado_aprobacion[]" value="pendiente" <?= in_array('pendiente', $_GET['estado_aprobacion'] ?? []) ? 'checked' : '' ?>> Pendiente</label>
                        <label><input type="checkbox" name="estado_aprobacion[]" value="aprobado" <?= in_array('aprobado', $_GET['estado_aprobacion'] ?? []) ? 'checked' : '' ?>> Aprobado</label>
                        <label><input type="checkbox" name="estado_aprobacion[]" value="rechazado" <?= in_array('rechazado', $_GET['estado_aprobacion'] ?? []) ? 'checked' : '' ?>> Rechazado</label>
                    </div>
                </div>

                <div class="botones-filtro">

                    <button type="submit">Filtrar</button>
                    <a href="index.php?action=admin-panel&seccion=usuarios" class="btn-limpiar">Limpiar</a>
                </div>



            </form>
        </section>

        <hr>

        <nav class="paginacion-superior-wrapper">
            <div class="paginacion-superior-inner">

                <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'superior') ?>
            </div>

        </nav>

        <section class="tabla-admin">
            <?php if (empty($usuarios)): ?>
                <p>No se encontraron usuarios.</p>
            <?php else: ?>


                <form method="post" action="index.php?action=accion-masiva-usuarios">
                    <?= campoCSRF() ?>
                    <?= generarInputsOcultos($_GET) ?>

                    <div class="acciones-masivas">
                        <div class="acciones-izquierda">
                            <label for="limite">Mostrar:</label>
                            <select id="limite" name="limite" onchange="this.form.submit()">
                                <?php foreach ([10, 20, 50, 100] as $opcion): ?>
                                    <option value="<?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?>" <?= $limite == $opcion ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="acciones-derecha">
                            <select name="accion">
                                <option value="">-- Acción masiva --</option>
                                <option value="aprobar">Aprobar</option>
                                <option value="rechazar">Rechazar</option>
                                <option value="desactivar">Desactivar</option>
                                <option value="cambiar-rol-estudiante">Cambiar rol a Estudiante</option>
                                <option value="cambiar-rol-profesor">Cambiar rol a Profesor</option>
                                <option value="cambiar-rol-administrativo">Cambiar rol a Administrativo</option>
                            </select>
                            <button type="submit">Aplicar</button>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="seleccionar-todos"></th>
                                <th>
                                    <a href="<?= $orden['links']['nombre'] ?>">
                                        Nombre<?= $orden['flechas']['nombre'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-mediana">
                                    <a href="<?= $orden['links']['email'] ?>">
                                        Email<?= $orden['flechas']['email'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-tableta">
                                    <a href="<?= $orden['links']['rol'] ?>">
                                        Rol<?= $orden['flechas']['rol'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-movil">
                                    <a href="<?= $orden['links']['activo'] ?>">
                                        Activo<?= $orden['flechas']['activo'] ?>
                                    </a>
                                </th>
                                <th class="col-oculta-mediana">
                                    <a href="<?= $orden['links']['estado_aprobacion'] ?>">
                                        Aprobación<?= $orden['flechas']['estado_aprobacion'] ?>
                                    </a>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><input type="checkbox" name="seleccionados[]" value="<?= $usuario['id_usuario'] ?>"></td>

                                    <td data-label="Nombre"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                    <td data-label="Email" class="col-oculta-mediana"><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td data-label="Rol" class="col-oculta-tableta">
                                        <select name="nuevo_rol" id="nuevo_rol_<?= $usuario['id_usuario'] ?>"
                                            onchange="cambiarRolUsuario(<?= $usuario['id_usuario'] ?>)">
                                            <?php foreach (['estudiante', 'profesor', 'administrativo'] as $rol): ?>
                                                <option value="<?= $rol ?>" <?= $usuario['rol'] === $rol ? 'selected' : '' ?>>
                                                    <?= ucfirst($rol) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td data-label="Activo" class="col-oculta-movil"><?= $usuario['activo'] ? 'Sí' : 'No' ?></td>

                                    <td data-label="Acciones" class="col-oculta-mediana">
                                        <?php if ($usuario['estado_aprobacion'] === 'pendiente'): ?>
                                            <button type="button" class="btn-aprobar" onclick="actualizarEstadoAprobacionUsuario(<?= $usuario['id_usuario'] ?>, 'aprobado')">Aprobar</button>
                                            <button type="button" class="btn-rechazar" onclick="actualizarEstadoAprobacionUsuario(<?= $usuario['id_usuario'] ?>, 'rechazado')">Rechazar</button>
                                        <?php else: ?>
                                            <?= ucfirst($usuario['estado_aprobacion']) ?>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($usuario['activo']): ?>
                                            <button type="button" class="btn-rechazar" onclick="desactivarUsuario(<?= $usuario['id_usuario'] ?>)">Desactivar</button>
                                        <?php else: ?>
                                            <button type="button" class="btn-aprobar" onclick="reactivarUsuario(<?= $usuario['id_usuario'] ?>)">Reactivar</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <nav class="paginacion-inferior-wrapper">
                        <?= PaginadorHelper::renderizarPaginacion($pagina_actual, $total_paginas, 'inferior') ?>
                    </nav>

                </form>
            <?php endif; ?>
        </section>



    </main>
    <script src="assets/js/admin-filtros.js"></script>
    <script src="assets/js/scroll-y.js?v=<?= time(); ?>"></script>

</body>

</html>