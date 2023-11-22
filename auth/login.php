<?php
// Asegúrate de iniciar las sesiones en la parte superior de tu script
session_start();

// Verifica si hay datos de POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("data/conexion.php"); // Incluye tu archivo de conexión a la base de datos

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Utiliza consultas preparadas para evitar inyecciones SQL
    $sql = "SELECT * FROM Usuarios WHERE username = ?";
    $stmt = $db2->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        // Usuario autenticado correctamente
        $_SESSION['user_id'] = $row['id']; // Asigna el ID del usuario a la sesión
        $_SESSION['username'] = $username;

        // Realiza las operaciones adicionales necesarias o redirige al usuario
        header("Location: pagina_principal.php"); // Redirige a la página principal
        exit;
    } else {
        // Credenciales incorrectas
        // Guarda el error en la sesión o maneja como prefieras
        $_SESSION['error'] = "Credenciales incorrectas.";
        header("Location: index.php"); // Redirige de nuevo al formulario de inicio de sesión
        exit;
    }
} else {
    // Si no hay datos POST, redirige al formulario de inicio de sesión o maneja como prefieras
    header("Location: index.php");
    exit;
}
?>

