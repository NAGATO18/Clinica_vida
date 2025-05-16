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

// Obtener los datos del paciente
$sql = "SELECT * FROM login WHERE id_login = ? AND rol = 'paciente'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $paciente = $result->fetch_assoc();
} else {
    echo "Paciente no encontrado.";
    exit();
}

// Actualizar los datos del paciente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_paciente'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $numero = $_POST['numero'];
    $correo = $_POST['correo'];

    // Actualizar en la base de datos
    $sql_update = "UPDATE login SET nombre = ?, apellido = ?, numero = ?, correo = ? WHERE id_login = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("ssisi", $nombre, $apellido, $numero, $correo, $id_paciente);

    if ($stmt_update->execute()) {
        echo "<script>alert('Paciente actualizado con éxito'); window.location.href='gestionar_pacientes.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el paciente');</script>";
    }

    $stmt_update->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h2>Editar Paciente</h2>
            <nav>
                <ul>
                    <li><a href="gestionar_pacientes.php">Gestionar Pacientes</a></li>
                </ul>
            </nav>
        </header>

        <section>
            <h3>Formulario de Edición de Paciente</h3>
            <form action="editar_paciente.php?id=<?php echo $id_paciente; ?>" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $paciente['nombre']; ?>" required><br>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo $paciente['apellido']; ?>" required><br>

                <label for="numero">Número:</label>
                <input type="number" id="numero" name="numero" value="<?php echo $paciente['numero']; ?>" required><br>

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo $paciente['correo']; ?>" required><br>

                <input type="submit" name="editar_paciente" value="Actualizar Paciente">
            </form>
        </section>
    </div>
</body>
</html>

<?php
$conexion->close();
?>
