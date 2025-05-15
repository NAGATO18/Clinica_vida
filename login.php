<?php
session_start();  // Inicia la sesión

$conexion = new mysqli("localhost", "root", "", "bd");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Consultar la base de datos para verificar el correo y la contraseña
$sql = "SELECT * FROM login WHERE correo = ? AND contrasena = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $correo, $contrasena);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    
    // Guardar el ID de sesión y el rol
    $_SESSION['id_login'] = $usuario['id_login'];
    $_SESSION['rol'] = $usuario['rol'];
    
    // Redirigir según el rol
    if ($usuario['rol'] === 'paciente') {
        header("Location: solicitar_cita.php");  // Redirige a la página del paciente
    } elseif ($usuario['rol'] === 'medico') {
        header("Location: medico.php");  // Redirige a la página del médico
    } elseif ($usuario['rol'] === 'administrador') {
        header("Location: administrador.php");  // Redirige a la página del administrador
    }
    exit();
} else {
    echo "❌ Correo o contraseña incorrectos.";
}

$stmt->close();
$conexion->close();
?>
