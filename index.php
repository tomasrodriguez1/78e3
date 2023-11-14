<?php include('./templates/header.html'); ?>

<body>
    <h1 align="center"> BIBLIOTECA DE VIDEOJUEGOS Y PELICULAS</h1>
    <p style="text-align:center;"> En esta plataforma podrás encontrar todas las películas y juegos que desees en distintios proveedores y distintos proveedores</p>

    <br>

    <h3 align="center">Aprieta este boton para ver los proveedores que tienen todos los videojuegos</h3>

    <form action="./consultas/consulta_juegos.php" method="post">
        <input type="submit" value="Mostrar Juegos y Proveedores">
    </form>

    <br>
    <br>

    <h3 align="center">Ingresa el numero de reseñas positivas y encontraras los juegos con esa cantidad o mas de reseñas positivas</h3>

    <form action="./consultas/consulta_resenas.php" method="post">
        <label for="numero_resenas">Ingrese el número de reseñas positivas:</label>
        <input type="text" id="numero_resenas" name="numero_resenas">
        <input type="submit" value="Buscar Juegos">
    </form>

    <br>
    <br>

    <h3 align="center">Ingresa el juego que quieras y aparecerá los proveedores que tiene</h3>

    <form action="./consultas/consulta_proveedores.php" method="post">
        <label for="nombre_juego">Ingrese el nombre del juego:</label>
        <input type="text" id="nombre_juego" name="nombre_juego">
        <input type="submit" value="Buscar Proveedores">
    </form>

    <br>
    <br>

    <h3 align="center">Aprieta el genero que quieras buscar y aparecerán los juegos que tiene</h3>

    <?php
    # Llama a conexión, crea el objeto PDO y obtiene la variable $db
    require("config/conexion.php");

    # Se construye la consulta para obtener todos los géneros distintos
    $query_generos = "SELECT DISTINCT nombre AS nombre_genero FROM Videojuego_genero;";
    $result_generos = $db->prepare($query_generos);
    $result_generos->execute();
    $generos = $result_generos->fetchAll();
    ?>

    <form action="./consultas/consulta_genero.php" method="post">
        <label for="genero">Seleccione un género:</label>
        <select id="genero" name="genero">
            <?php
            foreach ($generos as $genero) {
                echo "<option value=\"$genero[nombre_genero]\">$genero[nombre_genero]</option>";
            }
            ?>
        </select>
        <input type="submit" value="Buscar">
    </form>

    <br>
    <br>

    <h3 align="center">Ingresa el nombre de usuario a buscar y te aparecerán los juegos que tiene</h3>

    <form action="./consultas/consulta_usuario_juego.php" method="post">
        <label for="username">Ingrese el nombre de usuario:</label>
        <input type="text" id="username" name="username">
        <input type="submit" value="Buscar Juegos y Proveedores">
    </form>

    <br>
    <br>

    <h3 align="center">Ingresa el nombre de usuario a buscar y te aparecerá sus preordenes</h3>

    <form action="./consultas/consulta_usuario_preorden.php" method="post">
        <label for="username2">Ingrese el nombre de usuario:</label>
        <input type="text" id="username2" name="username2">
        <input type="submit" value="Buscar Proveedores con Preordenes">
    </form>

    <br>
    <br>

    <h3 align="center">Aprieta este boton para ver el gasto total de todos los usuarios en juegos por subscripción</h3>

    <form action="./consultas/consulta_gasto_subscripcion.php" method="get">
        <input type="submit" value="Ver gasto total de cada usuario por subscripción">
    </form>

    <br>
    <br>
</body>
</html>
