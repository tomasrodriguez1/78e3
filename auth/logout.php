<?php

    session_start();

    // Vaciar todas las variables de sesión
    $_SESSION = array();

    // Destruir la sesión
    session_destroy();

    // Redirigir al usuario al login (index.php)
    header("Location: ../index.php");
    exit;

?>
