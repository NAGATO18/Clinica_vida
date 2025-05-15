<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "bd_CitasMedicas");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener datos del formulario
$id_paciente = $_POST['id_paciente'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$motivo = $_POST['motivo'];

// Insertar la cita
$sql = "INSERT INTO citas (id_paciente, fecha, hora, motivo) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("isss", $id_paciente, $fecha, $hora, $motivo);

if ($stmt->execute()) {
    echo "✅ Cita agendada correctamente.";
} else {
    echo "❌ Error al agendar la cita: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
