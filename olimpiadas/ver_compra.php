<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id_compra'])) {
    die("❌ No autorizado.");
}

$id_compra = (int) $_GET['id_compra'];
$usuario_id = $_SESSION['id_usuario'];

$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Obtener datos de la compra
$stmt = $conn->prepare("
    SELECT c.fecha_compra, c.total, p.metodo_pago 
    FROM compra c
    JOIN pago p ON p.id_compra = c.id_compra
    WHERE c.id_compra = ? AND c.id_usuario = ?");
$stmt->bind_param("ii", $id_compra, $usuario_id);
$stmt->execute();
$stmt->bind_result($fecha, $total, $metodo);
if (!$stmt->fetch()) {
    die("❌ Compra no encontrada.");
}
$stmt->close();

// Mostrar detalle
echo "<h2>🧾 Detalle de compra</h2>";
echo "<p><strong>Fecha:</strong> $fecha</p>";
echo "<p><strong>Total:</strong> $" . number_format($total, 2) . "</p>";
echo "<p><strong>Método de pago:</strong> " . ucfirst($metodo) . "</p>";

// Mostrar productos
echo "<h3>📦 Productos comprados</h3>";

$stmt = $conn->prepare("
    SELECT p.nombre, p.precio, cp.cantidad
    FROM carrito_producto cp
    JOIN producto p ON cp.id_producto = p.id_producto
    WHERE cp.id_carrito = (SELECT id_carrito FROM compra WHERE id_compra = ?)
");
$stmt->bind_param("i", $id_compra);
$stmt->execute();
$result = $stmt->get_result();
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['precio'] * $row['cantidad'];
    echo "<li><strong>" . htmlspecialchars($row['nombre']) . "</strong> x " . $row['cantidad'] . " = $" . number_format($subtotal, 2) . "</li>";
}
echo "</ul>";

echo "<br><a href='index.php'>⬅️ Volver al inicio</a>";
?>
