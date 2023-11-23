<?php
require("../data/conexion.php");
include('../templates/header.html');
$id_videojuego = $_GET['id'] ?? null;

if (!$id_videojuego) {
    die("ID de juego no proporcionado");
}

function obtenerDetallesJuego($db, $id_videojuego) {
    $sql = "SELECT 
                v.id_videojuego, 
                v.titulo, 
                v.puntuacion, 
                v.clasificacion, 
                v.fecha_de_lanzamiento,
                vns.beneficio_preorden
            FROM videojuegos v
            LEFT JOIN videojuego_no_suscripcion vns ON v.id_videojuego = vns.id_videojuego
            WHERE v.id_videojuego = :id_videojuego";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        $detalles = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error en la consulta SQL: " . $e->getMessage());
    }

    // Consulta para obtener proveedores
    $sql_proveedores = "SELECT pv.id, p.nombre
                        FROM proveedores_videojuegos pv
                        JOIN proveedores p ON pv.id_proveedor = p.id
                        WHERE pv.id_videojuego = :id_videojuego";
    $stmt_proveedores = $db->prepare($sql_proveedores);
    $stmt_proveedores->bindParam(':id_videojuego', $id_videojuego, PDO::PARAM_INT);
    $stmt_proveedores->execute();
    $detalles['proveedores'] = $stmt_proveedores->fetchAll(PDO::FETCH_ASSOC);

    return $detalles;
}

$detallesJuego = obtenerDetallesJuego($db, $id_videojuego);

if (!$detallesJuego) {
    die("Juego no encontrado");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Juego</title>
</head>
<body>
    <?php if ($detallesJuego): ?>
        <h1><?= htmlspecialchars($detallesJuego['titulo']) ?></h1>
        <p>Puntuación: <?= htmlspecialchars($detallesJuego['puntuacion']) ?></p>
        <p>Clasificación: <?= htmlspecialchars($detallesJuego['clasificacion']) ?></p>
        <p>Fecha de Lanzamiento: <?= htmlspecialchars($detallesJuego['fecha_de_lanzamiento']) ?></p>
        <p>Beneficio Preorden: <?= htmlspecialchars($detallesJuego['beneficio_preorden']) ?></p>

        <!-- Formulario para seleccionar proveedor y comprar -->
        <form action="procesar_compra.php" method="post">
            <input type="hidden" name="id_videojuego" value="<?= $id_videojuego ?>">

            <label for="proveedor">Seleccione un proveedor:</label>
            <select name="proveedor" id="proveedor">
                <?php foreach ($detallesJuego['proveedores'] as $proveedor): ?>
                    <option value="<?= $proveedor['id'] ?>">
                        <?= htmlspecialchars($proveedor['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Comprar</button>
        </form>
    <?php else: ?>
        <p>Juego no encontrado.</p>
    <?php endif; ?>
</body>
</html>
