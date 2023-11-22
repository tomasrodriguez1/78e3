<html>
<head>
    <title>Registro de Usuario</title>
</head>
<body>
    <h1 align="center">Registro de Usuario</h1>
    <div align="center">
        <form method="post" action="auth/registro.php">
            <label for="username">Nombre de Usuario:</label><br>
            <input type="text" id="username" name="username" required><br>

            <label for="nombre">Nombre Completo:</label><br>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="email">Correo Electrónico:</label><br>
            <input type="email" id="email" name="email" required><br>

            <label for="fechaNacimiento">Fecha de Nacimiento:</label><br>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" required><br>

            <label for="password">Contraseña:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Registrarse">
        </form>
    </div>
</body>
</html>