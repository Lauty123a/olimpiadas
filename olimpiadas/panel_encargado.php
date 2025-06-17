<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$usuario = "root";
$contrasena = "";
$basedatos = "agencia_viaje";
$conn = new mysqli($host, $usuario, $contrasena, $basedatos);
if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo_producto = $_POST['tipo_producto'];
    $precio = $_POST['precio'];
    $destino = $_POST['destino'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $duracion_dias = $_POST['duracion_dias'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES['imagen']['name'];
        $tmpArchivo = $_FILES['imagen']['tmp_name'];
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $permitidas)) {
            $nuevoNombre = uniqid() . "." . $ext;
            $ruta = "imagenes/" . $nuevoNombre;

            if (move_uploaded_file($tmpArchivo, $ruta)) {
                $sql = "INSERT INTO producto (nombre, descripcion, tipo_producto, precio, destino, fecha_inicio, fecha_fin, duracion_dias, imagen)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdsssis", $nombre, $descripcion, $tipo_producto, $precio, $destino, $fecha_inicio, $fecha_fin, $duracion_dias, $nuevoNombre);
                $stmt->execute() ? $mensaje = "✅ Producto cargado correctamente." : $mensaje = "❌ Error al guardar.";
            } else {
                $mensaje = "❌ No se pudo mover la imagen.";
            }
        } else {
            $mensaje = "❌ Solo imágenes JPG, JPEG, PNG o GIF.";
        }
    } else {
        $mensaje = "❌ Error al subir la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Encargado</title>
    <link rel="stylesheet" href="estilos1.css">
</head>
<body>
<div class="navbar">
    <img src="logo.jpeg" alt="Logo" class="logo">
    <span class="bienvenida">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
    <div>
        <a href="index.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</div>

<div class="container">
    <h1>Gestión de Productos</h1>

    <?php if ($mensaje): ?>
        <p class="<?php echo strpos($mensaje, '✅') === 0 ? 'success' : 'error'; ?>"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>

        <label>Tipo de producto:</label>
        <input type="text" name="tipo_producto" required>

        <label>Precio:</label>
        <input type="number" name="precio" step="0.01" required>

        <label>Destino:</label>
        <input type="text" name="destino" required>

        <label>Fecha inicio:</label>
        <input type="date" name="fecha_inicio" required>

        <label>Fecha fin:</label>
        <input type="date" name="fecha_fin" required>

        <label>Duración en días:</label>
        <input type="number" name="duracion_dias" required>

        <label>Imagen:</label>
        <input type="file" name="imagen" accept="image/*" required>

        <input type="submit" value="Cargar producto">
    </form>

    <section>
        <h2>Actualizar Productos</h2>
        <p><a href="actualizar_productos.php">Ir al panel de actualización</a></p>
    </section>
</div>
</body>
</html>
