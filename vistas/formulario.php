<?php
$titulo_form = $modo === 'editar' ? 'Editar oferta' : 'Nueva oferta';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_form ?> | Bolsa de Trabajo</title>

    <!-- Fuentes y estilos generales -->
    <link rel="stylesheet" href="assets/css/bolsa_trabajo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/formulario_oferta.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="assets/css/mensajes.css?v=<?= time(); ?>">

</head>

<body>
    <section class="formulario-oferta-contenedor">
        <h2 class="formulario-titulo"><?= $titulo_form ?></h2>
        <?php include 'partials/mensajes.php'; ?>

        <?php if (!empty($errores) && is_array($errores)): ?>
            <div class="formulario-error-global">
                <p>Revise los campos marcados y corrija los errores para continuar.</p>
            </div>
        <?php endif; ?>
        <form method="post" action="index.php?action=<?= $modo === 'editar' ? 'editar-oferta' : 'nueva-oferta' ?>" class="formulario-oferta">
            <?= campoCSRF() ?>
            <input type="hidden" name="modo" value="<?= $modo ?>">
            <?php if ($modo === 'editar' && isset($oferta['id_oferta'])): ?>
                <input type="hidden" name="id_oferta" value="<?= htmlspecialchars($oferta['id_oferta']) ?>">
            <?php endif; ?>

            <!-- Información del puesto -->
            <div class="formulario-grupo">
                <label for="puesto">Puesto*:</label>
                <input type="text" name="puesto" id="puesto"
                    maxlength="150" required
                    title="Solo se permiten letras, números, espacios y puntuación básica"
                    value="<?= htmlspecialchars($oferta['puesto'] ?? '') ?>">
                <?php if (!empty($errores['puesto'])): ?>
                    <p class="formulario-error-campo"><?= htmlspecialchars($errores['puesto']) ?></p>
                <?php endif; ?>
            </div>

            <div class="formulario-grupo">
                <label for="empresa">Empresa:</label>
                <input type="text" name="empresa" id="empresa"
                    maxlength="100"
                    title="Solo se permiten letras, números, espacios y símbolos como . , ' & ( ) @ / - !"
                    value="<?= htmlspecialchars($oferta['empresa'] ?? '') ?>"> <?php if (!empty($errores['empresa'])): ?>
                    <p class="formulario-error-campo"><?= htmlspecialchars($errores['empresa']) ?></p>
                <?php endif; ?>

            </div>

            <div class="formulario-grupo">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" name="ubicacion" id="ubicacion"
                    maxlength="150"
                    value="<?= htmlspecialchars($oferta['ubicacion'] ?? '') ?>"> <?php if (!empty($errores['ubicacion'])): ?>
                    <p class="formulario-error-campo"><?= htmlspecialchars($errores['ubicacion']) ?></p>
                <?php endif; ?>
            </div>


            <div class="formulario-grupo">
                <label for="descripcion">Descripción*:</label>

                <div class="editor-container">
                    <div class="editor-toolbar">
                        <button type="button" data-comando="bold"><strong>B</strong></button>
                        <button type="button" data-comando="italic"><em>I</em></button>
                        <button type="button" data-comando="underline"><u>U</u></button>
                        <button type="button" data-comando="insertUnorderedList">• Lista</button>
                    </div>

                    <div id="editor" contenteditable="true" class="editor-contenido">
                        <?= $oferta['descripcion'] ?? '' ?>
                    </div>
                </div>

                <textarea name="descripcion" id="descripcion" hidden maxlength="10000"></textarea>

                <?php if (!empty($errores['descripcion'])): ?>
                    <p class="formulario-error-campo"><?= htmlspecialchars($errores['descripcion']) ?></p>
                <?php endif; ?>
            </div>


            <!-- Ubicación y condiciones -->

            <div class="formulario-fila-doble">
                <div class="formulario-grupo">
                    <label for="modalidad">Modalidad:</label>
                    <select name="modalidad" id="modalidad">
                        <?php
                        $modalidad_actual = $oferta['modalidad'] ?? 'presencial';
                        foreach (['presencial', 'remoto', 'híbrido'] as $opcion):
                        ?>
                            <option value="<?= $opcion ?>" <?= $modalidad_actual === $opcion ? 'selected' : '' ?>>
                                <?= ucfirst($opcion) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errores['modalidad'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['modalidad']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="formulario-grupo">
                    <label for="jornada">Jornada:</label>
                    <select name="jornada" id="jornada">
                        <?php
                        $jornada_actual = $oferta['jornada'] ?? '';
                        foreach (['completa' => 'Completa', 'parcial' => 'Parcial'] as $valor => $texto):
                        ?>
                            <option value="<?= $valor ?>" <?= $jornada_actual === $valor ? 'selected' : '' ?>>
                                <?= $texto ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="" <?= $jornada_actual === null || $jornada_actual === '' ? 'selected' : '' ?>>No especificada</option>
                    </select>
                    <?php if (!empty($errores['jornada'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['jornada']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-fila-doble">
                <div class="formulario-grupo">
                    <label for="horario">Horario:</label>
                    <input type="text" name="horario" id="horario"
                         maxlength="100"
                        value="<?= htmlspecialchars($oferta['horario'] ?? '') ?>"> <?php if (!empty($errores['horario'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['horario']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="formulario-grupo">
                    <label for="experiencia_requerida">Experiencia requerida (años):</label>
                    <input type="number" name="experiencia_requerida" id="experiencia_requerida"
                        min="0" max="20"
                        value="<?= htmlspecialchars($oferta['experiencia_requerida'] ?? '') ?>"> <?php if (!empty($errores['experiencia_requerida'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['experiencia_requerida']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contacto -->
            <div class="formulario-fila-doble">
                <div class="formulario-grupo">
                    <label for="email_contacto">Email de contacto:</label>
                    <input type="email" name="email_contacto" id="email_contacto" value="<?= htmlspecialchars($oferta['email_contacto'] ?? '') ?>">
                    <?php if (!empty($errores['email_contacto'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['email_contacto']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="formulario-grupo">
                    <label for="telefono_contacto">Teléfono de contacto:</label>
                    <input type="tel" name="telefono_contacto" id="telefono_contacto"
                        pattern="(\+54\s?)?(\(?\d{2,4}\)?[\s\-]?)?[\d\s\-]{6,12}" minlength="8" maxlength="20"
                        title="Formato: +54 11 1234-5678 o 11-1234-5678 o 1234567890"
                        placeholder="+54 11 1234-5678 / 11-1234-5678 / 1234567890"
                        value="<?= htmlspecialchars($oferta['telefono_contacto'] ?? '') ?>"> <?php if (!empty($errores['telefono_contacto'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['telefono_contacto']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="formulario-grupo">
                <label for="enlace">Enlace externo:</label>
                <input type="url" name="enlace" id="enlace" value="<?= htmlspecialchars($oferta['enlace'] ?? '') ?>">
                <?php if (!empty($errores['enlace'])): ?>
                    <p class="formulario-error-campo"><?= htmlspecialchars($errores['enlace']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Vigencia -->
            <div class="formulario-fila-doble">
                <div class="formulario-grupo">
                    <label for="fecha_fin">Fecha de finalización:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($oferta['fecha_fin'] ?? '') ?>">
                    <?php if (!empty($errores['fecha_fin'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['fecha_fin']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="formulario-grupo">
                    <label style="visibility: hidden;">Oferta abierta</label>
                    <div class="formulario-checkbox-campo">
                        <input type="checkbox" name="activa" id="activa" <?= !isset($oferta['activa']) || $oferta['activa'] ? 'checked' : '' ?>>
                        <span>Oferta abierta</span>
                    </div>
                    <?php if (!empty($errores['activa'])): ?>
                        <p class="formulario-error-campo"><?= htmlspecialchars($errores['activa']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Botones -->

            <div class="formulario-botones">
                <a href="index.php?action=listado" class="hero-btn-secondary">Cancelar</a>
                <button type="submit" class="hero-btn-primary">Guardar</button>
            </div>
        </form>
    </section>

    <script src="assets/js/editor-descripcion.js?v=<?= time(); ?>"></script>

    <script>
        /**
         * Este script desactiva el checkbox "activa" si la fecha de finalización ingresada es anterior a hoy.
         * También impide que se vuelva a activar mientras la fecha sea inválida y muestra un mensaje explicativo.
         */
        document.addEventListener("DOMContentLoaded", function() {
            const fechaFinInput = document.getElementById("fecha_fin");
            const checkboxActiva = document.getElementById("activa");

            function esFechaAnteriorAHoy(fechaStr) {
                if (!fechaStr) return false;
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0); // para evitar errores por horas
                const fecha = new Date(fechaStr);
                return fecha < hoy;
            }

            function validarCheckbox() {
                const fecha = fechaFinInput.value;
                if (esFechaAnteriorAHoy(fecha)) {
                    checkboxActiva.checked = false;
                    checkboxActiva.disabled = true;
                    checkboxActiva.title = "No se puede activar la oferta si la fecha de finalización es anterior a hoy.";
                } else {
                    checkboxActiva.disabled = false;
                    checkboxActiva.title = "";
                }
            }

            // Al cargar la página
            validarCheckbox();

            // Cuando cambia la fecha
            fechaFinInput.addEventListener("change", validarCheckbox);
        });
    </script>

    <script src="assets/js/volver.js?v=<?= time(); ?>"></script>


</body>

</html>