<?php
$rol_actual = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor';
?>
<div class="sidebar shadow-sm">
    <h6 class="fw-bold text-uppercase text-muted px-2 mb-3 small">Navegación</h6>
    <div class="nav flex-column">
        <a href="menuinicio.php?modulo=inicio" class="nav-link <?php echo ($modulo=='inicio')?'active':''; ?>">Inicio</a>
        <a href="menuinicio.php?modulo=ventas" class="nav-link <?php echo ($modulo=='ventas')?'active':''; ?>">Iniciar Ventas</a>

        <?php if ($rol_actual == 'Vendedor'): ?>
            <!-- El Vendedor estándar tiene restringidos varios módulos -->
            <a href="#" class="nav-link" onclick="alertaPermisos('Pedidos WhatsApp')">Pedidos WhatsApp</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Inventario')">Inventario</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Historial de Ventas')">Historial de Ventas</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Reportes Contabilidad')">Reportes Contabilidad</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Configuración')">Configuración</a>

        <?php elseif ($rol_actual == 'Vendedor Pro'): ?>
            <!-- El Vendedor Pro puede ver Inventario y WhatsApp, pero no contabilidad ni configuración -->
            <a href="menuinicio.php?modulo=whatsapp" class="nav-link <?php echo ($modulo=='whatsapp')?'active':''; ?>">Pedidos WhatsApp</a>
            <a href="menuinicio.php?modulo=inventario" class="nav-link <?php echo ($modulo=='inventario')?'active':''; ?>">Inventario</a>
            <a href="menuinicio.php?modulo=historial" class="nav-link <?php echo ($modulo=='historial')?'active':''; ?>">Historial de Ventas</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Reportes Contabilidad')">Reportes Contabilidad</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Configuración')">Configuración</a>

        <?php elseif ($rol_actual == 'Contabilidad'): ?>
            <!-- Contabilidad ve reportes e historial, pero no ventas de mostrador directas o inventario operativo -->
            <a href="#" class="nav-link" onclick="alertaPermisos('Pedidos WhatsApp')">Pedidos WhatsApp</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Inventario')">Inventario</a>
            <a href="menuinicio.php?modulo=historial" class="nav-link <?php echo ($modulo=='historial')?'active':''; ?>">Historial de Ventas</a>
            <a href="menuinicio.php?modulo=reportes" class="nav-link <?php echo ($modulo=='reportes')?'active':''; ?>">Reportes Contabilidad</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Configuración')">Configuración</a>

        <?php else: ?>
            <!-- Administrador o cualquier otro rol con acceso total -->
            <a href="menuinicio.php?modulo=whatsapp" class="nav-link <?php echo ($modulo=='whatsapp')?'active':''; ?>">Pedidos WhatsApp</a>
            <a href="menuinicio.php?modulo=inventario" class="nav-link <?php echo ($modulo=='inventario')?'active':''; ?>">Inventario</a>
            <a href="menuinicio.php?modulo=historial" class="nav-link <?php echo ($modulo=='historial')?'active':''; ?>">Historial de Ventas</a>
            <a href="menuinicio.php?modulo=reportes" class="nav-link <?php echo ($modulo=='reportes')?'active':''; ?>">Reportes Contabilidad</a>
            <a href="menuinicio.php?modulo=configuracion" class="nav-link <?php echo ($modulo=='configuracion')?'active':''; ?>">Configuración</a>
        <?php endif; ?>

        <a href="../login.php" class="nav-link text-danger mt-4">Cerrar Sesión</a>
    </div>
</div>

<script>
function alertaPermisos(moduloNombre) {
    alert("Usuario con rol: <?php echo $rol_actual; ?>\nRequiere permisos superiores para acceder a: " + moduloNombre);
}
</script>