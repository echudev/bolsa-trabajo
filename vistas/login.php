<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">


    <title>Document</title>
</head>

<body>
    <main class="login-container">
        <section class="login-box">
            <h2>Bolsa de Trabajo</h2>
            <h3 class="inicio-sesion">Iniciar sesión</h3>

            <?php include 'partials/mensajes.php'; ?>
            <form method="post" action="index.php?action=procesar-login">
                <?= campoCSRF() ?>
                <div class="campo-formulario">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="campo-formulario">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit">Ingresar</button>
                </div>
            </form>

            <p class="enlace-formulario">
                ¿No tenés cuenta? <a href="index.php?action=registro">Registrate acá</a>.
            </p>

            <p class="enlace-formulario">
                ¿Olvidaste tu contraseña? <a href="index.php?action=olvido-pass">Recuperar contraseña</a>.
            </p>
        </section>
    </main>
</body>

</html>