<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error: " . $conn->connect_error);
}

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Tu carrito</title>
    <style>
        .bienvenido {
            color: orange;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>";

if (isset($_SESSION['id_usuario']) && isset($_SESSION['nombre'])) {
    echo "<div class='bienvenido'>👋 Bienvenido, " . htmlspecialchars($_SESSION['nombre']) . "</div>";
}

echo "<h2>🛒 Tu carrito</h2>";
$total = 0;

if (isset($_SESSION['id_usuario'])) {
    $usuario_id = $_SESSION['id_usuario'];

    // Buscar carrito activo
    $stmt = $conn->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' ORDER BY id_carrito DESC LIMIT 1");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($id_carrito);
    $stmt->fetch();
    $stmt->close();

    if ($id_carrito) {
        $sql = "SELECT p.id_producto, p.nombre, p.precio, cp.cantidad 
                FROM carrito_producto cp
                JOIN producto p ON cp.id_producto = p.id_producto
                WHERE cp.id_carrito = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_carrito);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['precio'] * $row['cantidad'];
            $total += $subtotal;
            echo "<li>
                    <strong>" . htmlspecialchars($row['nombre']) . "</strong> x " . $row['cantidad'] . " = $" . number_format($subtotal, 2) . "
                    <form action='eliminar_del_carrito.php' method='POST' style='display:inline'>
                        <input type='hidden' name='id_producto' value='" . $row['id_producto'] . "'>
                        <button type='submit'>🗑️ Eliminar</button>
                    </form>
                  </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Tu carrito está vacío.</p>";
    }

} else {
    // Usuario no logueado
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo "<p>Tu carrito está vacío.</p>";
    } else {
        echo "<ul>";
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $stmt = $conn->prepare("SELECT nombre, precio FROM producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $stmt->bind_result($nombre, $precio);
            $stmt->fetch();
            $stmt->close();

            $subtotal = $precio * $cantidad;
            $total += $subtotal;

            echo "<li>
                    <strong>" . htmlspecialchars($nombre) . "</strong> x $cantidad = $" . number_format($subtotal, 2) . "
                    <form action='eliminar_del_carrito.php' method='POST' style='display:inline'>
                        <input type='hidden' name='id_producto' value='$id_producto'>
                        <button type='submit'>🗑️ Eliminar</button>
                    </form>
                  </li>";
        }
        echo "</ul>";
    }
}

echo "<p><strong>Total: $" . number_format($total, 2) . "</strong></p>";

if ($total > 0) {
    echo "<a href='pagar.php'><button>✅ Ir a pagar</button></a>";
}

echo "<br><br><a href='index.php'>⬅️ Volver a la tienda</a>";
echo "</body></html>";
$conn->close();
?>
