<?php
header("Content-Type: application/json");
require "conexion.php";

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

if ($nombre === '' || $correo === '' || $telefono === '' || strlen($password) < 6) {
    echo json_encode(["exito" => false, "mensaje" => "Completá todos los campos correctamente."]);
    exit;
}

$correo_esc = $conexion->real_escape_string($correo);
$check = $conexion->query("SELECT id FROM usuarios WHERE correo = '$correo_esc'");
if ($check && $check->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ya existe una cuenta con ese correo."]);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, telefono, password_hash) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $correo, $telefono, $hash);

if ($stmt->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Cuenta creada. Redirigiendo a iniciar sesión..."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "Error al crear la cuenta: " . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>