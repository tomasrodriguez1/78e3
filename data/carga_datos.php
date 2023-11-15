<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("conexion.php");

function verificarCampos($linea, $indicesCampos) {
    foreach ($indicesCampos as $indice) {
        if (!isset($linea[$indice]) || trim($linea[$indice]) === '') {
            return false;
        }
    }
    return true;
}

## TABLA PROVEEDORES
try {
    $db->beginTransaction();
    $csv_proveedores = file("CSV PAR/proveedores.csv");
    foreach ($csv_proveedores as $index => $linea) {
        if ($index === 0) continue;

        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2])) {
            continue;
        }
        $idLimpio = filter_var(trim($linea[0]), FILTER_SANITIZE_NUMBER_INT);
        $sqlVerificar = "SELECT COUNT(*) FROM proveedores WHERE id = :id OR nombre = :nombre";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id', $idLimpio, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':nombre', $linea[1]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO proveedores (id, nombre, plataforma) VALUES (:id, :nombre, :plataforma)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $idLimpio, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':plataforma', $linea[2]);
        $stmt->execute();
        echo "Datos cargados proveedores\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de proveedores: " . $e->getMessage();
}

## TABLA GENERO - SUBGENERO
try {
    $db->beginTransaction();
    $csv_genero_subgenero = file("CSV PAR/genero.csv");
    foreach ($csv_genero_subgenero as $linea) {
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM genero_subgenero WHERE genero = :genero AND subgenero = :subgenero";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':genero', $linea[0]);
        $stmtVerificar->bindParam(':subgenero', $linea[1]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO genero_subgenero (genero, subgenero) VALUES (:genero, :subgenero)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':genero', $linea[0]);
        $stmt->bindParam(':subgenero', $linea[1]);
        $stmt->execute();
        echo "Datos cargados genero_subgenero\n";
    }

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de genero_subgenero: " . $e->getMessage();
}

## TABLA VIDEOJUEGOS
try {
    $db->beginTransaction();
    $csv_videojuegos = file("CSV PAR/videojuego.csv");
    foreach ($csv_videojuegos as $linea) {
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2, 3, 4])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM videojuegos WHERE id_videojuego = :id_videojuego" ; # Porque esta linea
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_videojuego', $linea[0]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO videojuegos (id_videojuego, titulo, puntuacion, clasificacion, fecha_de_lanzamiento) VALUES (:id_videojuego, :titulo, :puntuacion, :clasificacion, :fecha_de_lanzamiento)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':titulo', $linea[1]);
        $stmt->bindParam(':puntuacion', $linea[2]);
        $stmt->bindParam(':clasificacion', $linea[3]);
        $stmt->bindParam(':fecha_de_lanzamiento', $linea[4]);
        $stmt->execute();
        echo "Datos cargados videojuegos\n";

    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de videojuegos: " . $e->getMessage();
}

## TABLA VIDEOJUEGOS_GENERO
try {
    $db->beginTransaction();
    $csv_videojuegos_genero = file("CSV PAR/videojuego_genero.csv");
    foreach ($csv_videojuegos_genero as $linea) {
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM videojuegos_genero WHERE id_videojuego = :id_videojuego AND nombre = :nombre"; # No permite los duplicados si coinciden en id y genero?
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_videojuego', $linea[0]);
        $stmtVerificar->bindParam(':nombre', $linea[1]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO videojuego_genero (id_videojuego, nombre) VALUES (:id_videojuego, :nombre)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->execute();
        echo "Datos cargados videojuegos_genero\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de videojuegos_genero: " . $e->getMessage();
}

?>
