<?php
# Obtener los datos del formulario de registro
$username = $_POST['username'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$password = $_POST['password'];

# Verificar si el username ya existe en la base de datos
$sql = "SELECT COUNT(*) as count FROM Usuarios WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    # El username ya existe, mostrar mensaje de error o redirigir a página de registro
    echo "El username ya está en uso.";
} else {
    # Obtener el sucesor del mayor id del sistema
    $sql = "SELECT MAX(id) as max_id FROM Usuarios";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nuevoId = $row['max_id'] + 1;

    # Insertar el nuevo usuario en la tabla Usuarios
    # Se debe ingresar la contraseña ENCRIPTADA --> Falta por hacer
    $sql = "INSERT INTO Usuarios (id, username, nombre, email, fecha_nacimiento, password) 
            VALUES ('$nuevoId', '$username', '$nombre', '$email', '$fechaNacimiento', '$password')";
    $conn->query($sql);


    # Falta ver el tema de los permisos
    
    // Redirigir a la página de inicio de sesión u otra página
    header("Location: login.php");
    exit();
}
?>
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