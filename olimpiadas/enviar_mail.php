<?php
// Este archivo es incluido desde confirmar_compra.php
// Ya existen $usuario_id, $id_compra, $total, $metodo_pago, $fecha

// Buscar correo del usuario
$stmt = $conn->prepare("SELECT email FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

$asunto = "🧾 Confirmación de compra - Mundo Aventura";
$mensaje = "Gracias por tu compra.<br><strong>Total:</strong> $" . number_format($total, 2) . "<br><strong>Método:</strong> " . ucfirst($metodo_pago);
$estado_envio = "enviado";

// Insertar en tabla `mail`
$stmt = $conn->prepare("INSERT INTO mail (id_usuario, id_compra, asunto, mensaje, fecha_envio, estado_envio) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $usuario_id, $id_compra, $asunto, $mensaje, $fecha, $estado_envio);
$stmt->execute();
$stmt->close();
?>
