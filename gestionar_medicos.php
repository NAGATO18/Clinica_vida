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

// Obtener todos los médicos
$sql = "SELECT id_medico, l.nombre, l.apellido, m.especialidad 
        FROM medicos m
        JOIN login l ON m.id_login = l.id_login";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Médicos</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h2>Gestionar Médicos</h2>
            <nav>
                <ul>
                    <li><a href="administrador.php">Inicio</a></li>
                    <li><a href="gestionar_pacientes.php">Gestionar Pacientes</a></li>
                    <li><a href="gestionar_medicos.php">Gestionar Médicos</a></li>
                </ul>
            </nav>
        </header>

        <section>
            <h3>Listado de Médicos</h3>

            <!-- Botón para registrar nuevo médico -->
            <p><a href="crear_medico.php" class="boton-crear">Registrar Nuevo Médico</a></p>

            <?php
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr><th>Nombre</th><th>Especialidad</th><th>Acción</th></tr></thead>";
                echo "<tbody>";

                while ($row = $result->fetch_assoc()) {
                    $id_medico = $row['id_medico'];
                    $nombre_medico = $row['nombre'] . " " . $row['apellido'];
                    $especialidad = $row['especialidad'];

                    echo "<tr>
                            <td>$nombre_medico</td>
                            <td>$especialidad</td>
                            <td><a href='editar_medico.php?id=$id_medico'>Editar</a> | <a href='eliminar_medico.php?id=$id_medico' onclick='return confirm(\"¿Estás seguro de eliminar este médico?\")'>Eliminar</a></td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No hay médicos registrados.</p>";
            }
            ?>
        </section>
    </div>
</body>
</html>

<?php
$conexion->close();
?>
