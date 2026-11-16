<?php
// Incluir la conexión a la base de datos
include __DIR__ . '/../../../conexion.php'; 

// Nombre del archivo a descargar con la fecha y hora actual
$nombre_archivo = 'respaldo_bd_' . date('Y-m-d_H-i-s') . '.sql';

// Configurar encabezados HTTP para forzar la descarga del archivo SQL
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');

// Encabezado del archivo SQL
echo "-- Respaldo de Base de Datos\n";
echo "-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";

// Obtener todas las tablas de la base de datos
$tablas = [];
$result = mysqli_query($conexion, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tablas[] =$row[0];
}

// Recorrer cada tabla para extraer su estructura y sus datos
foreach ($tablas as$tabla) {
    // 1. Estructura de la tabla (CREATE TABLE)
    $create_table = mysqli_query($conexion, "SHOW CREATE TABLE `$tabla`");
    $row_create = mysqli_fetch_row($create_table);
    echo "\n\n-- Estructura de la tabla `$tabla` --\n\n";
    echo "DROP TABLE IF EXISTS `$tabla`;\n";
    echo $row_create[1] . ";\n\n";

    // 2. Datos de la tabla (INSERT INTO)
    $datos = mysqli_query($conexion, "SELECT * FROM `$tabla`");
    $num_campos = mysqli_num_fields($datos);

    while ($row = mysqli_fetch_row($datos)) {
        echo "INSERT INTO `$tabla` VALUES(";
        for ($j = 0; $j < $num_campos; $j++) {
            if (isset($row[$j])) {
                // Escapar caracteres especiales para no romper el SQL
                $val = mysqli_real_escape_string($conexion, $row[$j]);
                echo '"' . $val . '"';
            } else {
                echo 'NULL';
            }
            if ($j < ($num_campos - 1)) {
                echo ',';
            }
        }
        echo ");\n";
    }
}

exit;
?>