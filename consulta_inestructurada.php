<?php
require("../data/conexion.php");
include('../templates/header.html');
session_start();
?>

<?php
$tipoDB = "";
$resultados = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['peliculas_series'])) {
        $tipoDB = "db2";
    } elseif (isset($_POST['videojuegos'])) {
        $tipoDB = "videojuegos";
    }

    if (isset($_POST['consulta'])) {
        $atributos = $_POST['atributos']; 
        $tabla = $_POST['tabla'];
        $criterio = $_POST['criterio']; 
        $consulta = "SELECT $atributos FROM $tabla WHERE $criterio";

        try {
          
            if ($tipoDB == "db"){
                $stmt = $db->query($consulta);
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {    
                $stmt = $db2->query($consulta);
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    }
}
?>

<form method="post">
    <button type="submit" name="peliculas_series">Películas y Series</button>
    <button type="submit" name="videojuegos">Videojuegos</button>
</form>

<?php if ($tipoDB != ""): ?>
    <form method="post">
        Atributos: <input type="text" name="atributos" required><br>
        Nombre de la Tabla: <input type="text" name="tabla" required><br>
        Criterio (WHERE clause): <input type="text" name="criterio"><br>
        <input type="hidden" name="tipoDB" value="<?php echo $tipoDB; ?>">
        <input type="hidden" name="consulta">
        <input type="submit" value="Realizar Consulta">
    </form>
<?php endif; ?>

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
