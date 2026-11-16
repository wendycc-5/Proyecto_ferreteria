<?php
// Asegurar que las variables existan para evitar warnings
$rol = isset($rol) ? $rol : (isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor');
$nombre_usuario = isset($nombre_usuario) ? $nombre_usuario : (isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'WEBE');
?>
<div class="header-bar">
    <div>
        <span class="badge bg-warning text-dark fw-bold px-3 py-2 fs-6">Rol: <?php echo htmlspecialchars($rol); ?></span>
    </div>
    <div class="header-title">
        SISTEMA DE CONTROL Y REGISTRO - PINTURAS TAPIR
    </div>
    <div class="fw-bold text-white fs-6">
        <?php echo htmlspecialchars($nombre_usuario); ?>
    </div>
</div>