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

function obtenerPeliculasArriendo($db2) {
    try {
        $sql = "SELECT DISTINCT 
                        peliculas.pid,
                        peliculas.titulo
                FROM peliculas
                INNER JOIN peliculasarriendo ON peliculas.pid = peliculasarriendo.pid;";
        $stmt = $db2->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener peliculas: " . $e->getMessage());
    }
}

$contenido_peliculas = obtenerPeliculasArriendo($db2);

$peliculas = [];
foreach ($contenido_peliculas as $pelicula) {
    $id = $pelicula['pid'];
    $peliculas[$id] = $pelicula['titulo'];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Videojuegos</title>
    <style>
        #listaJuegos { display: none; } 
        #listaPeliculas { display: none; } 
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar">
            <a href="../pagina_principal.php">Página Principal</a>
        </div>
    </div>

    <h2> Selcciona el contenido que deseas comprar </h2>

    <button id="btnPeliculas">Películas</button>
    &nbsp;&nbsp;&nbsp;
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


    <div id="listaPeliculas">
        <?php foreach ($peliculas as $id_pelicula => $titulo_pelicula): ?>
            <div class="nombre-pelicula">
                <a href="detalles_one_time_peliculas.php?id=<?= urlencode($id_pelicula) ?>">
                    <?= htmlspecialchars($titulo_pelicula) ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>


    <script>
    function toggleDisplay(elementId) {
        var element = document.getElementById(elementId);
        element.style.display = (element.style.display === 'none' || element.style.display === '') ? 'block' : 'none';
    }

    document.getElementById('btnJuegos').addEventListener('click', function() {
        toggleDisplay('listaJuegos');
    });

    document.getElementById('btnPeliculas').addEventListener('click', function() {
        toggleDisplay('listaPeliculas');
    });
    </script>
</body>
</html>
