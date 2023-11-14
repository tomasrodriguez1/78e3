<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Se obtiene el valor del input del usuario
  $username = $_POST["username"];

  # Se construye la consulta como un string
  $query = "SELECT v.titulo AS juego, pr.nombre AS proveedor
  FROM usuarios u
  JOIN usuario_proveedor up ON u.id_usuario = up.id_usuario
  JOIN proveedores pr ON up.id_proveedor = pr.id
  LEFT JOIN proveedores_videojuegos_pre pp ON pr.id = pp.id
  LEFT JOIN videojuegos v ON pp.id_videojuego = v.id_videojuego
  WHERE u.username = ?";

  # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
  $result = $db -> prepare($query);
  $result -> execute([$username]);
  $juegos = $result -> fetchAll();
?>

  <?php
    if (count($juegos) > 0) {
      echo "<table>
              <tr>
                <th>Nombre del Juego</th>
                <th>Proveedor</th>
              </tr>";
      foreach ($juegos as $juego) {
        echo "<tr><td>$juego[0]</td><td>$juego[1]</td></tr>";
      }
      echo "</table>";
    } else {
      echo "No se encontraron juegos para el usuario ingresado.";
    }
  ?>

<?php include('../templates/footer.html'); ?>
