<?php 
include('./templates/header.html'); 
require("data/conexion.php");

session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: pagina_principal.php'); // Redirigir a la página principal
    exit;
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta preparada para evitar inyecciones SQL
    $sql = "SELECT * FROM Usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        // Usuario autenticado correctamente
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $username;

        // Redirigir a otra página, por ejemplo, un perfil de usuario
        header("Location: pagina_principal.php");
        exit;
    } else {
        // Credenciales incorrectas
        $_SESSION['error'] = "Credenciales incorrectas.";
        header("Location: index.php");
        exit;
    }
}
?>

<body>
    <h1 align="center"> BIBLIOTECA DE VIDEOJUEGOS Y PELICULAS</h1>
    <p style="text-align:center;">En esta plataforma podrás encontrar todas las películas y juegos que desees en distintos proveedores</p>

    <?php if ($error): ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <div align="center">
        <form method="post" action="index.php">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Iniciar Sesión">
        </form>
    </div>
</body>
</html>
