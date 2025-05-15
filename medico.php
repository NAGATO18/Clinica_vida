<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene rol de médico
if (!isset($_SESSION['id_login']) || $_SESSION['rol'] !== 'medico') {
    header("Location: login.html");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "bd_CitasMedicas");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Procesar cambios de estado si se envió un formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_solicitud'], $_POST['nuevo_estado'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $nuevo_estado = $_POST['nuevo_estado'];

    // Solo permitir ciertos estados válidos
    $estados_validos = ['proceso', 'terminado'];
    if (in_array($nuevo_estado, $estados_validos)) {
        $sql_update = "UPDATE solicitudes_cita SET estado = ? WHERE id_solicitud = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("si", $nuevo_estado, $id_solicitud);
        $stmt_update->execute();
        $stmt_update->close();

        // Redirigir para evitar reenvío del formulario
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

$id_login = $_SESSION['id_login'];

// Obtener ID del médico
$sql_medico = "SELECT m.id_medico, l.nombre, l.apellido
               FROM medicos m
               JOIN login l ON m.id_login = l.id_login
               WHERE m.id_login = ?";
$stmt_medico = $conexion->prepare($sql_medico);
$stmt_medico->bind_param("i", $id_login);
$stmt_medico->execute();
$result_medico = $stmt_medico->get_result();

if ($result_medico->num_rows === 0) {
    echo "No se encontró el médico.";
    exit();
}

$medico = $result_medico->fetch_assoc();
$id_medico = $medico['id_medico'];
$nombre_medico = $medico['nombre'] . ' ' . $medico['apellido'];

// Consultar las citas asignadas al médico
$sql = "SELECT sc.id_solicitud, sc.fecha_asignada, sc.hora_asignada, l.nombre, l.apellido, sc.estado 
        FROM solicitudes_cita sc
        JOIN login l ON sc.id_paciente = l.id_login
        WHERE sc.id_medico = ? AND sc.estado IN ('pendiente' , 'asignada', 'proceso')";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_medico);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Médico</title>
    <link rel="stylesheet" href="medico.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bienvenido, Dr. <?php echo htmlspecialchars($nombre_medico); ?></h1>
            <!-- Aquí agregamos el botón de salida -->
            <form action="logout.php" method="POST">
                <button type="submit">Salir</button>
            </form>
        </header>

        <section class="citas">
            <h2>Citas Asignadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Cita</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id_solicitud']}</td>
                                    <td>{$row['fecha_asignada']}</td>
                                    <td>{$row['hora_asignada']}</td>
                                    <td>{$row['nombre']} {$row['apellido']}</td>
                                    <td>{$row['estado']}</td>
                                    <td>
                                        <form method='POST' style='display:inline;'>
                                            <input type='hidden' name='id_solicitud' value='{$row['id_solicitud']}'>
                                            <input type='hidden' name='nuevo_estado' value='proceso'>
                                            <button type='submit'>Proceso</button>
                                        </form>
                                        <form method='POST' style='display:inline; margin-left:5px;'>
                                            <input type='hidden' name='id_solicitud' value='{$row['id_solicitud']}'>
                                            <input type='hidden' name='nuevo_estado' value='terminado'>
                                            <button type='submit'>Terminado</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No tienes citas asignadas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>
