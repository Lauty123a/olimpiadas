<?php
// lista_productos.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión directa a la base de datos
$host = "localhost";
$usuario = "root";
$contrasena = ""; // Cambiar si usás clave
$basedatos = "agencia_viaje";

$conn = new mysqli($host, $usuario, $contrasena, $basedatos);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

session_start();

// Consulta de productos
$sql = "SELECT id_producto, nombre, descripcion, precio FROM producto";
$result = $conn->query($sql);

if (!$result) {
    die("❌ Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "<h2>Lista de Productos</h2><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><strong>" . htmlspecialchars($row['nombre']) . "</strong> - " .
             htmlspecialchars($row['descripcion']) . " - $ " . htmlspecialchars($row['precio']) .
             "<form action='agregar_al_carrito.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='" . $row['id_producto'] . "'>
                <input type='number' name='cantidad' value='1' min='1' required>
                <input type='submit' value='Agregar al carrito'>
              </form></li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hay productos disponibles.</p>";
}

$conn->close();
?>
