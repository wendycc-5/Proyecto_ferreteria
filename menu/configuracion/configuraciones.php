<?php
// conexión con la base de datos
if (!isset($conexion)) {
    include "../conexion.php";
}

$tab = $_GET['tab'] ?? 'usuarios';
$submodulo = $_GET['submodulo'] ?? 'menu';
?>

<style>
    .card-config {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        max-width: 900px;
        margin: 15px auto;
    }

    .sidebar-config {
        background-color: #eef2f5;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sidebar-config .nav-link {
        color: #6c757d;
        font-weight: 600;
        padding: 14px 18px;
        border-bottom: 1px solid #e0e6ed;
        border-radius: 0;
    }

    .sidebar-config .nav-link.active {
        background-color: #5c7083 !important;
        color: #fff !important;
    }

    .btn-custom-blue {
        background-color: #8faec4 !important;
        border-color: #8faec4 !important;
        color: #ffffff !important;
        border-radius: 25px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .btn-custom-blue:hover {
        background-color: #7a9bb2 !important;
        border-color: #7a9bb2 !important;
    }
</style>

<div class="card card-config">
    <div class="row g-0">
        
        <!-- SUBMENU LATERAL -->
        <div class="col-md-3 sidebar-config">
            <div class="nav flex-column nav-pills">
                <a class="nav-link <?php echo ($tab === 'usuarios') ? 'active' : ''; ?>" href="menuinicio.php?modulo=configuracion&tab=usuarios">
                    Usuarios y Personal
                </a>
                <a class="nav-link <?php echo ($tab === 'ajustes') ? 'active' : ''; ?>" href="menuinicio.php?modulo=configuracion&tab=ajustes">
                    Ventas e Inventario
                </a>
                <a class="nav-link <?php echo ($tab === 'empresa') ? 'active' : ''; ?>" href="menuinicio.php?modulo=configuracion&tab=empresa">
                    Respaldo
                </a>
            </div>
        </div>

        <!-- ÁREA DINÁMICA -->
        <div class="col-md-9 p-4 d-flex flex-column justify-content-center align-items-center" style="min-height: 380px;">
            <?php
// Capturamos la pestaña y submódulo actual (con valores por defecto por si no vienen en la URL)
$tab = $_GET['tab'] ?? 'empresa';
$submodulo = $_GET['submodulo'] ?? 'menuus';

if ($tab === 'usuarios') {

    if ($submodulo === 'personal') {
        include 'usuarios/personal.php';
    } elseif ($submodulo === 'roles') {
        include 'usuarios/roles.php';
    } elseif ($submodulo === 'sesion') {
        include 'usuarios/sesion.php';
    } else {
        include 'usuarios/menuus.php';
    }

}elseif ($tab === 'empresa') {

    // Aseguramos la ruta directa a empresa.php
    $ruta_empresa = __DIR__ . '/empresa/empresa.php';

    if (file_exists($ruta_empresa)) {
        include $ruta_empresa;
    } else {
        echo "<p style='color:red; text-align:center;'>No se encontró el archivo en: " . $ruta_empresa . "</p>";
    }
} elseif ($tab === 'ajustes') {

    $ruta_ajustes = __DIR__ . '/ajustes/ajustes.php';
    if (file_exists($ruta_ajustes)) {
        include $ruta_ajustes;
    }

} else {
    // Pestaña por defecto si el valor de $tab no coincide con ninguno
    echo "<div class='text-center text-muted'><p>Selecciona una opción del menú de configuración.</p></div>";
}
?>
        </div>

    </div>
</div>