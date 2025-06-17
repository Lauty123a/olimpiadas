<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Conexión
$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

if (!isset($_POST['id_producto']) || !isset($_POST['cantidad'])) {
    echo "<p>❌ Faltan datos.</p><a href='index.php'>⬅️ Volver</a>";
    exit;
}

$id_producto = (int) $_POST['id_producto'];
$cantidad = (int) $_POST['cantidad'];

echo "<h2>🛒 Carrito actualizado</h2>";

if (isset($_SESSION['id_usuario'])) {
    $usuario_id = $_SESSION['id_usuario'];

    $stmt = $conn->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' ORDER BY id_carrito DESC LIMIT 1");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($id_carrito);
    $stmt->fetch();
    $stmt->close();

    if (empty($id_carrito)) {
        $fecha = date('Y-m-d H:i:s');
        $estado = 'activo';
        $stmt = $conn->prepare("INSERT INTO carrito (id_usuario, fecha_creacion, estado) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $fecha, $estado);
        $stmt->execute();
        $id_carrito = $stmt->insert_id;
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT id_carrito_producto, cantidad FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
    $stmt->bind_param("ii", $id_carrito, $id_producto);
    $stmt->execute();
    $stmt->bind_result($id_cp, $cantidad_existente);
    if ($stmt->fetch()) {
        $nueva_cantidad = $cantidad_existente + $cantidad;
        $stmt->close();
        $stmt = $conn->prepare("UPDATE carrito_producto SET cantidad = ? WHERE id_carrito_producto = ?");
        $stmt->bind_param("ii", $nueva_cantidad, $id_cp);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO carrito_producto (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_carrito, $id_producto, $cantidad);
        $stmt->execute();
        $stmt->close();
    }

    echo "<p>✅ Producto agregado al carrito.</p>";
} else {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto] += $cantidad;
    } else {
        $_SESSION['carrito'][$id_producto] = $cantidad;
    }

    echo "<p>✅ Producto agregado al carrito temporal.</p>";
}

echo "<br><a href='ver_carrito.php'><button>🛍️ Ver carrito y pagar</button></a>";
echo "<br><br><a href='index.php'>⬅️ Seguir comprando</a>";

$conn->close();
?>
