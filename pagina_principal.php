

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

    <!-- INICIO DE LA PAGINA -->

    <!-- SECCION: Barra de Navegación -->
    <div style="text-align: center;"> <!-- Contenedor para centralizar -->
        <div class="navbar">
            <a href="perfil_usuario.php">Mi Perfil</a>
            <a href="./paginas/one_time_purchase.php">One Time Purchases</a>
            <a href="consulta_inestructurada.php">Consulta Inestructurada</a>
            <!-- Agrega aquí más enlaces según necesites -->
        </div>
    </div>

    <br>
    <br>
   <!-- Formulario de Búsqueda -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" name="proveedor" placeholder="Nombre del Proveedor">
        <input type="text" name="nombre_videojuego" placeholder="Nombre del Videojuego">
        <input type="submit" value="Buscar">
    </form>

    <!-- Procesar el formulario y obtener resultados -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Aquí tu código para procesar el formulario y realizar la consulta
        // Por ejemplo:
        // $resultados = tuFuncionParaConsultarLaBaseDeDatos($_POST['proveedor'], $_POST['nombre_videojuego']);

        // Mostrar Resultados de la Búsqueda
        foreach ($resultados as $row) {
            // Mostrar cada resultado
            echo "<div>";
            echo "Proveedor: " . htmlspecialchars($row['nombre_proveedor']);
            echo " - Videojuego: " . htmlspecialchars($row['nombre_videojuego']);
            echo " - Precio: " . htmlspecialchars($row['precio']);
            echo "</div>";
            }
        }
    ?>
    <!-- Mostrar cada proveedor en su rectangulo -->
   <div class="proveedores-container">
        <?php
        try {
            $sql = "SELECT * FROM proveedores";
            $stmt = $db->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='proveedor-bloque' data-id='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</div>";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <div class="logout-button">
        <a href="auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
<!-- FIN DE LA PAGINA -->

<!-- Modal para detalles del proveedor -->
<div id="modalProveedor" style="display:none;">
    <div id="detallesProveedor"></div>
    <br>
    <button onclick="cerrarModal()">Cerrar</button>
</div>

<!-- Código JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('.proveedor-bloque').forEach(item => {
            item.addEventListener('click', function(e) {
                var proveedorId = this.getAttribute('data-id');
                document.getElementById('detallesProveedor').innerHTML = 'Detalles del proveedor con ID: ' + proveedorId;
                document.getElementById('modalProveedor').style.display = 'block';
            });
        });
    });

    function cerrarModal() {
        document.getElementById('modalProveedor').style.display = 'none';
    }
</script>


</body>
</html>
