<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../templates/header.html'); 
require("../data/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    # Obtener los datos del formulario de registro
    $username = $_POST['username'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $password = $_POST['password'];

    # Verificar si el username ya existe en la base de datos
    $stmt = $db2->prepare("SELECT COUNT(*) as count FROM Usuarios WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        # El username ya existe, mostrar mensaje de error
        echo "El username ya está en uso.";
    } else {
        $sql = "SELECT MAX(id_usuario) as max_id FROM Usuarios";
        $result = $db2->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $nuevoId = $row['max_id'] + 1;
        # Encriptar contraseña
        $passwordEncriptada = password_hash($password, PASSWORD_DEFAULT);

        # Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO Usuarios (id_usuario, username, nombre, email, fecha_nacimiento, password) 
            VALUES ('$nuevoId', '$username', '$nombre', '$email', '$fechaNacimiento', '$passwordEncriptada')";

        # Redirigir a la página de inicio de sesión
        header("Location: ../index.php");
        exit();
    }
}
?>
<html>
<head>
    <title>Registro de Usuario</title>
</head>
<body>
    <h1 align="center">Registro de Usuario</h1>
    <div align="center">
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
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