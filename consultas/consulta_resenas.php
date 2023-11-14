<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Se obtiene el valor del input del usuario
  $n = $_POST["numero_resenas"];
  $n = intval($n);

  # Se construye la consulta como un string
  $query = "SELECT v.titulo AS juego, COUNT(ur.veredicto) AS reseñas_positivas 
  FROM videojuegos v 
  JOIN usuarios_resenas ur ON v.id_videojuego = ur.id_videojuego 
  WHERE ur.veredicto = 'positivo'  
  GROUP BY v.titulo 
  HAVING COUNT(ur.veredicto) >= $n;";

  # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
  $result = $db -> prepare($query);
  $result -> execute();
  $juegos = $result -> fetchAll();
?>

  <?php
    if (count($juegos) > 0) {
      echo "<table>
              <tr>
                <th>Nombre del Juego</th>
              </tr>";
      foreach ($juegos as $juego) {
        echo "<tr><td>$juego[0]</td></tr>";
      }
      echo "</table>";
    } else {
      echo "No hay ningún juego con esa cantidad de reseñas positivas.";
    }
  ?>

<?php include('../templates/footer.html'); ?>
