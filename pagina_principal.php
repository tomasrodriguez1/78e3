

<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


    <div class="logout-button">
        <a href="auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

    <!-- Aquí puedes incluir el resto del contenido de tu página -->

    <?php
    require("data/conexion.php");

    $sql = "SELECT * FROM proveedores";
    $result = mysqli_query($conexion, $sql);

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Plataforma</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['plataforma'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    ?>

</body>
</html>
