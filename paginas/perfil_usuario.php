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


?>
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
    <?php if (!empty($suscripciones)): ?>
        <ul>
            <?php foreach ($suscripciones as $suscripcion): ?>
                <li>
                    Estados de Suscripción: <?php echo is_array($suscripcion['estados_subscripciones']) ? implode(", ", $suscripcion['estados_subscripciones']) : $suscripcion['estados_subscripciones']; ?>
                    <br>
                    Fechas de Inicio: <?php echo is_array($suscripcion['fechas_inicio']) ? implode(", ", $suscripcion['fechas_inicio']) : $suscripcion['fechas_inicio']; ?>
                    <br>
                    Fechas de Término: <?php echo is_array($suscripcion['fechas_termino']) ? implode(", ", $suscripcion['fechas_termino']) : $suscripcion['fechas_termino']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay suscripciones activas.</p>
    <?php endif; ?>

    <div class="logout-button">
        <a href="../auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

</body>
</html>


