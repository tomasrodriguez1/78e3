<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("conexion.php");

function eliminarBOM($valor) {
    if (substr($valor, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $valor = substr($valor, 3);
    }
    return $valor;
}

try {
    // Iniciar transacción para proveedores
    $db->beginTransaction();

    $csv_proveedores = file("CSV PAR/proveedores.csv");
    foreach ($csv_proveedores as $index => $linea) {
        // Saltar el encabezado
        if ($index === 0) {
            continue;
        }

        $linea = str_getcsv($linea, ";");
        $idLimpio = filter_var(trim($linea[0]), FILTER_SANITIZE_NUMBER_INT);
        if (!is_numeric($idLimpio)) {
            throw new Exception("El valor de ID no es un número válido: " . $linea[0]);
        }

        // Verificar si el proveedor ya existe
        $sqlVerificar = "SELECT COUNT(*) FROM proveedores WHERE id = :id OR nombre = :nombre";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id', $idLimpio, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':nombre', $linea[1]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            echo "Proveedor ya cargado: " . $linea[1] . "\n";
            continue;
        }

        // Insertar el proveedor si no existe
        $sql = "INSERT INTO proveedores (id, nombre, plataforma) VALUES (:id, :nombre, :plataforma)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $idLimpio, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':plataforma', $linea[2]);
        $stmt->execute();
        echo "Datos cargados proveedores\n";
    }

    // Confirmar la transacción para proveedores
    $db->commit();
} catch (Exception $e) {
    // Revertir la transacción si hay un error
    $db->rollBack();
    echo "Error durante la carga de datos de proveedores: " . $e->getMessage();
}

try {
    // Iniciar transacción para genero_subgenero
    $db->beginTransaction();

    $csv_genero_subgenero = file("CSV PAR/genero_subgenero.csv");
    foreach ($csv_genero_subgenero as $linea) {
        $linea = str_getcsv($linea, ";");

        // Verificar si el genero_subgenero ya existe
        $sqlVerificar = "SELECT COUNT(*) FROM genero_subgenero WHERE genero = :genero AND subgenero = :subgenero";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':genero', $linea[0]);
        $stmtVerificar->bindParam(':subgenero', $linea[1]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            echo "Genero y subgenero ya cargados: " . $linea[0] . ", " . $linea[1] . "\n";
            continue;
        }

        // Insertar genero_subgenero si no existe
        $sql = "INSERT INTO genero_subgenero (genero, subgenero) VALUES (:genero, :subgenero)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':genero', $linea[0]);
        $stmt->bindParam(':subgenero', $linea[1]);
        $stmt->execute();
        echo "Datos cargados genero_subgenero\n";
    }

    // Confirmar la transacción para genero_subgenero
    $db->commit();
} catch (Exception $e) {
    // Revertir la transacción si hay un error
    $db->rollBack();
    echo "Error durante la carga de datos de genero_subgenero: " . $e->getMessage();
}

?>
