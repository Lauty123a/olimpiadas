<?php  
// login.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "agencia_viaje";

$conn = new mysqli($host, $usuario, $contrasena, $basedatos);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT id_usuario, password_hash, id_rol, nombre FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if ($contrasena === $usuario['password_hash']) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['rol'] = $usuario['id_rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            if (isset($_SESSION['volver_a_pagar'])) {
              unset($_SESSION['volver_a_pagar']);
              header("Location: pagar.php");
              exit;
          }
          
          if ($usuario['id_rol'] == 1) {
              header("Location: panel_encargado.php");
              exit;
          } else {
              header("Location: index.php");
              exit;
          }
          
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="estilos_login.css" />
</head>
<body>
  <div class="formulario-container">
    <h2>Iniciar Sesión</h2>

    <form action="" method="POST">
      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" required />

      <label for="contrasena">Contraseña:</label>
      <input type="password" id="contrasena" name="contrasena" required />

      <?php if (isset($_GET['cerrado']) && $_GET['cerrado'] == 1): ?>
        <div class="mensaje-exito">
          ✅ Sesión cerrada con éxito.
        </div>
      <?php endif; ?>

      <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <input type="submit" value="Ingresar" />
    </form>

    <p class="link-opcion">
      ¿No tenés cuenta? <a href="registro.php">Registrate</a>
    </p>
  </div>
</body>
</html>
