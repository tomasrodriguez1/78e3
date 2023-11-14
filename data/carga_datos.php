<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("conexion.php");

$csv_proveedores = file("CSV PAR/proveedores.csv");
foreach ($csv_proveedores as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO proveedores (id, nombre, plataforma) VALUES (:id, :nombre, :plataforma)";

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':plataforma', $linea[2]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

echo "Datos cargados con éxito.";
?>
