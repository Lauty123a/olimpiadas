<?php
$conexion = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conexion->connect_error) die("Error: " . $conexion->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_producto'])) {
    $sql = "UPDATE producto SET descripcion=?, destino=?, fecha_inicio=?, fecha_fin=?, duracion_dias=?, precio=? WHERE id_producto=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "ssssidi",
        $_POST['descripcion'],
        $_POST['destino'],
        $_POST['fecha_inicio'],
        $_POST['fecha_fin'],
        $_POST['duracion_dias'],
        $_POST['precio'],
        $_POST['id_producto']
    );
    $stmt->execute();
}

$resultado = $conexion->query("SELECT * FROM producto");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Productos</title>
    <link rel="stylesheet" href="estilos2.css">
</head>
<body>
<div class="navbar">
    <a href="panel_encargado.php">Volver al panel</a>
</div>

<div class="container">
    <h1>Actualizar Productos</h1>
    <?php while ($row = $resultado->fetch_assoc()): ?>
    <h2 class="producto-nombre"><?= htmlspecialchars($row['nombre']) ?></h2>
    <form method="POST" class="producto-form">
        <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">

        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion"><?= htmlspecialchars($row['descripcion']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Destino:</label>
            <input type="text" name="destino" value="<?= htmlspecialchars($row['destino']) ?>">
        </div>

        <div class="form-group">
            <label>Fecha inicio:</label>
            <input type="date" name="fecha_inicio" value="<?= $row['fecha_inicio'] ?>">
        </div>

        <div class="form-group">
            <label>Fecha fin:</label>
            <input type="date" name="fecha_fin" value="<?= $row['fecha_fin'] ?>">
        </div>

        <div class="form-group">
            <label>Duración en días:</label>
            <input type="number" name="duracion_dias" value="<?= $row['duracion_dias'] ?>">
        </div>

        <div class="form-group">
            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= $row['precio'] ?>">
        </div>

        <div class="form-group form-submit">
            <input type="submit" value="Actualizar">
        </div>
    </form>
    <hr>
<?php endwhile; ?>
</div>
</body>
</html>
