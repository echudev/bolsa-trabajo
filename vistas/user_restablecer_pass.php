<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">


    <title>Restablecer contraseña</title>
</head>

<body>
    <main class="login-container">
        <section class="login-box">
            <h2>Restablecer contraseña</h2>

            <?php include 'partials/mensajes.php'; ?>
            <form method="post" action="index.php?action=procesar-restablecer-pass">
                <?= campoCSRF() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                <div class="campo-formulario">
                    <label for="nueva_password">Nueva contraseña</label>
                    <input
                        type="password"
                        id="nueva_password"
                        name="nueva_password" 
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                        title="Debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número."
                        required
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
                        required
                        >
                </div>

                <div class="acciones-formulario">
                    <button type="submit">Restablecer</button>
                </div>
            </form>

            <p class="enlace-formulario">
                ¿Volver al login? <a href="index.php?action=login">Volver al login acá</a>.
            </p>
        </section>
    </main>
</body>

</html>