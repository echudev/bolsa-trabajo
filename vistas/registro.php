<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

</head>

<body>

    <main class="login-container">
        <section class="login-box">
            <h2>Registro de usuario</h2>

            <?php include 'partials/mensajes.php'; ?>

            <form method="post" action="index.php?action=procesar-registro">
                <?= campoCSRF() ?>

                <div class="campo-formulario">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="campo-formulario">
                    <label for="apellido">Apellido:</label>
                    <input type="text" name="apellido" id="apellido" required>
                </div>

                <div class="campo-formulario">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="campo-formulario">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$" 
                        title="Debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número."
                        required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit">Registrarse</button>
                </div>
            </form>

            <p class="enlace-formulario">
                ¿Ya tenés cuenta? <a href="index.php?action=login">Iniciá sesión</a>.
            </p>
        </section>
    </main>

</body>

</html>