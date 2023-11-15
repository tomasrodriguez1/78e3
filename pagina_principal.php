

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
        <a href="one_time_purchase.php">One Time Purchases</a>
        <a href="consulta_inestructurada.php">Consulta Inestructurada</a>
        <!-- Agrega aquí más enlaces según necesites -->
    </div>

    <br>
    <br>

    <div class="logout-button">
        <a href="auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
    <br>
    <br>
   <!-- Aquí empieza el contenido de la página -->
   <h1 class="titulo-suscripciones">Suscripciones</h1>

   <div>
        <?php
        try {
            $sql = "SELECT * FROM proveedores";
            $stmt = $db->query($sql);

            echo "<table class='center'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Plataforma</th></tr>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['plataforma']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

</body>
</html>
