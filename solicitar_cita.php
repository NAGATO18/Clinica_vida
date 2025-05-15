<?php 
session_start();
if (!isset($_SESSION['id_login']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.html");
    exit();
}

// Mensaje de éxito o error
$mensaje = "";

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = $_SESSION['id_login'];
    $especialidad = $_POST['especialidad'];

    $conexion = new mysqli("localhost", "root", "", "bd_CitasMedicas");

    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Guardar la solicitud de cita en la base de datos
    $sql = "INSERT INTO solicitudes_cita (id_paciente, especialidad) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $id_paciente, $especialidad);

    if ($stmt->execute()) {
        $mensaje = "<p class='success'>✅ Solicitud enviada correctamente. Te enviaremos un correo cuando se asigne la cita.</p>";
    } else {
        $mensaje = "<p class='error'>❌ Error al guardar la solicitud: " . $conexion->error . "</p>";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Cita</title>
    <link rel="stylesheet" href="solicitar_cita.css">
    <script>
        // JavaScript para ocultar el mensaje después de 5 segundos
        window.onload = function() {
            var mensaje = document.getElementById('mensaje');
            if (mensaje) {
                setTimeout(function() {
                    mensaje.style.display = 'none';
                }, 1000);  // El mensaje desaparecerá después de 5 segundos
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Solicitar Cita Médica</h2>

        <!-- Aquí se muestra el mensaje -->
        <?php if (!empty($mensaje)) echo "<div id='mensaje'>$mensaje</div>"; ?>

        <!-- Formulario de solicitud -->
        <form action="solicitar_cita.php" method="POST">
            <label for="especialidad">Especialidad:</label>
            <select name="especialidad" required>
                <option value="">Selecciona una especialidad</option>
                <option value="Medicina General">Medicina General</option>
                <option value="Pediatría">Pediatría</option>
                <option value="Ginecología">Ginecología</option>
                <option value="Dermatología">Dermatología</option>
            </select>
            <input type="submit" value="Solicitar Cita">
            <a href="logout.php" class="exit-button">Salir</a>
        </form>
    </div>
</body>
</html>
