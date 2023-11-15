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
    foreach ($csv_genero_subgenero as $index => $linea) {

        if ($index === 0) continue;
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
    foreach ($csv_videojuegos as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2, 3, 4])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM videojuegos WHERE id_videojuego = :id_videojuego" ; 
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
        $fecha_de_lanzamiento = date_format(date_create_from_format('d-m-y', $linea[4]), 'Y-m-d');
        $stmt->bindParam(':fecha_de_lanzamiento', $fecha_de_lanzamiento);
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
    $csv_videojuego_genero = file("CSV PAR/videojuego_genero.csv");
    foreach ($csv_videojuego_genero as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM videojuego_genero WHERE id_videojuego = :id_videojuego AND genero = :genero"; 
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_videojuego', $linea[0]);
        $stmtVerificar->bindParam(':genero', $linea[1]);
        $stmtVerificar->execute();
        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO videojuego_genero (id_videojuego, genero) VALUES (:id_videojuego, :genero)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $linea[0]);
        $stmt->bindParam(':genero', $linea[1]);
        $stmt->execute();
        echo "Datos cargados videojuego_genero\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de videojuego_genero: " . $e->getMessage();
}


## TABLA PROVEEDORES VIDEOJUEGOS
try {
    $db->beginTransaction();
    $csv_proveedores_videojuegos = file("CSV PAR/proveedores.csv");
    foreach ($csv_proveedores_videojuegos as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 3, 4])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM proveedores_videojuegos WHERE id = :id AND id_videojuego = :id_videojuego"; 
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id', $linea[0]);
        $stmtVerificar->bindParam(':id_videojuego', $linea[3]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO proveedores_videojuegos (id, id_videojuego, precio) VALUES (:id, :id_videojuego, :precio)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':id_videojuego', $linea[3]);
        $stmt->bindParam(':precio', $linea[4]);
        $stmt->execute();
        echo "Datos cargados proveedores_videojuegos\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de proveedores_videojuegos: " . $e->getMessage();
}  

## TABLA PROVEEDORES VIDEOJUEGOS PRE
try {
    $db->beginTransaction();
    $csv_proveedores_videojuegos = file("CSV PAR/proveedores.csv");
    foreach ($csv_proveedores_videojuegos as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 3, 5])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM proveedores_videojuegos_pre WHERE id = :id AND id_videojuego = :id_videojuego"; 
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id', $linea[0]);
        $stmtVerificar->bindParam(':id_videojuego', $linea[3]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO proveedores_videojuegos_pre (id, id_videojuego, precio_preorden) VALUES (:id, :id_videojuego, :precio_preorden)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':id_videojuego', $linea[3]);
        $stmt->bindParam(':precio_preorden', $linea[5]);
        $stmt->execute();
        echo "Datos cargados proveedores_videojuegos_pre\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de proveedores_vidoejuegos_pre: " . $e->getMessage();
}  

## TABLA USUARIOS
try {
    $db->beginTransaction();
    $csv_usuarios = file("CSV PAR/usuario_actividades.csv");
    foreach($csv_usuarios as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2, 3, 4, 11])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM usuarios WHERE id_usuario = :id_usuario";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_usuario', $linea[0]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO usuarios (id_usuario, nombre, mail, password, username, fecha_nacimiento) VALUES (:id_usuario, :nombre, :mail, :password, :username, :fecha_nacimiento)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':mail', $linea[2]);
        $stmt->bindParam(':password', $linea[3]);
        $stmt->bindParam(':username', $linea[4]);
        $fecha_nacimiento = date_format(date_create_from_format('d-m-Y', $linea[11]), 'Y-m-d');
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->execute();
        echo "Datos cargados usuarios\n";

    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de usuarios: " . $e->getMessage();

}

## TABLA USUARIO PROVEEDOR
try {
    $db->beginTransaction();
    $csv_usuario_proveedor = file("CSV PAR/usuario_proveedor.csv");
    foreach($csv_usuario_proveedor as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM usuario_proveedor WHERE id_usuario = :id_usuario AND id_proveedor = :id_proveedor";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_usuario', $linea[0]);
        $stmtVerificar->bindParam(':id_proveedor', $linea[1]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO usuario_proveedor (id_usuario, id_proveedor) VALUES (:id_usuario, :id_proveedor)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $linea[0]);
        $stmt->bindParam(':id_proveedor', $linea[1]);
        $stmt->execute();
        echo "Datos cargados usuario_proveedor\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de usuario_proveedor: " . $e->getMessage();
}

## TABLA USUARIO HORAS --> Se produce un error por que no esta el id de videojuego = 2 en la tabla videojuegos
try {
    $db->beginTransaction();
    $csv_usuario_horas = file("CSV PAR/usuario_actividades.csv");
    foreach($csv_usuario_horas as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 5, 7])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM usuario_horas WHERE id_usuario = :id_usuario AND id_videojuego = :id_videojuego";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_usuario', $linea[0]);
        $stmtVerificar->bindParam(':id_videojuego', $linea[5]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        #$sql = "INSERT INTO usuario_horas (id_usuario, id_videojuego, cantidad) VALUES (:id_usuario, :id_videojuego, :cantidad)";
        #$stmt = $db->prepare($sql);
        #$stmt->bindParam(':id_usuario', $linea[0]);
        #$stmt->bindParam(':id_videojuego', $linea[5]);
        #$stmt->bindParam(':cantidad', $linea[7]);
        #$stmt->execute();
        #echo "Datos cargados usuario_horas\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de usuario_horas: " . $e->getMessage();
}

## TABLA USUARIO RESENA
try {
    $db->beginTransaction();
    $csv_usuario_resena = file("CSV PAR/usuario_actividades.csv");
    foreach($csv_usuario_resena as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 5, 6, 8, 9, 10])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM usuario_resena WHERE id_usuario = :id_usuario AND id_videojuego = :id_videojuego";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_usuario', $linea[0]);
        $stmtVerificar->bindParam(':id_videojuego', $linea[5]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO usuario_resena (id_usuario, id_videojuego, fecha, veredicto, titulo, texto) VALUES (:id_usuario, :id_videojuego, :fecha, :veredicto, :titulo, :texto)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $linea[0]);
        $stmt->bindParam(':id_videojuego', $linea[5]);
        $fecha = date_format(date_create_from_format('d-m-Y', $linea[6]), 'Y-m-d');
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':veredicto', $linea[8]);
        $stmt->bindParam(':titulo', $linea[9]);
        $stmt->bindParam(':texto', $linea[10]);
        $stmt->execute();
        echo "Datos cargados usuario_resena\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de usuario_resena: " . $e->getMessage();
}

## TABLA PAGO NO SUSCRIPCION
try {
    $db->beginTransaction();
    $csv_pago_no_suscripcion = file("CSV PAR/pagos.csv");
    foreach($csv_pago_no_suscripcion as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2, 3, 4, 5, 6])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM pago_no_suscripcion WHERE pago_id = :pago_id";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':pago_id', $linea[0]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO pago_no_suscripcion (pago_id, monto, fecha, id_usuario, preorden, id_proveedor, id_videojuego) VALUES (:pago_id, :monto, :fecha, :id_usuario, :preorden, :id_proveedor, :id_videojuego)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pago_id', $linea[0]);
        $stmt->bindParam(':monto', $linea[1]);
        $fecha = date_format(date_create_from_format('d-m-y', $linea[2]), 'Y-m-d');
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':id_usuario', $linea[3]);
        $stmt->bindParam(':preorden', $linea[4]);
        $stmt->bindParam(':id_proveedor', $linea[5]);
        $stmt->bindParam(':id_videojuego', $linea[6]);
        $stmt->execute();
        echo "Datos cargados pago_no_suscripcion\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de pago_no_suscripcion: " . $e->getMessage();
}
## TABLA Pago SUSCRIPCION --> PROBLEMA ACA 
try {
    $db->beginTransaction();
    $csv_pago_suscripcion = file("CSV PAR/pagos.csv");
    foreach($csv_pago_suscripcion as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (verificarCampos($linea, [0, 1, 2, 3, 7])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM pago_suscripcion WHERE pago_id = :pago_id";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':pago_id', $linea[0]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }

        $sql = "INSERT INTO pago_suscripcion (pago_id, monto, fecha, id_usuario, subs_id) VALUES (:pago_id, :monto, :fecha, :id_usuario, :subs_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':pago_id', $linea[0]);
        $stmt->bindParam(':monto', $linea[1]);
        $fecha = date_format(date_create_from_format('d-m-y', $linea[2]), 'Y-m-d');
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':id_usuario', $linea[3]);
        $stmt->bindParam(':subs_id', $linea[7]);
        $stmt->execute();
        echo "Datos cargados pago_suscripcion\n";
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de pago_suscripcion: " . $e->getMessage();
}   
?>


