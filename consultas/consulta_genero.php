<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Verificamos si se ha enviado el género seleccionado por el usuario
  if (isset($_POST["genero"])) {
    $id_genero = $_POST["genero"];

    # Consulta SQL para obtener juegos del género seleccionado y sus proveedores
    $query = "SELECT DISTINCT v.titulo AS juego, p.nombre AS proveedor
              FROM videojuego_genero vg
              JOIN videojuegos v ON vg.id_videojuego = v.id_videojuego
              LEFT JOIN proveedores_videojuegos_pre pp ON v.id_videojuego = pp.id_videojuego
              LEFT JOIN proveedores p ON pp.id = p.id
              WHERE vg.nombre = :id_genero
                 OR vg.nombre IN (SELECT subgenero FROM genero_subgenero WHERE genero = :id_genero);";

    # Se prepara y ejecuta la consulta
    $result = $db->prepare($query);
    $result->bindParam(':id_genero', $id_genero);
    $result->execute();
    $juegos = $result->fetchAll();
  }
?>

<?php
  # Mostrar resultados si hay juegos disponibles
  if (isset($juegos) && count($juegos) > 0) {
    echo "<table>
            <tr>
              <th>Nombre del Juego</th>
              <th>Proveedor</th>
            </tr>";
    foreach ($juegos as $juego) {
      echo "<tr><td>$juego[juego]</td><td>$juego[proveedor]</td></tr>";
    }
    echo "</table>";
  } elseif (isset($_POST["genero"])) {
    echo "No se encontraron juegos para el género seleccionado.";
  }
?>

<?php include('../templates/footer.html'); ?>