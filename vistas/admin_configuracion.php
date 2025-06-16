<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configuración del sistema</title>
    <link rel="stylesheet" href="assets/css/paginacion.css?v=<?= time(); ?>">

    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

</head>

<body>
    <main class="admin-container">
        <?php include 'vistas/partials/admin_tabs.php'; ?>

        <p><a href="index.php?action=listado" class="volver-link">← Volver a ofertas</a></p>

        <?php include 'partials/mensajes.php'; ?>


        <section class="admin-box">
            <h2>Configuración del sistema</h2>
            <form method="post" action="index.php?action=actualizar-configuracion" class="form-configuracion">
                <div class="config-item">
                    <label>Requiere aprobación de nuevas ofertas</label>
                    <label class="switch">
                        <input type="checkbox" name="aprobar_ofertas" value="true"
                            <?= ($configuraciones['aprobar_ofertas'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="config-item">
                    <label>Requiere aprobación de nuevos usuarios </label>
                    <label class="switch">
                        <input type="checkbox" name="aprobar_registros" value="true"
                            <?= ($configuraciones['aprobar_registros'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="btn-derecha">

                    <button type="submit">Guardar cambios</button>
                </div>

            </form>
        </section>
    </main>
</body>

</html>