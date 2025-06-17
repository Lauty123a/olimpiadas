<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['volver_a_pagar'] = true;
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error: " . $conn->connect_error);
}

// Buscar carrito activo
$stmt = $conn->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' ORDER BY id_carrito DESC LIMIT 1");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($id_carrito);
$stmt->fetch();
$stmt->close();

if (!$id_carrito) {
    echo "<p>🛒 Tu carrito está vacío.</p><a href='index.php'>⬅️ Volver</a>";
    exit;
}

// Calcular total
$total = 0;
$stmt = $conn->prepare("SELECT p.precio, cp.cantidad FROM carrito_producto cp JOIN producto p ON cp.id_producto = p.id_producto WHERE cp.id_carrito = ?");
$stmt->bind_param("i", $id_carrito);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $total += $row['precio'] * $row['cantidad'];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar pago</title>
    <style>
        .bienvenido {
            color: orange;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['nombre'])): ?>
  <div class="bienvenido">👋 Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></div>
<?php endif; ?>

<h2>🧾 Confirmar pago</h2>
<p>Total: <strong>$<?= number_format($total, 2) ?></strong></p>

<form action="confirmar_compra.php" method="POST">
    <input type="hidden" name="id_carrito" value="<?= $id_carrito ?>">
    <input type="hidden" name="total" value="<?= $total ?>">
    <label for="metodo_pago">Método de pago:</label>
    <select name="metodo_pago" required>
        <option value="efectivo">Efectivo</option>
        <option value="debito">Débito</option>
        <option value="credito">Crédito</option>
    </select>
    <br><br>
    <button type="submit">✅ Confirmar compra</button>
</form>

<a href="index.php">⬅️ Volver</a>

</body>
</html>
