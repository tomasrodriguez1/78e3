<?php
$username = $_POST['username'];
$password = $_POST['password'];
# Busqueda del usuario en la base de datos
$sql = "SELECT * FROM Usuarios WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if ($row && password_verify($hashedPassword, $row['password'])) {

# Encriptar la contraseña y ver si coincide con la encriptacion de la base de datos
#if ($row && password_verify($password, $row['password'])) {
    // Usuario autenticado correctamente

    // Mostrar la información desde una vista materializada
    $sql = "SELECT * FROM VistaInformacionUsuario WHERE username = '$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Mostrar la información en la página Mi Perfil
    echo "Nombre: " . $row['nombre'] . "<br>";
    echo "Email: " . $row['email'] . "<br>";
    // ...

} else {
    // Credenciales incorrectas, mostrar mensaje de error o redirigir a página de inicio de sesión
    echo "Credenciales incorrectas.";
}
?>
