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

// Obtener los datos del médico a editar
if (isset($_GET['id'])) {
    $id_medico = $_GET['id'];

    $sql = "SELECT l.id_login, l.nombre, l.apellido, l.numero, l.correo, m.especialidad FROM medicos m
            JOIN login l ON m.id_login = l.id_login WHERE m.id_medico = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_medico);
    $stmt->execute();
    $result = $stmt->get_result();
    $medico = $result->fetch_assoc();
} else {
    echo "<script>alert('Médico no encontrado'); window.location.href='gestionar_medicos.php';</script>";
    exit();
}

// Procesar la edición
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_medico'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $numero = $_POST['numero'];
    $correo = $_POST['correo'];
    $especialidad = $_POST['especialidad'];

    // Actualizar los datos en la tabla login
    $sql_login = "UPDATE login SET nombre = ?, apellido = ?, numero = ?, correo = ? WHERE id_login = ?";
    $stmt_login = $conexion->prepare($sql_login);
    $stmt_login->bind_param("ssiss", $nombre, $apellido, $numero, $correo, $medico['id_login']);

    if ($stmt_login->execute()) {
        // Actualizar la especialidad del médico
        $sql_medico = "UPDATE medicos SET especialidad = ? WHERE id_medico = ?";
        $stmt_medico = $conexion->prepare($sql_medico);
        $stmt_medico->bind_param("si", $especialidad, $id_medico);

        if ($stmt_medico->execute()) {
            echo "<script>alert('Médico actualizado exitosamente'); window.location.href='gestionar_medicos.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar especialidad del médico');</script>";
        }
    } else {
        echo "<script>alert('Error al actualizar datos del médico');</script>";
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Médico</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <h2>Editar Médico</h2>
        <form action="editar_medico.php?id=<?php echo $id_medico; ?>" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo $medico['nombre']; ?>" required>
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" value="<?php echo $medico['apellido']; ?>" required>
            <label for="numero">Número:</label>
            <input type="text" name="numero" value="<?php echo $medico['numero']; ?>" required>
            <label for="correo">Correo:</label>
            <input type="email" name="correo" value="<?php echo $medico['correo']; ?>" required>
            <label for="especialidad">Especialidad:</label>
            <input type="text" name="especialidad" value="<?php echo $medico['especialidad']; ?>" required>
            <input type="submit" name="editar_medico" value="Actualizar Médico">
        </form>
    </div>
</body>
</html>
