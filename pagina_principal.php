<?php 
include('./templates/header.html'); 
require("data/conexion.php");

session_start();

// Verificar si el usuario ya está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirigir al login si no está logueado
    exit;
}

?>

<body>

    <!-- Barra de Navegación -->
    <div class="navbar">
        <a href="perfil_usuario.php">Mi Perfil</a>
        <a href="pagina_suscripciones.php">Página de Suscripciones</a>
        <a href="one_time_purchase.php">One Time Purchases</a>
        <a href="consulta_inestructurada.php">Consulta Inestructurada</a>
        <!-- Agrega aquí más enlaces según necesites -->
    </div>

    <!-- Aquí puedes incluir el resto del contenido de tu página -->

    <div class="logout-button">
        <a href="auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</body>
</html>
