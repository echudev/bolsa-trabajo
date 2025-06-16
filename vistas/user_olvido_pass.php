<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">


    <title>Recuperar contraseña</title>
</head>

<body>
    <main class="login-container">
        <section class="login-box">
            <h2>Recuperar contraseña</h2>

            <?php include 'partials/mensajes.php'; ?>
            <form method="post" action="index.php?action=procesar-olvido-pass">
                <?= campoCSRF() ?>
                <div class="campo-formulario">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit">Recuperar contraseña</button>
                </div>
            </form>

            <p class="enlace-formulario">
                <a href="index.php?action=login">Volver al login</a>.
            </p>
        </section>
    </main>
</body>

</html>