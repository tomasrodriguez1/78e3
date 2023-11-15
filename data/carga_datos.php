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
?>
