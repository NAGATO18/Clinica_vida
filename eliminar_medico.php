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

// Eliminar médico
if (isset($_GET['id'])) {
    $id_medico = $_GET['id'];

    // Eliminar del registro en la tabla medicos
    $sql = "DELETE FROM medicos WHERE id_medico = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_medico);
    if ($stmt->execute()) {
        // Eliminar también de la tabla login
        $sql_login = "DELETE FROM login WHERE id_login = (SELECT id_login FROM medicos WHERE id_medico = ?)";
        $stmt_login = $conexion->prepare($sql_login);
        $stmt_login->bind_param("i", $id_medico);
        $stmt_login->execute();
        echo "<script>alert('Médico eliminado exitosamente'); window.location.href='gestionar_medicos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar médico');</script>";
    }
}

$conexion->close();
?>
