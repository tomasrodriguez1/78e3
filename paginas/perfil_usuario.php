<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../data/conexion.php");
include('../templates/header.html');

session_start();

// Verificar si el usuario ya está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Consulta SQL para obtener la información de nombre, correo electrónico y nombre de usuario de la tabla "usuarios" según el usuario logueado
$query = "SELECT nombre, mail, username FROM usuarios WHERE id_usuario = :user_id";
$stmt = $db2->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

if ($stmt) {
    // Obtener los resultados de la consulta
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'];
    $email = $row['mail'];
    $username = $row['username'];
    
    // Liberar los resultados de la consulta
    $stmt->closeCursor();
} else {
    // Manejar el caso de error en la consulta
    echo "Error en la consulta: " . $db2->errorInfo()[2];
}

// Consulta para obtener la lista de suscripciones del usuario
$querySuscripciones = "SELECT servicios_subscripciones, fechas_inicio, fechas_termino FROM vista_info_usuarios_subscripciones WHERE id_usuario = :user_id";
$stmtSuscripciones = $db2->prepare($querySuscripciones);
$stmtSuscripciones->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtSuscripciones->execute();

$suscripciones = $stmtSuscripciones->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener la lista de suscripciones de videojuegos del usuario
$queryVideojuegos = "
    SELECT v.titulo, s.fecha_inicio, s.fecha_termino 
    FROM suscripciones s
    JOIN videojuegos v ON s.id_videojuego = v.id_videojuego
    WHERE s.id_usuario = :user_id AND s.estado = TRUE
    ORDER BY s.fecha_inicio DESC";
$stmtVideojuegos = $db->prepare($queryVideojuegos);
$stmtVideojuegos->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtVideojuegos->execute();

$videojuegos = $stmtVideojuegos->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las horas totales jugadas en videojuegos
$queryHorasJugadas = "
    SELECT SUM(cantidad) AS horas_jugadas 
    FROM usuario_horas 
    WHERE id_usuario = :user_id";
$stmtHorasJugadas = $db->prepare($queryHorasJugadas);
$stmtHorasJugadas->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtHorasJugadas->execute();

$horasJugadas = $stmtHorasJugadas->fetch(PDO::FETCH_ASSOC)['horas_jugadas'];

?>

<html>
<head>
    <title>Mi Perfil</title>
</head>
<body>
    <div style="text-align: center;">
        <!-- SECCION: Barra de Navegación -->
        <div class="navbar">
            <a href="../pagina_principal.php">Página Principal</a>
        </div>
    </div>

    <h1>Mi Perfil</h1>

    <h2>Información Personal</h2>
    <p>Nombre: <?php echo $nombre; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <p>Nombre de Usuario: <?php echo $username; ?></p>

    <h2>Suscripciones Activas de Películas</h2>
    <?php if (!empty($suscripciones)): ?>
        <ul>
            <?php foreach ($suscripciones as $suscripcion): ?>
                <li>Servicio: <?php echo htmlspecialchars(implode(", ", $suscripcion['servicios_subscripciones'])); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay suscripciones activas.</p>
    <?php endif; ?>

    <h2>Suscripciones a Videojuegos</h2>
    <?php if (!empty($videojuegos)): ?>
        <ul>
            <?php foreach ($videojuegos as $videojuego): ?>
                <li><?php echo htmlspecialchars($videojuego['titulo']) . " - Desde: " . htmlspecialchars($videojuego['fecha_inicio']) . " hasta " . htmlspecialchars($videojuego['fecha_termino']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay suscripciones activas a videojuegos.</p>
    <?php endif; ?>

    <h2>Horas Totales Jugadas en Videojuegos</h2>
    <p>Horas Totales Jugadas: <?php echo $horasJugadas; ?></p>

    <div class="logout-button">
        <a href="../auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

</body>
</html>
