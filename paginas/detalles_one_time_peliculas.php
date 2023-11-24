<?php
require("../data/conexion.php");
include('../templates/header.html');
$id_videojuego = $_GET['id'] ?? null;

if (!$id_videojuego) {
    die("ID de pelicula no proporcionado");
}

function obtenerDetallePelicula($db2, $id_pelicula) {
    try {
        $sql = "SELECT
            p.titulo,
            p.duracion,
            p.clasificacion,
            p.puntuacion,
            p.ano
        FROM peliculas p
        WHERE p.pid = :id_pelicula";

        $stmt = $db2->prepare($sql);
        $stmt->bindParam(':id_pelicula', $id_pelicula);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error buscando detalles peliculas: " . $e->getMessage();
    }
}

function obtenerProveedoresPelicula($db2, $id_pelicula) {
    try {
        $sql = "SELECT  p.pid,
                        pa.pro_id,
                        pr.nombre AS proveedor,
                        pa.precio,
                        pa.disponibilidad
                FROM peliculasarriendo pa
                JOIN peliculas p ON pa.pid = p.pid
                JOIN proveedores pr ON pa.pro_id = pr.id
                WHERE p.pid = :id_pelicula";

        $stmt = $db2->prepare($sql);
        $stmt->bindParam(':id_pelicula', $id_pelicula);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error buscando proveedores se la pelicula: " . $e->getMessage();
    }
}

$detallePelicula = obtenerDetallePelicula($db2, $id_videojuego);

$proveedoresPelicula = obtenerProveedoresPelicula($db2, $id_videojuego);

?>

<h1><?php echo $detallePelicula['titulo']; ?></h1>

<p>Duración: <?php echo $detallePelicula['duracion']; ?> minutos.</p>
<p>Clasificación: <?php echo $detallePelicula['clasificacion']; ?></p>
<p>Puntuación: <?php echo $detallePelicula['puntuacion']; ?></p>
<p>Año: <?php echo $detallePelicula['ano']; ?></p>

<h2>Proveedores de la película</h2>
<ul>
    <?php foreach ($proveedoresPelicula as $proveedor) : ?>
        <div>
            Proveedor: <?php echo $proveedor['proveedor']; ?>    |    
            Precio: <?php echo $proveedor['precio']; ?>     |    
            Disponibilidad: <?php echo $proveedor['disponibilidad']; ?>
        </div>
    <?php endforeach; ?>
</ul>

<h2>Comprar Juego</h2>
        <form id="purchaseForm" action="confirmacion_compra_videojuego.php" method="post">
            <label for="proveedor">Elige un proveedor:</label>
            <select name="proveedor" id="proveedor">
                <?php foreach ($proveedoresPelicula as $proveedor): ?>
                    <option value="<?= $proveedor['proveedor'] ?>"><?= htmlspecialchars($proveedor['proveedor']) ?> - <?= htmlspecialchars($proveedor['precio']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="id_pelicula" value="<?= htmlspecialchars($id_pelicula) ?>">
            <button id="initialBuyButton" type="submit">Comprar</button>
        </form>
        
        <!-- Confirmation Buttons -->
        <div id="confirmButtons" style="display: none;">
            <button type="button" onclick="document.getElementById('purchaseForm').submit();">Confirmar</button>
            <button type="button" onclick="goBack();">Volver Atrás</button>
        </div>
        
<?php

