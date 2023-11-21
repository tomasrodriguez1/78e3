<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("conexion.php");

## FUNCIONES
function verificarCampos($linea, $indicesCampos) {
    foreach ($indicesCampos as $indice) {
        if (!isset($linea[$indice]) || trim($linea[$indice]) === '') {
            return false;
        }
    }
    return true;
}

## Funciones grupo impar
$replacements = [
    '√°' => 'á',
    '√©' => 'é',
    '√≠' => 'í',
    '√≥' => 'ó',
    '√∫' => 'ú',
    '√±' => 'ñ',
    '√ü' => 'ü',
    '√§' => 'ç',
    '√®' => '®',
    '√©' => '©',
    '√¢' => '¢',
    '√π' => 'π',
    '√±' => '±',
    '√∞' => '∞',
    '√µ' => 'µ',
    '√£' => '£',
    '√§' => '§',
    '√¨' => '¨',
    '√´' => '´',
    '√ª' => 'ª',
    '√º' => 'º',
    '√Ω' => 'Ω',
    '√∂' => 'ö',
    '√Ñ' => 'Ö',
    '√Ñ' => 'Ä',
    '√•' => '•',
    '√¶' => '¶',
    '√ß' => 'ß',
    '√Å' => 'å',
    '√Ä' => 'ä',
    '√†' => '†',
    '√§' => '§',
    '√∫' => 'š',
    '√¶' => 'Œ',
    '√ü' => 'œ',
    '√Ü' => 'Ü',
    '√ö' => 'é',
    '¬°' => '¡',
    '±' => 'ñ',
    '¬ø' => '¿', 
    '©' => 'é',
    '√â' => 'É',
];

function fixEncoding($text, $replacements) {
    foreach ($replacements as $incorrect => $correct) {
        $text = str_replace($incorrect, $correct, $text);
    }
    return $text;
}

function convertirFecha($fecha) {
    $date = DateTime::createFromFormat('d-m-y', $fecha);
    return $date ? $date->format('Y-m-d') : null;
}

function convertirFecha2($fecha) {
    $date = DateTime::createFromFormat('m-d-Y', $fecha);
    return $date ? $date->format('Y-m-d') : null;
}


## TABLA Usuarios (id, nombre, mail, password, username, fecha_nacimiento)
try {
    $db->beginTransaction();
    $csv_usuarios = file("CSV IMPAR/usuarios.csv");
    foreach($csv_usuarios as $index => $linea) {
        if ($index === 0) continue;
        $linea = str_getcsv($linea, ";");
        if (!verificarCampos($linea, [0, 1, 2, 3, 4, 5])) {
            continue;
        }
        $sqlVerificar = "SELECT COUNT(*) FROM usuarios WHERE id = :id";
        $stmtVerificar = $db->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id', $linea[0]);
        $stmtVerificar->execute();

        if ($stmtVerificar->fetchColumn() > 0) {
            continue;
        }
        $sql = "INSERT INTO usuarios (id, nombre, mail, password, username, fecha_nacimiento) VALUES (:id, :nombre, :mail, :password, :username, :fecha_nacimiento)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $linea[0]);
        $stmt->bindParam(':nombre', $linea[1]);
        $stmt->bindParam(':mail', $linea[2]);
        $stmt->bindParam(':password', $linea[3]);
        $stmt->bindParam(':username', $linea[4]);
        $fecha_nacimiento = date_format(date_create_from_format('d-m-Y', $linea[5]), 'Y-m-d');
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->execute();

    }
    $db->commit();
    echo "Datos cargados en Usuarios\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de usuarios: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## TABLA GeneroSubgenero (genero, nombre_subgenero)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/generos_subgeneros.csv", "r");

    // Skip the header line
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $line[0] = fixEncoding($line[0], $replacements);
        $line[1] = fixEncoding($line[1], $replacements);

        $sql = "INSERT INTO GeneroSubgenero (genero, nombre_subgenero) VALUES (:genero, :nombre_subgenero) ON CONFLICT (genero, nombre_subgenero) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':genero', $line[0]);
        $stmt->bindParam(':nombre_subgenero', $line[1]);
        $stmt->execute();
    }

    // Close the file
    fclose($csvFile);

    // Commit the transaction
    $db->commit();
    echo "Datos cargados en GeneroSubgenero\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de GenerosSubgeneros: " . $e->getMessage();
    echo "\n";
    echo "\n";
}


## TABLA Series (sid, nombre)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/multimedia.csv", "r");

    // Skip the header line
    fgetcsv($csvFile, 1000, ";");

    $series = [];
    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $sid = $line[1];
        $serieName = fixEncoding($line[9], $replacements);
        $nombre = fixEncoding($line[3], $replacements); // Fixing encoding for 'nombre' column

        // Skip the row if 'sid' is empty or if 'serieName' is empty
        if (!is_numeric($sid) || empty($sid)) {
            continue;
        }

        if (!isset($series[$sid])) {
            $series[$sid] = $serieName;
        }
    }

    foreach ($series as $sid => $name) {
        $sql = "INSERT INTO Series (sid, nombre) VALUES (:sid, :nombre) ON CONFLICT (sid) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $name, PDO::PARAM_STR);
        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en Series\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de SERIES: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla Peliculas (pid, titulo, duracion, clasificacion, puntuacion, ano)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/multimedia.csv", "r");

    // Skip the header line
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pid = $line[0];
        $titulo = fixEncoding($line[3], $replacements);
        $duracion = $line[4];
        $clasificacion = $line[5];
        $puntuacion = $line[6];
        $año = $line[7];

        // Skip the row if 'pid' is empty
        if (!is_numeric($pid) || empty($pid)) {
            continue;
        }

        $sql = "INSERT INTO Peliculas (pid, titulo, duracion, clasificacion, puntuacion, ano) VALUES (:pid, :titulo, :duracion, :clasificacion, :puntuacion, :ano) ON CONFLICT (pid) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':clasificacion', $clasificacion, PDO::PARAM_STR);
        $stmt->bindParam(':puntuacion', $puntuacion);
        $stmt->bindParam(':ano', $año, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en Peliculas\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de PELICULAS: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla Capitulos (sid, cid, titulo, duracion, clasifcacion, puntuacion, año, numero)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/multimedia.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $sid = $line[1];
        $cid = $line[2];
        $titulo = fixEncoding($line[3], $replacements);
        $duracion = $line[4];
        $clasificacion = $line[5];
        $puntuacion = $line[6];
        $año = $line[7];
        $numero = $line[8];

        // Verificar si 'cid' es un número (clave primaria)
        if (!is_numeric($cid)) {
            continue;
        }

        $sql = "INSERT INTO Capitulos (sid, cid, titulo, duracion, clasificacion, puntuacion, ano, numero) VALUES (:sid, :cid, :titulo, :duracion, :clasificacion, :puntuacion, :ano, :numero) ON CONFLICT (cid) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':clasificacion', $clasificacion, PDO::PARAM_STR);
        $stmt->bindParam(':puntuacion', $puntuacion);
        $stmt->bindParam(':ano', $año, PDO::PARAM_INT);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en Capitulos\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos En Capitulos: \n" . $e->getMessage();
    echo "\n";
    echo "\n";
}

#Tabla GenerosCapitulos (sid, cid, genero)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/multimedia.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $sid = $line[1];
        $cid = $line[2];
        $genero = fixEncoding($line[10], $replacements);

        // Verificar si 'sid' y 'cid' están presentes
        if (!is_numeric($sid) || !is_numeric($cid)) {
            continue;
        }

        $sql = "INSERT INTO GenerosCapitulos (sid, cid, genero) VALUES (:sid, :cid, :genero) ON CONFLICT (sid, cid) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en GenerosCapitulos\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en GenerosCapitulos: \n" . $e->getMessage();
}

## Tabla GenerosPeliculas (pid, genero)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/multimedia.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pid = $line[0];
        $genero = fixEncoding($line[10], $replacements);

        // Verificar si 'pid' está presente
        if (!is_numeric($pid)) {
            continue;
        }

        $sql = "INSERT INTO GenerosPeliculas (pid, genero) VALUES (:pid, :genero) ON CONFLICT (pid, genero) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en GenerosPeliculas\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en GenerosPeliculas: \n" . $e->getMessage();
}

## Tabla Proveedores (id PK, nombre, costo INT)\
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/proveedores.csv", "r");

    // Skip the header line
    fgetcsv($csvFile, 1000, ";");

    $proveedores = [];
    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $id = $line[0];
        $nombre = $line[1];
        $costo = $line[2];

        // Saltamos las filas con id invalidos
        if (!is_numeric($id) || !is_numeric($costo)) {
            continue; // Skip rows with invalid 'id' or 'costo'
        }

        // Group by 'nombre', keeping the first encountered 'id' and 'costo'
        if (!isset($proveedores[$nombre])) {
            $proveedores[$nombre] = ['id' => $id, 'costo' => $costo];
        }
    }

    foreach ($proveedores as $nombre => $data) {
        $sql = "INSERT INTO Proveedores (id, nombre, costo) VALUES (:id, :nombre, :costo) ON CONFLICT (id) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':costo', $data['costo'], PDO::PARAM_INT);
        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en Proveedores\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos de PROVEEDORES: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla ProveedoresPeliculas (pro_id, pid)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/proveedores.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pro_id = $line[0];
        $pid = $line[4];
        $disponibilidad = $line[6];

        // Verificar si 'pid' es un número y 'disponibilidad' no es un número
        if (!is_numeric($pid) || is_numeric($disponibilidad)) {
            continue;
        }

        $sql = "INSERT INTO ProveedoresPeliculas (pro_id, pid) VALUES (:pro_id, :pid) ON CONFLICT (pro_id, pid) DO NOTHING;";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en ProveedoresPeliculas\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en ProveedoresPeliculas: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla ProveedoresSeries (pro_id, sid)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/proveedores.csv", "r");

    // Skip the header line
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pro_id = $line[0];
        $sid = $line[3];

        // Check if 'sid' is a valid integer and 'disponibilidad' is not a number
        if (!is_numeric($sid)) {
            continue;
        }

        $sql = "INSERT INTO ProveedoresSeries (pro_id, sid) VALUES (:pro_id, :sid) ON CONFLICT (pro_id, sid) DO NOTHING;";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en ProveedoresSeries\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos ProveedoresSeries: " . $e->getMessage();
    echo "\n";
    echo "\n";
}


## Tabla PeliculasArriendo (pro_id, pid, precio, disponibilidad)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/proveedores.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pro_id = trim($line[0]);
        $pid = trim($line[4]);
        $precio = trim($line[5]);
        $disponibilidad = trim($line[6]);

        // Verificar si 'pro_id', 'pid' y 'disponibilidad' son números válidos
        if (!is_numeric($pro_id) || !is_numeric($pid) || !is_numeric($disponibilidad)) {
            continue; // Saltar filas no válidas
        }

        $sql = "INSERT INTO PeliculasArriendo (pro_id, pid, precio, disponibilidad) VALUES (:pro_id, :pid, :precio, :disponibilidad) ON CONFLICT (pro_id, pid) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_INT);
        $stmt->bindParam(':disponibilidad', $disponibilidad, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en PeliculasArriendo\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en PeliculasArriendo: " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla Subscripciones (id, estado, fecha_inicio, pro_id, uid, fecha_termino)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/subscripciones.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $id = trim($line[0]);
        $estado = $line[1];
        $fecha_inicio = convertirFecha($line[2]);
        $pro_id = $line[3];
        $uid = $line[4];
        $fecha_termino = convertirFecha($line[5]);

        // Verificar si los campos obligatorios están presentes
        if (!is_numeric($id) || !is_numeric($pro_id) || !is_numeric($uid)) {
            continue; // Saltar filas no válidas
        }

        $sql = "INSERT INTO Subscripciones (id, estado, fecha_inicio, pro_id, uid, fecha_termino) VALUES (:id, :estado, :fecha_inicio, :pro_id, :uid, :fecha_termino) ON CONFLICT (id) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_termino', $fecha_termino);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en Subscripciones\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en Subscripciones: \n " . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla PagosSubscripciones (pago_id, monto, fecha, uid, subs_id)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/pagos.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pago_id = $line[0];
        $monto = $line[1];
        $fecha = convertirFecha($line[2]);
        $uid = $line[3];
        $subs_id = $line[4];
        $pid = $line[5];
        $pro_id = $line[6];

        // Verificar si 'pro_id' y 'pid'  no son numéricos
        if (is_numeric($pro_id) || is_numeric($pid)) {
            continue;
        }

        $sql = "INSERT INTO PagosSubscripcion (pago_id, monto, fecha, uid, subs_id) VALUES (:pago_id, :monto, :fecha, :uid, :subs_id) ON CONFLICT (pago_id) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pago_id', $pago_id, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':subs_id', $subs_id, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en PagosSubscripcion\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en PagosSubricpcion: \n" . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla PagosPeliculasArriendo

try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/pagos.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $pago_id = $line[0];
        $monto = $line[1];
        $fecha = convertirFecha($line[2]);
        $uid = $line[3];
        $pid = $line[5];
        $pro_id = $line[6];
        $subs_id = $line[4];

        // Verificar si 'pro_id', 'pid' son numéricos y 'subs_id' no es numérico
        if (!is_numeric($pro_id) || !is_numeric($pid) || is_numeric($subs_id)) {
            continue;
        }

        $sql = "INSERT INTO PagosPeliculasArriendo (pago_id, monto, fecha, uid, pid, pro_id) VALUES (:pago_id, :monto, :fecha, :uid, :pid, :pro_id) ON CONFLICT (pago_id) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pago_id', $pago_id, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en PagosPeliculasArriendo\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en PagosPeliculasArriendo: \n " . $e->getMessage();
    echo "\n";
    echo "\n";
}


## Tabla VisualizacionesPeliculas (uid, pid, fecha)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/visualizaciones.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $uid = $line[0];
        $pid = $line[1];
        $fecha = convertirFecha2($line[3]);

        // Verificar si 'pid' está presente
        if (empty($pid)) {
            continue;
        }

        $sql = "INSERT INTO VisualizacionesPeliculas (uid, pid, fecha) VALUES (:uid, :pid, :fecha) ON CONFLICT (uid, pid, fecha) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en VisualizacionesPeliculas\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en VisualizacionesPeliculas: \n" . $e->getMessage();
    echo "\n";
    echo "\n";
}

## Tabla VisualizacionesCapitulos (uid, cid, fecha)
try {
    $db->beginTransaction();

    $csvFile = fopen("CSV IMPAR/visualizaciones.csv", "r");

    // Omitir la línea del encabezado
    fgetcsv($csvFile, 1000, ";");

    while (($line = fgetcsv($csvFile, 1000, ";")) !== FALSE) {
        $uid = $line[0];
        $cid = $line[1];
        $fecha = convertirFecha2($line[3]);

        // Verificar si 'cid' está presente
        if (empty($cid)) {
            continue;
        }

        $sql = "INSERT INTO VisualizacionesCapitulos (uid, cid, fecha) VALUES (:uid, :cid, :fecha) ON CONFLICT (uid, cid, fecha) DO NOTHING";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);

        $stmt->execute();
    }

    fclose($csvFile);

    $db->commit();
    echo "Datos cargados en VisualizacionesCapitulos\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error durante la carga de datos en VisualizacionesCapitulos: \n" . $e->getMessage();
    echo "\n";
    echo "\n";
}

