<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Se obtiene el valor del input del usuario
  $nombre_juego = $_POST["nombre_juego"];

  # Se construye la consulta como un string
  $query = "SELECT v.titulo AS juego, p.nombre AS proveedor 
  FROM videojuegos v 
  JOIN proveedores_videojuegos pv ON v.id_videojuego = pv.id_videojuego 
  JOIN proveedores p ON pv.id= p.id 
  WHERE v.titulo LIKE ?;";

  # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
  $result = $db -> prepare($query);
  $nombre_juego_wildcard = "%" . $nombre_juego . "%";  # Agrega los caracteres comodín alrededor del nombre del juego
  $result -> execute([$nombre_juego_wildcard]);
  $proveedores = $result -> fetchAll();
?>

  <?php
    if (count($proveedores) > 0) {
      echo "<table>
              <tr>
                <th>Nombre del Juego</th>
                <th>Proveedor</th>
              </tr>";
      foreach ($proveedores as $proveedor) {
        echo "<tr><td>$proveedor[0]</td><td>$proveedor[1]</td></tr>";
      }
      echo "</table>";
    } else {
      echo "No se encontró el juego o no tiene proveedores asociados.";
    }
  ?>

<?php include('../templates/footer.html'); ?>
