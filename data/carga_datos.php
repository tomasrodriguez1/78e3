<?php
require("data/conexion.php"); // Asegúrate de que este archivo contiene la conexión a tu base de datos.

$csv_proveedores = file("data/CSV PAR/proveedores.csv");
foreach ($csv_proveedores as $linea) {
    $linea = str_getcsv($linea, ",");
    $sql = "INSERT INTO proveedores (id, nombre, plataforma) VALUES ('$linea[0]', '$linea[1]', '$linea[2]')";

    if (!mysqli_query($conexion, $sql)) {
        echo "Error: " . mysqli_error($conexion);
    }
}

echo "Datos cargados con éxito.";
?>