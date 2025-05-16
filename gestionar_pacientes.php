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

// Crear paciente (si el formulario ha sido enviado)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_paciente'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $numero = $_POST['numero'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);  // Encriptación de la contraseña
    $rol = 'paciente';  // Por defecto el rol es paciente

    // Insertar el paciente en la base de datos
    $sql = "INSERT INTO login (nombre, apellido, numero, rol, correo, contrasena) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssisss", $nombre, $apellido, $numero, $rol, $correo, $contrasena);

    if ($stmt->execute()) {
        echo "<script>alert('Paciente creado con éxito');</script>";
    } else {
        echo "<script>alert('Error al crear el paciente.');</script>";
    }

    $stmt->close();
}

// Obtener todos los pacientes
$sql = "SELECT id_login, nombre, apellido, correo FROM login WHERE rol = 'paciente'";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Pacientes</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h2>Gestionar Pacientes</h2>
            <nav>
                <ul>
                    <li><a href="administrador.php">Inicio</a></li>
                    <li><a href="gestionar_pacientes.php">Gestionar Pacientes</a></li>
                    <li><a href="gestionar_medicos.php">Gestionar Médicos</a></li>
                </ul>
            </nav>
        </header>

        <section>
            <h3>Crear Nuevo Paciente</h3>
            <form action="gestionar_pacientes.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required><br>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required><br>

                <label for="numero">Número:</label>
                <input type="number" id="numero" name="numero" required><br>

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required><br>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required><br>

                <input type="submit" name="crear_paciente" value="Crear Paciente">
            </form>

            <h3>Listado de Pacientes</h3>
            <?php
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr><th>Nombre</th><th>Correo</th><th>Acción</th></tr></thead>";
                echo "<tbody>";

                while ($row = $result->fetch_assoc()) {
                    $id_paciente = $row['id_login'];
                    $nombre_paciente = $row['nombre'] . " " . $row['apellido'];
                    $correo_paciente = $row['correo'];

                    echo "<tr>
                            <td>$nombre_paciente</td>
                            <td>$correo_paciente</td>
                            <td><a href='editar_paciente.php?id=$id_paciente'>Editar</a> | <a href='eliminar_paciente.php?id=$id_paciente'>Eliminar</a></td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No hay pacientes registrados.</p>";
            }
            ?>
        </section>
    </div>
</body>
</html>

<?php
$conexion->close();
?>
