<?php
include("../conexion.php");
session_start();

$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor';
$nombre_usuario = 'WEBE';
$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'ventas';

// Leer los datos JSON que envía JavaScript correctamente
$input_raw = file_get_contents("php://input");
$datos_json = json_decode($input_raw, true);

// API para procesar venta y restar stock en base de datos
if (isset($datos_json['accion_procesar_venta']) && $datos_json['accion_procesar_venta'] === true) {
    header('Content-Type: application/json');
    
    if (!empty($datos_json['carrito'])) {
        $conexion->begin_transaction();
        try {
            foreach ($datos_json['carrito'] as $item) {
                // Limpiar el nombre del producto por si trae etiquetas HTML (como los badges de descuento)
                $nombreProd = trim($conexion->real_escape_string(strip_tags($item['nombre'])));
                $cantidadVendida = intval($item['cantidad']);
                
                // Sentencia SQL para restar el stock asegurando que no quede negativo y coincida con el nombre
                $sqlStock = "UPDATE producto SET stock = stock - $cantidadVendida WHERE nom_produc LIKE '%$nombreProd%' AND stock >= $cantidadVendida";
                $conexion->query($sqlStock);
                
                if ($conexion->affected_rows === 0) {
                    throw new Exception("Stock insuficiente o producto no encontrado: " . $nombreProd);
                }
            }
            $conexion->commit();
            echo json_encode(["exito" => true]);
        } catch (Exception $e) {
            $conexion->rollback();
            echo json_encode(["exito" => false, "mensaje" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["exito" => false, "mensaje" => "Carrito vacío"]);
    }
    exit();
}

// API de búsqueda flexible de productos
if (isset($_GET['api_buscar_producto'])) {
    header('Content-Type: application/json');
    $busqueda = trim($conexion->real_escape_string($_GET['api_buscar_producto']));
    
    $query = "SELECT id_producto, nom_produc, precio, stock FROM producto 
              WHERE id_producto LIKE '%$busqueda%' 
                 OR nom_produc LIKE '%$busqueda%' 
              LIMIT 1";
              
    $res = $conexion->query($query);
    
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo json_encode([
            "encontrado" => true,
            "codigo" => $row['id_producto'],
            "nombre" => $row['nom_produc'],
            "precio" => floatval($row['precio']),
            "stock" => intval($row['stock'])
        ]);
    } else {
        echo json_encode(["encontrado" => false]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA DE CONTROL Y REGISTRO - PINTURAS TAPIR</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .header-bar { background-color: #1a365d; color: #ffffff; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #0d6efd; }
        .main-layout { display: flex; flex: 1; }
        .sidebar { width: 230px; background-color: #ffffff; padding: 20px 12px; border-right: 1px solid #e0e0e0; flex-shrink: 0; }
        .sidebar .nav-link { color: #495057; font-weight: 600; padding: 10px 14px; border-radius: 6px; margin-bottom: 6px; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #1a365d; color: #ffffff !important; }
        .content-area { flex: 1; padding: 25px; }
        .footer-bar { background-color: #1a365d; color: #ffffff; text-align: center; padding: 10px; font-size: 0.85rem; margin-top: auto; }
        .pos-card { border-radius: 8px; border: 1px solid #dee2e6; }
        .badge-cajero { background-color: #6c757d; font-size: 0.85rem; padding: 6px 12px; border-radius: 4px; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <?php include("encabezado.php"); ?>

    <div class="main-layout">
        <!-- Menú de navegación -->
        <?php include("menu.php"); ?>

      <!-- Contenido modular dinámico -->
        <div class="content-area">
            <?php 
            if ($modulo == 'ventas') {
                include("ventas.php");
            } elseif ($modulo == 'inventario') {
                include("../inventario/inventario.php");
            } elseif ($modulo == 'inicio') {
                include("pantalla_inicio/inicio.php");
            } else {
                echo "<div class='card p-4 shadow-sm bg-white'><h5>Módulo " . ucfirst($modulo) . "</h5><p class='text-muted mb-0'>Sección activa en el sistema.</p></div>";
            }
            ?>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer-bar">
        © 2026 - Sistema de Información | WEBE -- Instituto Tecnológico de Tizimín
    </div>

    <!-- Script general -->
    <script src="script.js"></script>
</body>
</html>