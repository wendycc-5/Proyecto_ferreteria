<?php
include("../conexion.php");
session_start();

$rol_actual = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Vendedor';
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'WEBE';
$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'inicio';

// Bloqueo de seguridad por URL según el rol
if ($rol_actual == 'Vendedor' && in_array($modulo, ['whatsapp', 'inventario', 'historial', 'reportes', 'configuracion'])) {
    $modulo = 'inicio'; 
} elseif ($rol_actual == 'Vendedor Pro' && in_array($modulo, ['reportes', 'configuracion'])) {
    $modulo = 'inicio';
} elseif ($rol_actual == 'Contabilidad' && in_array($modulo, ['ventas', 'whatsapp', 'inventario', 'configuracion'])) {
    $modulo = 'inicio';
}

// Leer los datos JSON que envía JavaScript correctamente
$input_raw = file_get_contents("php://input");
$datos_json = json_decode($input_raw, true);

// API para procesar venta, registrar en BD, detalle y restar stock
if (isset($datos_json['accion_procesar_venta']) && $datos_json['accion_procesar_venta'] === true) {
    header('Content-Type: application/json');
    
    if (!empty($datos_json['carrito'])) {
        $conexion->begin_transaction();
        try {
            // 1. Calcular el total de la venta
            $totalVenta = 0;
            foreach ($datos_json['carrito'] as $item) {
                $precioItem = floatval(isset($item['precio']) ? $item['precio'] : (isset($item['price']) ? $item['price'] : 0));
                $cantItem = intval(isset($item['cantidad']) ? $item['cantidad'] : (isset($item['qty']) ? $item['qty'] : 1));
                $totalVenta += $precioItem * $cantItem;
            }

            // 2. Insertar la cabecera en la tabla 'venta'
            $sqlVenta = "INSERT INTO venta (fecha, total) VALUES (NOW(), $totalVenta)";
            if (!$conexion->query($sqlVenta)) {
                throw new Exception("Error al registrar la venta principal: " . $conexion->error);
            }
            $idVentaGenerada = $conexion->insert_id;

            // 3. Recorrer el carrito para guardar el detalle y restar stock
            foreach ($datos_json['carrito'] as $item) {
                // Capturar datos admitiendo múltiples variantes de nombres enviadas por JS
                $codigoProd = isset($item['codigo']) ? trim($item['codigo']) : (isset($item['id']) ? trim($item['id']) : '');
                $nombreProd = trim(strip_tags(isset($item['nombre']) ? $item['nombre'] : (isset($item['name']) ? $item['name'] : '')));
                $cantidadVendida = intval(isset($item['cantidad']) ? $item['cantidad'] : (isset($item['qty']) ? $item['qty'] : 1));
                $precioUnitario = floatval(isset($item['precio']) ? $item['precio'] : (isset($item['price']) ? $item['price'] : 0));
                
                $subtotal = $cantidadVendida * $precioUnitario;

                // Encontrar el ID real en la base de datos de manera flexible
                $idProdReal = '';
                if (!empty($codigoProd)) {
                    // Verificar si el código existe directamente
                    $resCheck = $conexion->query("SELECT id_producto FROM producto WHERE id_producto = '" . $conexion->real_escape_string($codigoProd) . "' LIMIT 1");
                    if ($resCheck && $resCheck->num_rows > 0) {
                        $idProdReal = $codigoProd;
                    }
                }
                
                if (empty($idProdReal) && !empty($nombreProd)) {
                    $resProd = $conexion->query("SELECT id_producto FROM producto WHERE nom_produc LIKE '%" . $conexion->real_escape_string($nombreProd) . "%' LIMIT 1");
                    if ($resProd && $resProd->num_rows > 0) {
                        $rowP = $resProd->fetch_assoc();
                        $idProdReal = $rowP['id_producto'];
                    }
                }

                if (empty($idProdReal)) {
                    throw new Exception("Producto no encontrado o identificador inválido en la BD: " . ($nombreProd ?: $codigoProd));
                }

                $idProdRealEscapado = $conexion->real_escape_string($idProdReal);

                // A. Insertar en la tabla detalle_venta
                $sqlDetalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, p_unitario, subtotal) 
                               VALUES ($idVentaGenerada, '$idProdRealEscapado', $cantidadVendida, $precioUnitario, $subtotal)";
                if (!$conexion->query($sqlDetalle)) {
                    throw new Exception("Error al guardar el detalle de venta: " . $conexion->error);
                }

                // B. Restar el stock asegurando que no quede por debajo de 5 unidades
                $sqlStock = "UPDATE producto SET stock = stock - $cantidadVendida WHERE id_producto = '$idProdRealEscapado' AND (stock - $cantidadVendida) >= 5";
                $conexion->query($sqlStock);
                
                if ($conexion->affected_rows === 0) {
                    throw new Exception("Stock insuficiente o el stock no puede quedar por debajo de 5 unidades en: " . ($nombreProd ?: $idProdReal));
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
        <!-- Menú de navegación dinámico según rol -->
        <?php include("menu.php"); ?>

      <!-- Contenido modular dinámico -->
        <div class="content-area">
            <?php 
            if ($modulo == 'inicio') {
                include("pantalla_inicio/inicio.php");
            } elseif ($modulo == 'ventas') {
                include("historialventas/ventas.php");
            } elseif ($modulo == 'whatsapp'){
                include("conexiónWhatsApp/WhatsApp.html");
            } elseif ($modulo == 'inventario') {
                include("inventario/inventario.php");
            } elseif ($modulo == 'historial'){
                include("historialventas/historialventa.php");
            } elseif ($modulo == 'reportes'){
                include("reporteconta/reporte.php");
            } elseif ($modulo == 'configuracion') {
                include("configuracion/configuraciones.php");
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
    <script src="../js/script.js"></script>
</body>
</html>