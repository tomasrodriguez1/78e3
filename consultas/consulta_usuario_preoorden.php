<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Verifica si el valor del input del usuario existe
  if (isset($_POST["username2"])) {
      $username2 = $_POST["username2"];

      # Se construye la consulta como un string
      $query = "SELECT p.nombre AS proveedor
                FROM usuarios u
                JOIN pago_no_subscripcion pns ON u.id_usuario = pns.id_usuario
                JOIN proveedores p ON p.id = pns.id_proveedor
                WHERE u.username = ? AND pns.preorden = true
                GROUP BY p.nombre
                HAVING COUNT(DISTINCT pns.pago_id) > 1;";

      # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
      $result = $db -> prepare($query);
      $result -> execute([$username2]);
      $proveedores = $result -> fetchAll();

      if (count($proveedores) > 0) {
          echo "<table>
                  <tr>
                    <th>Proveedor</th>
                  </tr>";
          foreach ($proveedores as $proveedor) {
            echo "<tr><td>$proveedor[0]</td></tr>";
          }
          echo "</table>";
      } else {
          echo "El usuario ingresado no ha preordenado más de un juego para ningún proveedor.";
      }
  } else {
      echo "El nombre de usuario no existe.";
  }
?>

<?php include('../templates/footer.html'); ?>
