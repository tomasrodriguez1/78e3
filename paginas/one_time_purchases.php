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
    <title>Videojuegos</title>
    <!-- Agrega aquí tus estilos CSS si es necesario -->
</head>
<body>
    <div class="navbar">
        <a href="perfil_usuario.php">Mi Perfil</a>
        <a href="../pagina_principal.php">Página Principal</a>
    </div>

    <button id="btnPeliculas">Películas</button>
    <button id="btnJuegos">Juegos</button>

    <div id="listaJuegos">
        <?php foreach ($videojuegos as $id_videojuego => $videojuego): ?>
            <div class="nombre-juego" data-id="<?= $id_videojuego ?>">
                <?= htmlspecialchars($videojuego['detalles']['titulo']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php foreach ($videojuegos as $id_videojuego => $videojuego): ?>
        <div id="detalles-juego-<?= $id_videojuego ?>" class="detalles-juego" style="display: none;">
            <button class="regresar">Regresar a la lista</button>
            <h2><?= htmlspecialchars($videojuego['detalles']['titulo']) ?></h2>
            <p>Puntuación: <?= htmlspecialchars($videojuego['detalles']['puntuacion']) ?></p>
            <p>Clasificación: <?= htmlspecialchars($videojuego['detalles']['clasificacion']) ?></p>
            <p>Fecha de Lanzamiento: <?= htmlspecialchars($videojuego['detalles']['fecha_de_lanzamiento']) ?></p>
            <?php if (!empty($videojuego['detalles']['beneficio_preorden'])): ?>
                <p>Beneficio Preorden: <?= htmlspecialchars($videojuego['detalles']['beneficio_preorden']) ?></p>
            <?php endif; ?>
            <ul>
                <?php foreach ($videojuego['proveedores'] as $nombreProveedor => $precio): ?>
                    <li><?= htmlspecialchars($nombreProveedor) ?> - Precio: $<?= htmlspecialchars($precio) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <script>
    document.getElementById('btnJuegos').addEventListener('click', function() {
        document.getElementById('listaJuegos').style.display = 'block';
        document.querySelectorAll('.detalles-juego').forEach(function(el) {
            el.style.display = 'none'; // Corregido aquí
        });
    });

    document.querySelectorAll('.nombre-juego').forEach(function(element) {
        element.addEventListener('click', function() {
            const juegoId = this.getAttribute('data-id');
            document.getElementById('listaJuegos').style.display = 'none';
            document.querySelectorAll('.detalles-juego').forEach(function(el) {
                el.style.display = 'none';
            });
            document.getElementById('detalles-juego-' + juegoId).style.display = 'block';
        });
    });

    document.querySelectorAll('.regresar').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('listaJuegos').style.display = 'block';
            document.querySelectorAll('.detalles-juego').forEach(function(el) {
                el.style.display = 'none';
            });
        });
    });
</script>
</body>
</html>

