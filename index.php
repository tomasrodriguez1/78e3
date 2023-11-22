<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('./templates/header.html'); 
require("data/conexion.php");

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: pagina_principal.php');
    exit;
}

$errorUsername = '';
$errorPassword = '';
$formSubmitted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formSubmitted = true;
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verificación de acceso manual para 'pp' con contraseña 'pp'
    if ($username === 'pp' && $password === 'pp') {
        $_SESSION['user_id'] = 'manual_access';
        $_SESSION['username'] = 'pp';
        header("Location: pagina_principal.php");
        exit;
    }

    $sql = "SELECT * FROM Usuarios WHERE username = :username";
    $stmt = $db2->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        // Usuario encontrado, verificar contraseña
        if (password_verify($password, $row['password'])) {
            // La contraseña es correcta, iniciar sesión
            $_SESSION['user_id'] = $row['id_usuario']; // Asumiendo que la tabla tiene un campo 'id'
            $_SESSION['username'] = $username;
            header("Location: pagina_principal.php");
            exit;
        } else {
            // La contraseña no coincide
            $errorPassword = "Contraseña incorrecta.";
        }
    } else {
        // Usuario no encontrado
        $errorUsername = "Nombre de usuario no encontrado.";
    }
}
?>

<body>
    <h1 align="center"> VLOKVASTER</h1>
    <p style="text-align:center;">En esta plataforma podrás encontrar todas las películas y juegos que desees en distintos proveedores</p>

    <div align="center">
        <form method="post" action="index.php">
            <?php if ($formSubmitted && !empty($errorUsername)): ?>
                <p style="color:red;"><?php echo $errorUsername; ?></p>
            <?php endif; ?>
            <?php if ($formSubmitted && !empty($errorPassword)): ?>
                <p style="color:red;"><?php echo $errorPassword; ?></p>
            <?php endif; ?>

            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Iniciar Sesión">
        </form>

        <!-- Sección de registro -->
        <p>Si no tienes un usuario registrado, <a href="paginas/registro_frontend.php">regístrate</a>.</p>
    </div>
</body>
</html>
