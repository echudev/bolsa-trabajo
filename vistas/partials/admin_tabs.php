<?php
$seccion_actual = $_GET['seccion'] ?? 'usuarios';
?>
<div class="admin-tabs-wrapper">

    <nav class="admin-tabs">
        <a href="index.php?action=admin-panel&seccion=usuarios" class="<?= $seccion_actual === 'usuarios' ? 'activo' : '' ?>">Usuarios</a>
        <a href="index.php?action=admin-panel&seccion=ofertas" class="<?= $seccion_actual === 'ofertas' ? 'activo' : '' ?>">Ofertas</a>
        <a href="index.php?action=admin-panel&seccion=configuracion" class="<?= $seccion_actual === 'configuracion' ? 'activo' : '' ?>">Configuración</a>
    </nav>
</div>