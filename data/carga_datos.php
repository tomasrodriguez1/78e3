<?php
// Establecer la conexión a la base de datos (reemplaza los valores con los de tu configuración)
$host = 'bachman.ing.puc.cl';
$usuario = 'grupo78';
$contrasena = 'grupazo78';
$base_de_datos = 'grupo78e3_par';
$port = 22;

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos, $port);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}



