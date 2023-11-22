<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../templates/header.html');
require("../data/conexion.php");

session_start();

// Verificar si el usuario ya está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

?>

<body>

    <!-- INICIO DE LA PAGINA -->

    <!-- SECCION: Barra de Navegación -->
    <div style="text-align: center;">
        <div class="navbar">
            <a href="perfil_usuario.php">Mi Perfil</a>
            <a href="../pagina_principal.php">Página Principal</a>
        </div>
    </div>

    <br>
    <br>

    <!-- SECCION: Compras One-Time -->
    <h1 class="titulo-compras">Compras One-Time</h1>
    
    <!-- Lista de Contenido -->
    <div class="contenido-container">
        <?php
        // foreach ($contenido as $item) {
        //     echo "<div class='contenido-item'>";
        //     echo "<a href='detalles.php?id=" . htmlspecialchars($item['id']) . "'>";
        //     echo htmlspecialchars($item['nombre']);
        //     echo "</a>";
        //     echo "</div>";
        // }
        ?>
    </div>

    <br>
    <br>

    <!-- Botones en la parte inferior -->
    <div class="footer-buttons">
        <a href="../pagina_principal.php" class="btn-home">Volver al Inicio</a>
        <a href="../auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
    
    <!-- FIN DE LA PAGINA -->

</body>
</html>

