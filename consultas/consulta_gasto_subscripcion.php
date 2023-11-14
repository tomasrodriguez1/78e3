<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Se construye la consulta como un string
  $query = "SELECT ps.id_usuario, SUM(ps.monto) AS gasto_total
            FROM pago_subscripcion ps
            GROUP BY ps.id_usuario
            ORDER BY SUM(ps.monto) DESC;";

  # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
  $result = $db -> prepare($query);
  $result -> execute();
  $gastos = $result -> fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-container">
  <table class="center">
    <tr>
      <th>ID Usuario</th>
      <th>Gasto Total por Suscripción</th>
    </tr>
    <?php
      foreach ($gastos as $gasto) {
        echo "<tr><td>{$gasto['id_usuario']}</td><td>{$gasto['gasto_total']}</td></tr>";
      }
    ?>
  </table>
</div>


<?php include('../templates/footer.html'); ?>
