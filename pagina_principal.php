

<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require("data/conexion.php");

session_start();

// Verificar si el usuario ya está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proveedor = '%' . $_POST['proveedor'] . '%';
    $nombre = '%' . $_POST['nombre_videojuego'] . '%';

    try {
        $sql = "SELECT p.nombre AS nombre_proveedor, 'Película' AS tipo, peli.titulo AS titulo,
                        CASE WHEN pp.pid IS NOT NULL THEN 'Incluido' ELSE 'No Incluido' END AS estado
                FROM Proveedores AS p
                LEFT JOIN ProveedoresPeliculas AS pp ON p.id = pp.pro_id
                LEFT JOIN Peliculas AS peli ON pp.pid = peli.pid
                WHERE LOWER(p.nombre) LIKE LOWER(:proveedor)
                AND LOWER(peli.titulo) LIKE LOWER(:nombre)
                UNION
                SELECT p.nombre, 'Serie', ser.nombre AS titulo,
                        CASE WHEN ps.sid IS NOT NULL THEN 'Incluido' ELSE 'No Incluido' END AS estado
                FROM Proveedores AS p
                LEFT JOIN ProveedoresSeries AS ps ON p.id = ps.pro_id
                LEFT JOIN Series AS ser ON ps.sid = ser.sid
                WHERE LOWER(p.nombre) LIKE LOWER(:proveedor)
                AND LOWER(ser.nombre) LIKE LOWER(:nombre)
                ";
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
            echo " - Estado: " . htmlspecialchars($row['estado']);
            echo "</div>";
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
function obtenerTopVisualizaciones($db2, $proveedorId) {
    $resultados = [
        'peliculas' => [],
        'series' => []
    ];

    // Consulta para obtener las películas más vistas por proveedor
    $sqlPeliculas = "SELECT p.pid, p.titulo, COUNT(vp.pid) AS visualizaciones
                    FROM ProveedoresPeliculas pp
                    JOIN Peliculas p ON pp.pid = p.pid
                    LEFT JOIN VisualizacionesPeliculas vp ON p.pid = vp.pid
                    WHERE pp.pro_id = :proveedorId
                    GROUP BY p.pid
                    ORDER BY visualizaciones DESC
                    LIMIT 3;
                    ";

    try {
        $stmtPeliculas = $db2->prepare($sqlPeliculas);
        $stmtPeliculas->bindParam(':proveedorId', $proveedorId, PDO::PARAM_INT);
        $stmtPeliculas->execute();
        $resultados['peliculas'] = $stmtPeliculas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener películas más vistas: " . $e->getMessage());
    }

    // Consulta para obtener las series más vistas por proveedor
    $sqlSeries = "SELECT s.sid, s.nombre, SUM(vc.visualizaciones) AS visualizaciones_totales
                    FROM ProveedoresSeries ps
                    JOIN Series s ON ps.sid = s.sid
                    JOIN Capitulos c ON s.sid = c.sid
                    LEFT JOIN (SELECT cid, COUNT(*) as visualizaciones FROM VisualizacionesCapitulos GROUP BY cid) vc ON c.cid = vc.cid
                    WHERE ps.pro_id = :proveedorId
                    GROUP BY s.sid
                    ORDER BY visualizaciones_totales DESC
                    LIMIT 3;
                    ";

    try {
        $stmtSeries = $db2->prepare($sqlSeries);
        $stmtSeries->bindParam(':proveedorId', $proveedorId, PDO::PARAM_INT);
        $stmtSeries->execute();
        $resultados['series'] = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener series más vistas: " . $e->getMessage());
    }
    return json_encode($resultados);
}
if (isset($_GET['accion']) && $_GET['accion'] == 'obtenerTopVisualizaciones' && isset($_GET['proveedorId'])) {
    header('Content-Type: application/json');
    echo obtenerTopVisualizaciones($db2, $_GET['proveedorId']);
    exit; 
}
include('./templates/header.html'); 
?>


<body>
    <div class="content-container">
        <!-- INICIO DE LA PAGINA -->

        <!-- SECCION: Barra de Navegación -->
        <div style="text-align: center;"> <!-- Contenedor para centralizar -->
            <div class="navbar">
                <a href="paginas/perfil_usuario.php">Mi Perfil</a>
                <a href="paginas/one_time_purchases.php">One Time Purchases</a>
                <a href="paginas/consulta_inestructurada.php">Consulta Inestructurada</a>
            </div>
        </div>

        <br>
        <br>
        <h1 align="center"> Videojuegos</h1>
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
        <br>
            <!-- Inicio de la Sección de Proveedores de Videojuegos -->
        <h4 align="center">Proveedores de Videojuegos</h4>
        <div class="proveedores-container">
            <?php
                try {
                    $sql = "SELECT p.id, p.nombre,
                                (SELECT COUNT(*) FROM proveedores_videojuegos WHERE proveedores_videojuegos.id = p.id) as totalvideojuegos
                            FROM proveedores p";
                    $stmt = $db->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='proveedor-bloque-videojuego' data-id='" . htmlspecialchars($row['id']) . "' data-nombre='" . htmlspecialchars($row['nombre']) . "' data-totalvideojuegos='" . htmlspecialchars($row['totalvideojuegos']) . "'>" . htmlspecialchars($row['nombre']). "</div>";
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            ?>
        </div>
        <br>
        <br>
        <h1 align="center"> Películas y Series</h1>
        <br>
        <br>       
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
        <br>   
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
        <div class="logout-button">
            <a href="auth/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
        <!-- FIN DE LA PAGINA -->

            <!-- Modal para detalles del proveedor -->
            <div id="modalProveedorVideojuegos" style="display:none;">
            <div id="detallesProveedorVideojuegos"></div>
            <br>
            <button onclick="cerrarModalVideojuegos()">Cerrar</button>
            </div>

            <!-- Modal para detalles del proveedor de películas y series -->
            <div id="modalProveedorPeliculasSeries" style="display:none;">
                <div id="detallesProveedorPeliculasSeries"></div>
                <br>
                <button onclick="cerrarModalPeliculasSeries()">Cerrar</button>
            </div>

        <!-- Código JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Eventos para proveedores de videojuegos
                document.querySelectorAll('.proveedor-bloque-videojuego').forEach(item => {
                    item.addEventListener('click', function() {
                        var idVideojuegoProveedor = this.getAttribute('data-id');
                        var nombreVideojuegoProveedor = this.getAttribute('data-nombre');
                        var totalVideojuegos = this.getAttribute('data-totalvideojuegos');
                        
                        var detallesHTML2 = "<h3>" + nombreVideojuegoProveedor + "</h3>" +
                                        "<p>Total de Videojuegos: " + totalVideojuegos + "</p>";
                        
                        document.getElementById('detallesProveedorVideojuegos').innerHTML = detallesHTML2;
                        document.getElementById('modalProveedorVideojuegos').style.display = 'block';
                    });
                });

                // Eventos para proveedores de películas y series
                document.querySelectorAll('.proveedor-bloque').forEach(item => {
                    item.addEventListener('click', function() {
                        var idProveedor = this.getAttribute('data-id');
                        var nombreProveedor = this.getAttribute('data-nombre');
                        var costoProveedor = this.getAttribute('data-costo');
                        var peliculasTotal = this.getAttribute('data-totalpeliculas');
                        var seriesTotal = this.getAttribute('data-totalseries');

                        // Mostrar información básica del proveedor en el modal
                        var detallesHTML = "<h3>" + nombreProveedor + "</h3>" +
                                        "<p>Costo: $" + costoProveedor + "</p>" +
                                        "<p>Total de Películas: " + peliculasTotal + "</p>" +
                                        "<p>Total de Series: " + seriesTotal + "</p>";

                        fetch(window.location.href + '?accion=obtenerTopVisualizaciones&proveedorId=' + idProveedor)
                            .then(response => {
                                if (response.headers.get("content-type").includes("application/json")) {
                                    return response.json();
                                } else {
                                    throw new Error('No es JSON');
                                }
                            })
                            .then(data => {
                                var peliculasHTML = "<h4>Películas más vistas</h4>";
                                data.peliculas.forEach(function(pelicula) {
                                    peliculasHTML += "<p>" + pelicula.titulo + " - Visualizaciones: " + pelicula.visualizaciones + "</p>";
                                });

                                var seriesHTML = "<h4>Series más vistas</h4>";
                                data.series.forEach(function(serie) {
                                    seriesHTML += "<p>" + serie.nombre + " - Visualizaciones Totales: " + serie.visualizaciones_totales + "</p>";
                                });

                                detallesHTML += peliculasHTML + seriesHTML;
                                document.getElementById('detallesProveedorPeliculasSeries').innerHTML = detallesHTML;
                                document.getElementById('modalProveedorPeliculasSeries').style.display = 'block';
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.getElementById('detallesProveedorPeliculasSeries').innerHTML = detallesHTML + "<p>Error al cargar las visualizaciones.</p>";
                                document.getElementById('modalProveedorPeliculasSeries').style.display = 'block';
                            });
                    });
                });

            });

            function cerrarModalVideojuegos() {
                document.getElementById('modalProveedorVideojuegos').style.display = 'none';
            }

            function cerrarModalPeliculasSeries() {
                document.getElementById('modalProveedorPeliculasSeries').style.display = 'none';
            }

        </script>
    </div>

</body>
</html>
