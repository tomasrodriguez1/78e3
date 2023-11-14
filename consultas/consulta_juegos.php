<?php include('../templates/header.html'); ?>

<body>
<?php
  # Llama a conexión, crea el objeto PDO y obtiene la variable $db
  require("../config/conexion.php");

  # Se construye la consulta como un string
  $query = "SELECT v.titulo AS juego, p.nombre AS proveedor 
  FROM videojuegos v 
  JOIN proveedores_videojuegos pv ON v.id_videojuego = pv.id_videojuego 
  JOIN proveedores p ON pv.id = p.id;";

  # Se prepara y ejecuta la consulta. Se obtienen TODOS los resultados
  $result = $db -> prepare($query);
  $result -> execute();
  $juegos = $result -> fetchAll();
?>

<div class="table-container">
  <table class="center">
    <tr>
      <th>Nombre del Juego</th>
      <th>Proveedor</th>
    </tr>
  
    <?php
      foreach ($juegos as $j) {
        echo "<tr>
                <td>$j[juego]</td>
                <td>$j[proveedor]</td>
              </tr>";
      }
    ?>
  </table>
</div>

<?php include('../templates/footer.html'); ?>
