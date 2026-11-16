<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "pinturas_tapir";
$port = 3306;

$conexion = new mysqli($host, $user, $password, $database, $port);
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}
$conexion->set_charset("utf8");
?>
