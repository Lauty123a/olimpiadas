<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de Compras</title>
  <link rel="stylesheet" href="estilos.css">
  <style>
    .mensaje-exito {
      background-color: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-weight: bold;
    }
    .bienvenido-nav {
      color: white;
      margin-right: 10px;
      font-weight: bold;
    }
    .botones-nav a {
      color: white;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<div class="navbar">
  <img src="logo.jpeg" alt="Logo Mundo Aventura" class="logo">
  <div class="botones-nav">
    <a href="index.php">Inicio</a>

    <?php if (isset($_SESSION['id_usuario'])): ?>
      <span class="bienvenido-nav">👋 Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="logout.php" onclick="return confirm('¿Seguro que querés cerrar sesión?')">Cerrar sesión</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="registro.php">Registro</a>
    <?php endif; ?>
  </div>
</div>

<div style="margin-top: 10px;">
  <a href="ver_carrito.php">🛒 Ver carrito</a>
</div>

<div class="container">
  <h1>Lista de Productos</h1>

  <?php if (isset($_GET['bienvenido']) && isset($_SESSION['nombre'])): ?>
    <div class="mensaje-exito">
      ✅ ¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>!
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['cerrado']) && $_GET['cerrado'] == 1): ?>
    <div class="mensaje-exito">
      ✅ Sesión cerrada con éxito.
    </div>
  <?php endif; ?>

  <?php
  $host = "localhost";
  $usuario = "root";
  $contrasena = "";
  $basedatos = "agencia_viaje";

  $conn = new mysqli($host, $usuario, $contrasena, $basedatos);
  if ($conn->connect_error) {
      die("❌ Error de conexión: " . $conn->connect_error);
  }

  $sql = "SELECT id_producto, nombre, descripcion, precio, imagen FROM producto";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      echo "<ul class='producto-lista'>";
      while ($row = $result->fetch_assoc()) {
          $imgPath = "imagenes/" . $row['imagen'];

          if (!file_exists($imgPath) || empty($row['imagen'])) {
              $imgPath = "imagenes/default.jpg";
          }

          echo "<li class='producto-item'>
                  <img src='$imgPath' alt='Imagen de " . htmlspecialchars($row['nombre']) . "'>
                  <div class='producto-info'>
                    <h3>" . htmlspecialchars($row['nombre']) . "</h3>
                    <p>" . htmlspecialchars($row['descripcion']) . "</p>
                    <p class='precio'>$ " . htmlspecialchars($row['precio']) . "</p>
                    <form action='agregar_al_carrito.php' method='POST' class='producto-acciones'>
                      <input type='hidden' name='id_producto' value='" . $row['id_producto'] . "'>
                      <input type='number' name='cantidad' value='1' min='1' required>
                      <input type='submit' value='Agregar al carrito'>
                    </form>
                  </div>
                </li>";
      }
      echo "</ul>";
  } else {
      echo "<p>No hay productos disponibles.</p>";
  }

  $conn->close();
  ?>
</div>

</body>
</html>
