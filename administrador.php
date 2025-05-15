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

// Obtener todas las solicitudes pendientes
$sql = "SELECT * FROM solicitudes_cita WHERE estado = 'pendiente'";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Gestionar Citas</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h2>Gestión de Citas Médicas</h2>
            <nav>
                <ul>
                      <li><a href="administrador.php">Inicio</a></li>
                    <li><a href="gestionar_pacientes.php">Gestionar Pacientes</a></li>
                    <li><a href="gestionar_medicos.php">Gestionar Médicos</a></li>
                    <li><a href="login.html">Salir</a></li>
                </ul>
            </nav>
        </header>

        <!-- Gestionar Citas Médicas -->
        <section>
            <h3>Solicitudes Pendientes</h3>
            <?php
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr><th>Paciente</th><th>Especialidad</th><th>Asignar Médico</th><th>Fecha</th><th>Hora</th><th>Acción</th></tr></thead>";
                echo "<tbody>";

                while ($row = $result->fetch_assoc()) {
                    $id_solicitud = $row['id_solicitud'];
                    $id_paciente = $row['id_paciente'];
                    $especialidad = $row['especialidad'];

                    // Obtener el nombre del paciente
                    $sql_paciente = "SELECT nombre, apellido FROM login WHERE id_login = ?";
                    $stmt = $conexion->prepare($sql_paciente);
                    $stmt->bind_param("i", $id_paciente);
                    $stmt->execute();
                    $resultado_paciente = $stmt->get_result();
                    $paciente = $resultado_paciente->fetch_assoc();
                    $nombre_paciente = $paciente['nombre'] . " " . $paciente['apellido'];

                    // Obtener los médicos según especialidad
                    $sql_medicos = "SELECT m.id_medico, l.nombre, l.apellido 
                                    FROM medicos m
                                    JOIN login l ON m.id_login = l.id_login
                                    WHERE m.especialidad = ?";
                    $stmt_medicos = $conexion->prepare($sql_medicos);
                    $stmt_medicos->bind_param("s", $especialidad);
                    $stmt_medicos->execute();
                    $resultado_medicos = $stmt_medicos->get_result();

                    echo "<tr>
                            <td>$nombre_paciente</td>
                            <td>$especialidad</td>
                            <td>
                                <form action='asignar_cita.php' method='POST'>
                                    <select name='id_medico' required>";
                    if ($resultado_medicos->num_rows > 0) {
                        while ($medico = $resultado_medicos->fetch_assoc()) {
                            $nombre_completo = $medico['nombre'] . " " . $medico['apellido'];
                            echo "<option value='".$medico['id_medico']."'>$nombre_completo</option>";
                        }
                    } else {
                        echo "<option value=''>No hay médicos disponibles</option>";
                    }

                    echo "</select>
                            </td>
                            <td><input type='date' name='fecha' required></td>
                            <td><input type='time' name='hora' required></td>
                            <td>
                                <input type='hidden' name='id_solicitud' value='$id_solicitud'>
                                <input type='submit' value='Asignar Cita'>
                            </form>
                            </td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No hay solicitudes pendientes.</p>";
            }
            ?>
        </section>
    </div>
</body>
</html>

<?php
$conexion->close();
?>
