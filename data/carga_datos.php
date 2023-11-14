<?php
$csv_proveedores = file("data/CSV PAR/proveedores.csv");
foreach ($csv_proveedores as $linea) {
    $linea = str_getcsv($linea, ",");
    $sql = "INSERT INTO proveedores (id, nombre, plataforma) VALUES ('$linea[0]', '$linea[1]', '$linea[2]')";
}
?>
