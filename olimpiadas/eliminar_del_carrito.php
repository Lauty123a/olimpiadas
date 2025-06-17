<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión
$conn = new mysqli("localhost", "root", "", "agencia_viaje");
if ($conn->connect_error) {
    die("❌ Error: " . $conn->connect_error);
}

if (!isset($_POST['id_producto'])) {
    die("⚠️ Falta el ID del producto.");
}

$id_producto = (int) $_POST['id_producto'];

if (isset($_SESSION['id_usuario'])) {
    // Usuario logueado: eliminar de base de datos
    $usuario_id = $_SESSION['id_usuario'];

    // Buscar carrito activo
    $stmt = $conn->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' ORDER BY id_carrito DESC LIMIT 1");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($id_carrito);
    $stmt->fetch();
    $stmt->close();

    if ($id_carrito) {
        $stmt = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
        $stmt->bind_param("ii", $id_carrito, $id_producto);
        $stmt->execute();
        $stmt->close();
    }
} else {
    // Usuario no logueado: eliminar del array de sesión
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
    }
}

$conn->close();
header("Location: ver_carrito.php");
exit;
?>
