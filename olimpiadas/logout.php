<?php
session_start();
session_unset();      // Limpia todas las variables de sesión
session_destroy();    // Destruye la sesión

// Redirige al index con un mensaje de éxito
header("Location: index.php?cerrado=1");
exit;
