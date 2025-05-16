<?php
session_start();
if (!isset($_SESSION['id_login']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.html");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "bd_CitasMedicas");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id_paciente = $_GET['id'];

// Eliminar el paciente
$sql = "DELETE FROM login WHERE id_login = ? AND rol = 'paciente'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_paciente);

if ($stmt->execute()) {
    echo "<script>alert('Paciente eliminado con éxito'); window.location.href='gestionar_pacientes.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el paciente');</script>";
}

$stmt->close();
$conexion->close();
?>
