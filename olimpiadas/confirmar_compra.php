<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario'])) {
    die("⚠️ No estás logueado.");
}

$usuario_id = $_SESSION['id_usuario'];
$id_carrito = $_POST['id_carrito'] ?? null;
$total = $_POST['total'] ?? null;
$metodo_pago = $_POST['metodo_pago'] ?? null;

if (!$id_carrito || !$total || !$metodo_pago) {
    die("❌ Faltan datos.");
}

$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$fecha = date('Y-m-d H:i:s');

// Registrar compra
$stmt = $conn->prepare("INSERT INTO compra (id_carrito, id_usuario, fecha_compra, total) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisd", $id_carrito, $usuario_id, $fecha, $total);
$stmt->execute();
$id_compra = $stmt->insert_id;
$stmt->close();

// Registrar pago
$stmt = $conn->prepare("INSERT INTO pago (id_compra, metodo_pago, estado_pago, fecha_pago) VALUES (?, ?, ?, ?)");
$estado = 'completado';
$stmt->bind_param("isss", $id_compra, $metodo_pago, $estado, $fecha);
$stmt->execute();
$stmt->close();

// Registrar mail (en tabla 'mail')
require 'enviar_mail.php';

echo "<h2>✅ Compra confirmada</h2>";
echo "<p>Método de pago: <strong>" . ucfirst($metodo_pago) . "</strong></p>";
echo "<p>Total pagado: $" . number_format($total, 2) . "</p>";
echo "<a href='ver_compra.php?id_compra=$id_compra'>🧾 Ver detalle de compra</a>";
echo "<h2>🎉 ¡Gracias por comprar, " . htmlspecialchars($_SESSION['nombre']) . "!</h2>";
echo "<p>Recibirás un correo con los detalles de tu compra.</p>";
echo "<a href='index.php'>⬅️ Volver al inicio</a>";
