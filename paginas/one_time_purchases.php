<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../templates/header.html');
require("../data/conexion.php");

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

function obtenerContenidosDisponibles($db) {
    try {
        $sql = "SELECT 
                    v.id_videojuego, 
                    v.titulo, 
                    v.puntuacion, 
                    v.clasificacion, 
                    v.fecha_de_lanzamiento,
                    vns.beneficio_preorden,
                    pv.precio,
                    p.nombre as nombre_proveedor
                FROM videojuegos v
                LEFT JOIN videojuego_no_suscripcion vns ON v.id_videojuego = vns.id_videojuego
                LEFT JOIN proveedores_videojuegos pv ON v.id_videojuego = pv.id_videojuego
                LEFT JOIN proveedores p ON pv.id = p.id";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener videojuegos: " . $e->getMessage());
    }
}

$contenido = obtenerContenidosDisponibles($db);

$videojuegos = [];
foreach ($contenido as $item) {
    $id = $item['id_videojuego'];
    if (!isset($videojuegos[$id])) {
        $videojuegos[$id]['detalles'] = [
            'titulo' => $item['titulo'],
            'puntuacion' => $item['puntuacion'],
            'clasificacion' => $item['clasificacion'],
            'fecha_de_lanzamiento' => $item['fecha_de_lanzamiento'],
            'beneficio_preorden' => $item['beneficio_preorden']
        ];
    }
    $videojuegos[$id]['proveedores'][$item['nombre_proveedor']] = $item['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Videojuegos</title>
    <style>
        #listaJuegos { display: none; } 
    </style>
</head>
<body>
    <div class="navbar">
        <!-- Tu navbar aquí -->
    </div>

    <button id="btnPeliculas">Películas</button>
    <button id="btnJuegos">Juegos</button>

    <div id="listaJuegos">
        <?php foreach ($videojuegos as $id_videojuego => $videojuego): ?>
            <div class="nombre-juego">
                <a href="detalles_one_time.php?id=<?= urlencode($id_videojuego) ?>">
                    <?= htmlspecialchars($videojuego['detalles']['titulo']) ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.getElementById('btnJuegos').addEventListener('click', function() {
            var listaJuegos = document.getElementById('listaJuegos');
            if (listaJuegos.style.display === 'none') {
                listaJuegos.style.display = 'block';
            } else {
                listaJuegos.style.display = 'none';
            }
        });
    </script>
</body>
</html>
