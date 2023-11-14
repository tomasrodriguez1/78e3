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

$csv_genero_subgenero = file("CSV PAR/genero_subgenero.csv");
foreach ($csv_genero_subgenero as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO genero_subgenero (genero, subgenero) VALUES (:genero, :subgenero)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':genero', $linea[0]);
        $stmt->bindParam(':subgenero', $linea[1]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_pago_no_subscripcion = file("CSV PAR/pago_no_subscripcion.csv");
foreach ($csv_pago_no_subscripcion as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO pago_no_subscripcion (pago_id, monto, fecha, id_usuario, preorden, id_proveedor, id_videojuego) VALUES (:pago_id, :monto, :fecha, :id_usuario, :preorden, :id_proveedor, :id_videojuego)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pago_id', $linea[0]);
        $stmt->bindParam(':monto', $linea[1]);
        $stmt->bindParam(':fecha', $linea[2]);
        $stmt->bindParam(':id_usuario', $linea[3]);
        $stmt->bindParam(':preorden', $linea[4]);
        $stmt->bindParam(':id_proveedor', $linea[5]);
        $stmt->bindParam(':id_videojuego', $linea[6]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_pago_subscripcion = file("CSV PAR/pago_subscripcion.csv");
foreach ($csv_pago_subscripcion as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO pago_subscripcion (pago_id, monto, fecha, id_usuario, subs_id) VALUES (:pago_id, :monto, :fecha, :id_usuario, :subs_id)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pago_id', $linea[0]);
        $stmt->bindParam(':monto', $linea[1]);
        $stmt->bindParam(':fecha', $linea[2]);
        $stmt->bindParam(':id_usuario', $linea[3]);
        $stmt->bindParam(':subs_id', $linea[4]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_proveeores_videojuegos_pre = file("CSV PAR/proveedores_videojuegos_pre.csv");
foreach ($csv_proveeores_videojuegos_pre as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO proveedores_videojuegos_pre (id, id_videojuego, precio, precio_preorden) VALUES (:id, :id_videojuego, :precio, :precio_preorden)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':id_videojuego', $linea[1]);
        $stmt->bindParam(':precio', $linea[2]);
        $stmt->bindParam(':precio_preorden', $linea[3]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_proveeores_videojuegos = file("CSV PAR/proveedores_videojuegos.csv");
foreach ($csv_proveeores_videojuegos as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO proveedores_videojuegos (id, nombre, plataforma, id_videojuego, precio) VALUES (:id, :nombre, :plataforma, :id_videojuego, :precio)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':plataforma', $linea[2]);
        $stmt->bindParam(':id_videojuego', $linea[3]);
        $stmt->bindParam(':precio', $linea[4]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
$csv_subscripciones = file("CSV PAR/subscripciones.csv");
foreach ($csv_subscripciones as $linea) {
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO subscripciones (id, estado, fecha_inicio, id_usuario, fecha_termino, id_videojuego, mensualidad) VALUES (:id, :estado, :fecha_inicio, :id_usuario, :fecha_termino, :id_videojuego, :mensualidad)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':estado', $linea[1]);
        $stmt->bindParam(':fecha_inicio', $linea[2]);
        $stmt->bindParam(':id_usuario', $linea[3]);
        $stmt->bindParam(':fecha_termino', $linea[4]);
        $stmt->bindParam(':id_videojuego', $linea[5]);
        $stmt->bindParam(':mensualidad', $linea[6]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_videojuegos = file("CSV PAR/videojuegos.csv");
foreach ($csv_videojuegos as $linea){
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO videojuegos (id_videojuego, titulo, puntuacion,clasificion, fecha_de_lanzamiento) VALUES (:id_videojuego, :titulo, :puntuacion, :clasificion, :fecha_de_lanzamiento)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':titulo', $linea[1]);
        $stmt->bindParam(':puntuacion', $linea[2]);
        $stmt->bindParam(':clasificion', $linea[3]);
        $stmt->bindParam(':fecha_de_lanzamiento', $linea[4]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_videojuego_genero = file("CSV PAR/videojuego_genero.csv");
foreach ($csv_videojuego_genero as $linea){
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO videojuego_genero (id_videojuego, nombre) VALUES (:id_videojuego, :nombre)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$csv_videojuego_mensualidad = file("CSV PAR/videojuego_mensualidad.csv");
foreach ($csv_videojuego_mensualidad as $linea){
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO videojuego_mensualidad (id_videojuego, mensualidad) VALUES (:id_videojuego, :mensualidad)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':mensualidad', $linea[1]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


$csv_videojuego_preorden = file("CSV PAR/videojuego_preorden.csv");
foreach ($csv_videojuego_preorden as $linea){
    $linea = str_getcsv($linea, ";");
    $sql = "INSERT INTO videojuego_preorden (id_videojuego, beneficio_preorden) VALUES (:id_videojuego, :beneficio_preorden)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':beneficio_preorden', $linea[1]);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
echo "Datos cargados con éxito.";
?>
