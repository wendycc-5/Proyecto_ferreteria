<?php
$rol_actual = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor';
?>
<div class="sidebar shadow-sm">
    <h6 class="fw-bold text-uppercase text-muted px-2 mb-3 small">Navegación</h6>
    <div class="nav flex-column">
        <a href="menuinicio.php?modulo=inicio" class="nav-link <?php echo ($modulo=='inicio')?'active':''; ?>">Inicio</a>
        <a href="menuinicio.php?modulo=ventas" class="nav-link <?php echo ($modulo=='ventas')?'active':''; ?>">Iniciar Ventas</a>
        
        <!-- Validaciones de permisos -->
        <?php if ($rol_actual == 'Vendedor'): ?>
            <a href="#" class="nav-link" onclick="alertaPermisos('Pedidos WhatsApp')">Pedidos WhatsApp</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Inventario')">Inventario</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Historial de Ventas')">Historial de Ventas</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Reportes Contabilidad')">Reportes Contabilidad</a>
            <a href="#" class="nav-link" onclick="alertaPermisos('Configuracion')">Configuracion</a>
        <?php else: ?>
            <a href="menuinicio.php?modulo=whatsapp" class="nav-link <?php echo ($modulo=='whatsapp')?'active':''; ?>">Pedidos WhatsApp</a>
            <a href="menuinicio.php?modulo=inventario" class="nav-link <?php echo ($modulo=='inventario')?'active':''; ?>">Inventario</a>
            <a href="menuinicio.php?modulo=historial" class="nav-link <?php echo ($modulo=='historial')?'active':''; ?>">Historial de Ventas</a>
            <a href="menuinicio.php?modulo=reportes" class="nav-link <?php echo ($modulo=='reportes')?'active':''; ?>">Reportes Contabilidad</a>
            <a href="menuinicio.php?modulo=configuracion" class="nav-link <?php echo ($modulo=='configuracion')?'active':''; ?>">Configuracion</a>
        <?php endif; ?>
        
        <a href="../login.php" class="nav-link text-danger mt-4">Cerrar Sesion</a>
    </div>
</div>

<script>
function alertaPermisos(moduloNombre) {
    alert("Usuario: <?php echo $rol_actual; ?>\nRequiere de permisos del administrador para continuar con esta solicitud");
}
</script>