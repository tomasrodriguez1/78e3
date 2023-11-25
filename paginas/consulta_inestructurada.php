<?php
require("../data/conexion.php");
include('../templates/header.html');
session_start();

$tipoDB = "";
$resultados = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['peliculas_series'])) {
        $tipoDB = "db2"; // Asegúrate de que este valor sea consistente
    } elseif (isset($_POST['videojuegos'])) {
        $tipoDB = "videojuegos"; // Y este también
    }

    if (isset($_POST['consulta'])) {
        $atributos = $_POST['atributos']; // Considera validar y limpiar estos datos
        $tabla = $_POST['tabla']; // Ídem
        $criterio = $_POST['criterio']; // Ídem
        $consulta = "SELECT $atributos FROM $tabla WHERE $criterio"; // Vulnerable a inyección SQL

        try {
            $dbUsed = ($tipoDB == "db2") ? $db2 : $db; // Simplifica el uso de las bases de datos
            $stmt = $dbUsed->prepare($consulta); // Utiliza sentencias preparadas
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    }
}
?>

<body>
    <div style="text-align: center;">
        <div class="navbar">
            <a href="perfil_usuario.php">Mi Perfil</a>
            <a href="../pagina_principal.php">Pagina Principal</a>
        </div>
    </div>

    <br><br>

    <!-- Formulario para elegir tipo de base de datos -->
    <form method="post">
        <button type="submit" name="peliculas_series">Películas y Series</button>
        <button type="submit" name="videojuegos">Videojuegos</button>
    </form>

    <!-- Formulario para realizar consulta -->
    <?php if ($tipoDB != ""): ?>
        <form method="post">
            Atributos: <input type="text" name="atributos" required><br>
            Nombre de la Tabla: <input type="text" name="tabla" required><br>
            Criterio (WHERE clause): <input type="text" name="criterio"><br>
            <input type="hidden" name="tipoDB" value="<?php echo htmlspecialchars($tipoDB); ?>">
            <input type="hidden" name="consulta">
            <input type="submit" value="Realizar Consulta">
        </form>
    <?php endif; ?>

    <!-- Mostrar resultados -->
    <?php
    if ($resultados) {
        echo "<h2>Resultados:</h2>";
        echo "<table border='1'>";
        foreach ($resultados as $fila) {
            echo "<tr>";
            foreach ($fila as $columna) {
                echo "<td>" . htmlspecialchars($columna) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>