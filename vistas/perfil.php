<?php
// vistas/perfil.php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

    <title><?= htmlspecialchars($datosVista['titulo'] ?? 'Mi Perfil') ?></title>
</head>

<body>
    <main class="login-container">
        <section class="login-box">
            <h2><?= htmlspecialchars($datosVista['titulo'] ?? 'Editar mi perfil') ?></h2>

            <?php include 'partials/mensajes.php'; ?>

            <form method="post" action="index.php?action=procesar-perfil">
                <?= campoCSRF() ?>
                <div class="campo-formulario">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        required
                        maxlength="50"
                        value="<?= htmlspecialchars($datosVista['usuario']['nombre'] ?? '') ?>"
                    >
                </div>

                <div class="campo-formulario">
                    <label for="apellido">Apellido</label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($datosVista['usuario']['apellido'] ?? '') ?>"
                    >
                </div>

                <div class="campo-formulario">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars($datosVista['usuario']['email'] ?? '') ?>"
                    >
                </div>



                <div class="campo-formulario">
                    <label for="password_actual">Contraseña actual</label>
                    <input
                        type="password"
                        id="password_actual"
                        name="password_actual"
                        required
                    >
                </div>

                <div class="campo-formulario">
                    <label for="nueva_password">Nueva contraseña (opcional)</label>
                    <input
                        type="password"
                        id="nueva_password"
                        name="nueva_password"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                        title="Debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número."
                    >
                </div>

                <div class="campo-formulario">
                    <label for="repite_password">Repetir nueva contraseña</label>
                    <input
                        type="password"
                        id="repite_password"
                        name="repite_password"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                        title="Debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número."
                    >
                </div>

                <div class="acciones-formulario">
                    <button type="submit">Guardar cambios</button>
                </div>
            </form>

            <p class="enlace-formulario">
                <a href="index.php?action=listado">← Volver al listado</a>
            </p>
        </section>
    </main>
</body>

</html>
