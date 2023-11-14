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
