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
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error en la consulta SQL: " . $e->getMessage());
    }
}

function obtenerProveedoresJuego($db, $id_videojuego) {
    $sql = "SELECT 
                pv.precio,
                p.nombre as nombre_proveedor
            FROM proveedores_videojuegos pv
            JOIN proveedores p ON pv.id = p.id
            WHERE pv.id_videojuego = :id_videojuego";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_videojuego', $id_videojuego, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error en la consulta SQL: " . $e->getMessage());
    }
}

$detallesJuego = obtenerDetallesJuego($db, $id_videojuego);
$proveedoresJuego = obtenerProveedoresJuego($db, $id_videojuego);

if (!$detallesJuego) {
    die("Juego no encontrado");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Juego</title>
    <script type="text/javascript">
        // Wait for the DOM to be loaded before assigning event handlers
        document.addEventListener('DOMContentLoaded', function () {
            var purchaseForm = document.getElementById('purchaseForm');
            var confirmButtons = document.getElementById('confirmButtons');
            var initialBuyButton = document.getElementById('initialBuyButton');

            // Event handler for the form on submit
            purchaseForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting
                initialBuyButton.style.display = 'none'; // Hide the buy button
                confirmButtons.style.display = 'block'; // Show the confirm/back buttons
            });
        });

        // Function to hide confirm/back buttons and show the buy button again
        function goBack() {
            var initialBuyButton = document.getElementById('initialBuyButton');
            var confirmButtons = document.getElementById('confirmButtons');
            initialBuyButton.style.display = 'block'; // Show the buy button
            confirmButtons.style.display = 'none'; // Hide the confirm/back buttons
        }
    </script>
</head>
<body>
    <?php if ($detallesJuego): ?>
        <h1><?= htmlspecialchars($detallesJuego['titulo']) ?></h1>
        <p>Puntuación: <?= htmlspecialchars($detallesJuego['puntuacion']) ?></p>
        <p>Clasificación: <?= htmlspecialchars($detallesJuego['clasificacion']) ?></p>
        <p>Fecha de Lanzamiento: <?= htmlspecialchars($detallesJuego['fecha_de_lanzamiento']) ?></p>
        <p>Beneficio Preorden: <?= htmlspecialchars($detallesJuego['beneficio_preorden']) ?></p>
        <h2>Proveedores</h2>
        <?php foreach ($proveedoresJuego as $proveedor): ?>
            <p>Proveedor: <?= htmlspecialchars($proveedor['nombre_proveedor']) ?> - Precio: <?= htmlspecialchars($proveedor['precio']) ?></p>
        <?php endforeach; ?>
        
        <!-- Purchase Form -->
        <h2>Comprar Juego</h2>
        <form id="purchaseForm" action="confirmacion_compra_videojuego.php" method="post">
            <label for="proveedor">Elige un proveedor:</label>
            <select name="proveedor" id="proveedor">
                <?php foreach ($proveedoresJuego as $proveedor): ?>
                    <option value="<?= $proveedor['nombre_proveedor'] ?>"><?= htmlspecialchars($proveedor['nombre_proveedor']) ?> - <?= htmlspecialchars($proveedor['precio']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="id_videojuego" value="<?= htmlspecialchars($id_videojuego) ?>">
            <button id="initialBuyButton" type="submit">Comprar</button>
        </form>
        
        <!-- Confirmation Buttons -->
        <div id="confirmButtons" style="display: none;">
            <button type="button" onclick="document.getElementById('purchaseForm').submit();">Confirmar</button>
            <button type="button" onclick="goBack();">Volver Atrás</button>
        </div>
        
    <?php else: ?>
        <p>Juego no encontrado.</p>
    <?php endif; ?>
</body>
</html>



