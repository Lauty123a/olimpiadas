<?php
// registro.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "agencia_viaje";

$conn = new mysqli($host, $usuario, $contrasena, $basedatos);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $pass = $_POST['contrasena'] ?? '';
    $id_rol = 2;

    // Verificar si email ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "❌ Ya existe un usuario con este email.";
        $stmt->close();
    } else {
        $stmt->close();

        $stmt2 = $conn->prepare("INSERT INTO usuario (email, password_hash, nombre, apellido, id_rol) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("ssssi", $email, $pass, $nombre, $apellido, $id_rol);

        if ($stmt2->execute()) {
            $mensaje = "✅ Usuario registrado exitosamente. <a href='login.php'>Iniciar sesión</a>";
        } else {
            $mensaje = "❌ Error al registrar el usuario: " . $stmt2->error;
        }

        $stmt2->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <link rel="stylesheet" href="estilos_login.css">
</head>
<body>

<div class="container transparent-box">
  <h2>Registrar Usuario</h2>

  <?php if ($mensaje): ?>
    <div class="mensaje"><?= $mensaje ?></div>
  <?php endif; ?>

  <form action="registro.php" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required>

    <label for="email">Correo electrónico:</label>
    <input type="email" id="email" name="email" required>

    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required>

    <input type="submit" value="Registrar">
  </form>

  <p>¿Ya tenés cuenta? <a class="enlace" href="login.php">Iniciá sesión</a></p>
</div>

</body>
</html>
