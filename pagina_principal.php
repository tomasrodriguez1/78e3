

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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proveedor = '%' . $_POST['proveedor'] . '%';
    $nombre = '%' . $_POST['nombre_videojuego'] . '%';

    try {
        $sql = "SELECT p.nombre AS nombre_proveedor, 'Película' AS tipo, peli.titulo AS titulo
                FROM Proveedores AS p
                LEFT JOIN ProveedoresPeliculas AS pp ON p.id = pp.pro_id
                LEFT JOIN Peliculas AS peli ON pp.pid = peli.pid
                WHERE LOWER(p.nombre) LIKE LOWER(:proveedor)
                AND LOWER(peli.titulo) LIKE LOWER(:nombre)
                UNION
                SELECT p.nombre, 'Serie', ser.nombre AS titulo
                FROM Proveedores AS p
                LEFT JOIN ProveedoresSeries AS ps ON p.id = ps.pro_id
                LEFT JOIN Series AS ser ON ps.sid = ser.sid
                WHERE LOWER(p.nombre) LIKE LOWER(:proveedor)
                AND LOWER(ser.nombre) LIKE LOWER(:nombre)";
        $stmt = $db2->prepare($sql);
        $stmt->bindParam(':proveedor', $proveedor, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as $row) {
            echo "<div>";
            echo "Proveedor: " . htmlspecialchars($row['nombre_proveedor']);
            echo " - Tipo: " . htmlspecialchars($row['tipo']);
            echo " - Título: " . htmlspecialchars($row['titulo']);
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<body>

    <!-- INICIO DE LA PAGINA -->

    <!-- SECCION: Barra de Navegación -->
    <div style="text-align: center;"> <!-- Contenedor para centralizar -->
        <div class="navbar">
            <a href="paginas/perfil_usuario.php">Mi Perfil</a>
            <a href="paginas/one_time_purchases.php">One Time Purchases</a>
            <a href="consulta_inestructurada.php">Consulta Inestructurada</a>
            <!-- Agrega aquí más enlaces según necesites -->
        </div>
    </div>

    <br>
    <br>
    <h4 align="center"> Suscripciones de Videojuegos</h4>
    <br>
    <!-- Formulario de Búsqueda -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="formulario-busqueda">
        <div class="inputs-container">
            <input type="text" name="proveedor" placeholder="Nombre del Proveedor">
            <input type="text" name="nombre_videojuego" placeholder="Nombre del Videojuego">
        </div>
        <input type="submit" class="btn-logout" value="Buscar">
    </form>


    <!-- Procesar el formulario y obtener resultados -->
    <h4 align="center"> Suscripciones de Películas y Series</h4>
    <br>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="formulario-busqueda">
        <div class="inputs-container">
            <input type="text" name="proveedor" placeholder="Nombre del Proveedor">
            <input type="text" name="nombre_videojuego" placeholder="Nombre de la Serie o Película">
        </div>
        <input type="submit" class="btn-logout" value="Buscar">
    </form>

    <!-- Mostrar cada proveedor en su rectangulo -->
    <h4 align="center"> Proveedores de Películas y Series</h4>
    <div class="proveedores-container">
        <?php
            try {
                $sql = "SELECT Proveedores.id, Proveedores.nombre, Proveedores.costo, 
                            (SELECT COUNT(*) FROM ProveedoresPeliculas WHERE ProveedoresPeliculas.pro_id = Proveedores.id) as totalpeliculas,
                            (SELECT COUNT(*) FROM ProveedoresSeries WHERE ProveedoresSeries.pro_id = Proveedores.id) as totalseries
                        FROM Proveedores";
                $stmt = $db2->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='proveedor-bloque' data-id='" . htmlspecialchars($row['id']) . "' data-nombre='" . htmlspecialchars($row['nombre']) . "' data-costo='" . htmlspecialchars($row['costo']) . "' data-totalpeliculas='" . htmlspecialchars($row['totalpeliculas']) . "' data-totalseries='" . htmlspecialchars($row['totalseries']) . "'>" . htmlspecialchars($row['nombre']) . "</div>";
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
                    var proveedorNombre = this.getAttribute('data-nombre');
                    var proveedorCosto = this.getAttribute('data-costo');
                    var totalPeliculas = this.getAttribute('data-totalpeliculas');
                    var totalSeries = this.getAttribute('data-totalseries');
                    
                    var detallesHTML = "<h3>" + proveedorNombre + "</h3>";
                    detallesHTML += "<p>Costo: $" + proveedorCosto + "</p>";
                    detallesHTML += "<p>Total de Películas: " + totalPeliculas + "</p>";
                    detallesHTML += "<p>Total de Series: " + totalSeries + "</p>";
                    
                    document.getElementById('detallesProveedor').innerHTML = detallesHTML;
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
