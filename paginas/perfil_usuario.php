<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../data/conexion.php");
include('../templates/header.html');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$query = "SELECT nombre, mail, username, fecha_nacimiento FROM usuarios WHERE id_usuario = :user_id";
$stmt = $db2->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
$edad = '';
if ($userDetails && $userDetails['fecha_nacimiento']) {
    $fechaNacimiento = date_create_from_format('Y-m-d', $userDetails['fecha_nacimiento']);
    $today = new DateTime('now');
    $edad = date_diff($fechaNacimiento, $today)->y;
}
$querySuscripciones = "SELECT estados_subscripciones, fechas_inicio, fechas_termino FROM vista_info_usuarios_subscripciones WHERE id_usuario = :user_id";
$stmtSuscripciones = $db2->prepare($querySuscripciones);
$stmtSuscripciones->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtSuscripciones->execute();
$suscripciones = $stmtSuscripciones->fetchAll(PDO::FETCH_ASSOC);
$queryHorasJugadas = "SELECT SUM(cantidad) AS horas_jugadas FROM usuario_horas WHERE id_usuario = :user_id";
$stmtHorasJugadas = $db->prepare($queryHorasJugadas);
$stmtHorasJugadas->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmtHorasJugadas->execute();
$horasJugadas = $stmtHorasJugadas->fetch(PDO::FETCH_ASSOC);

$nombre = $userDetails['nombre'];
$email = $userDetails['mail'];
$username = $userDetails['username'];

function obtenerSubscripcionesPeliculas($db2, $id_usuario) {
    try {
        $sql = "SELECT 
                    p.nombre,
                    s.fecha_inicio,
                    s.estado
                FROM subscripciones s
                JOIN proveedores p ON s.pro_id = p.id
                WHERE s.estado='activa' AND s.uid=:id_usuario
                ORDER BY s.fecha_inicio ASC";

        $stmt = $db2->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $subscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $subscripciones;
    } catch (PDOException $e) {
        echo "Error al obtener las suscripciones activas de peliculas: " . $e->getMessage();
        return false;
    }
}

function obtenerSubscripcionesVideojuegos($db, $id_usuario) {
    try {
        $sql = "SELECT 
                    s.fecha_inicio,
                    s.estado,
                    s.fecha_termino,
                    v.titulo,
                    s.mensualidad
                FROM suscripciones s
                JOIN videojuegos v ON s.id_videojuego = v.id_videojuego
                WHERE s.estado = 'active' AND s.id_usuario = :id_usuario
                ORDER BY s.fecha_inicio ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $subscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $subscripciones;
    } catch (PDOException $e) {
        echo "Error al obtener las suscripciones activas de videojuegos: " . $e->getMessage();
        return false;
    }
}


$suscripciones_peliculas = obtenerSubscripcionesPeliculas($db2, $_SESSION['user_id']);
$suscripciones_videojuegos = obtenerSubscripcionesVideojuegos($db, $_SESSION['user_id']);
?>

<html>
<head>
    <title>Mi Perfil</title>
</head>
<body>
    <div style="text-align: center;">
        <div class="navbar">
            <a href="../pagina_principal.php">Página Principal</a>
        </div>
    </div>

    <h1>Mi Perfil</h1>

    <h2>Información Personal</h2>
    <p>Nombre: <?php echo htmlspecialchars($nombre); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Nombre de Usuario: <?php echo htmlspecialchars($username); ?></p>
    <p>Edad: <?php echo htmlspecialchars($edad); ?> años</p>

    <h2>Actividades</h2>
    <p>Horas Totales Jugadas en Videojuegos: <?php echo htmlspecialchars($horasJugadas['horas_jugadas']); ?></p>

    <h2>Suscripciones Activas</h2>
    <h3> Suscripciones a proveedores de peliculas: </h3>
    <?php
    if ($suscripciones_peliculas && count($suscripciones_peliculas) > 0) {
        echo "<div style='display: flex; justify-content: center;'>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Nombre del Proveedor</th><th>Fecha de Inicio</th><th>Estado</th></tr>";

        // Recorrer cada suscripción y mostrar sus detalles
        foreach ($suscripciones_peliculas as $suscripcion) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($suscripcion['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['fecha_inicio']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['estado']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p style='text-align: center;'>No hay suscripciones activas de películas.</p>";
    }
    ?>


    <h3> Suscripciones a proveedores de Videojuegos: </h3>
    <?php
    if ($suscripciones_videojuegos && count($suscripciones_videojuegos) > 0) {
        echo "<div style='display: flex; justify-content: center;'>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Nombre del Videojuego</th><th>Fecha de Inicio</th><th>Estado</th><th>Fecha de Termino</th><th>Mensualidad</th></tr>";

        // Recorrer cada suscripción y mostrar sus detalles
        foreach ($suscripciones_videojuegos as $suscripcion) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($suscripcion['titulo']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['fecha_inicio']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['estado']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['fecha_termino']) . "</td>";
            echo "<td>" . htmlspecialchars($suscripcion['mensualidad']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p style='text-align: center;'>No hay suscripciones activas de videojuegos.</p>";
    }

    ?>
    <div class="logout-button">
        <a href="../auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

</body>
</html>


