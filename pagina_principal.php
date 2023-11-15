

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
      <!-- Modal para detalles del proveedor -->
      <div id="modalProveedor" style="display:none;">
        <div id="detallesProveedor"></div>
        <button onclick="cerrarModal()">Cerrar</button>
    </div>
    <!-- Código JavaScript -->
    <script>
    // Event listener para los clics en los nombres de los proveedores
    document.querySelectorAll('.proveedor').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var proveedorId = this.getAttribute('data-id');
            // Aquí puedes hacer una solicitud AJAX para obtener los detalles del proveedor
            // Por ahora, simplemente mostraremos el ID
            document.getElementById('detallesProveedor').innerHTML = 'Detalles del proveedor con ID: ' + proveedorId;
            document.getElementById('modalProveedor').style.display = 'block';
        });
    });

    function cerrarModal() {
        document.getElementById('modalProveedor').style.display = 'none';
    }
    </script>

    <!-- INICIO DE LA PAGINA -->

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
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>" . htmlspecialchars($row['nombre']) . "</li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>


    <!-- FIN DE LA PAGINA -->


</body>
</html>
