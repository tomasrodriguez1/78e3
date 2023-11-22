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
$query = "SELECT nombre, mail, username FROM usuarios WHERE id = :user_id";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
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
    echo "Error en la consulta: " . $conexion->errorInfo()[2];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mi Perfil</title>
</head>
<body>
    <div style="text-align: center;">
        <!-- SECCION: Barra de Navegación -->
        <div class="navbar">
            <a href="perfil_usuario.php">Mi Perfil</a>
            <a href="../pagina_principal.php">Página Principal</a>
        </div>
    </div>

    <h1>Mi Perfil</h1>

    <h2>Información Personal</h2>
    <p>Nombre: <?php echo $nombre; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <p>Nombre de Usuario: <?php echo $username; ?></p>

</body>
</html>
