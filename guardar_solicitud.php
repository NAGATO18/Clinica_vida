<?php
session_start();

if (!isset($_SESSION['id_login']) || $_SESSION['rol'] !== 'paciente') {
    die("Acceso no autorizado.");
}

$conexion = new mysqli("localhost", "root", "", "bd_CitasMedicas");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id_paciente = $_SESSION['id_login'];
$especialidad = $_POST['especialidad'];

$sql = "INSERT INTO solicitudes_cita (id_paciente, especialidad) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("is", $id_paciente, $especialidad);

if ($stmt->execute()) {
    echo "✅ Solicitud enviada correctamente. Te enviaremos un correo cuando se asigne la cita.";
} else {
    echo "❌ Error al guardar la solicitud: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
