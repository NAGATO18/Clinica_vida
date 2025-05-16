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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $numero = $_POST['numero'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Insertar el nuevo médico en la tabla login
    $sql_login = "INSERT INTO login (nombre, apellido, numero, rol, correo, contrasena) VALUES (?, ?, ?, 'medico', ?, ?)";
    $stmt_login = $conexion->prepare($sql_login);
    $stmt_login->bind_param("ssiss", $nombre, $apellido, $numero, $correo, $contrasena);
    $stmt_login->execute();
    $id_login = $stmt_login->insert_id; // Obtener el id del login insertado

    // Obtener las especialidades seleccionadas
    if (!empty($_POST['especialidades'])) {
        foreach ($_POST['especialidades'] as $especialidad) {
            // Insertar cada especialidad en la tabla medicos
            $sql_medico = "INSERT INTO medicos (id_login, especialidad) VALUES (?, ?)";
            $stmt_medico = $conexion->prepare($sql_medico);
            $stmt_medico->bind_param("is", $id_login, $especialidad);
            $stmt_medico->execute();
        }

        echo "<script>alert('Médico registrado con éxito.'); window.location.href='gestionar_medicos.php';</script>";
    } else {
        echo "<script>alert('Debes seleccionar al menos una especialidad.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Médico</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h2>Registrar Nuevo Médico</h2>
            <nav>
                <ul>
                    <li><a href="administrador.php">Inicio</a></li>
                    <li><a href="gestionar_pacientes.php">Gestionar Pacientes</a></li>
                    <li><a href="gestionar_medicos.php">Gestionar Médicos</a></li>
                </ul>
            </nav>
        </header>

        <section>
            <form action="crear_medico.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="numero">Número de Teléfono:</label>
                    <input type="text" id="numero" name="numero" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <div class="form-group">
                    <label>Especialidades:</label><br>
                    <label><input type="checkbox" name="especialidades[]" value="Medicina General"> Medicina General</label><br>
                    <label><input type="checkbox" name="especialidades[]" value="Pediatría"> Pediatría</label><br>
                    <label><input type="checkbox" name="especialidades[]" value="Ginecología"> Ginecología</label><br>
                    <label><input type="checkbox" name="especialidades[]" value="Dermatología"> Dermatología</label><br>
                    <label><input type="checkbox" name="especialidades[]" value="Otorrinolaringología"> Otorrinolaringología</label><br>
                </div>

                <div class="form-group">
                    <input type="submit" value="Registrar Médico">
                </div>
            </form>
        </section>
    </div>
</body>
</html>

<?php
$conexion->close();
?>
