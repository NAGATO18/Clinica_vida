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

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Recoger datos del formulario
$id_solicitud = $_POST['id_solicitud'];
$id_medico = $_POST['id_medico'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

// Convertir fecha y hora seleccionadas a datetime
$fechaHoraInicio = date('Y-m-d H:i:s', strtotime("$fecha $hora"));
$fechaHoraFin = date('Y-m-d H:i:s', strtotime("$fecha $hora +90 minutes"));

// Verificar si el médico ya tiene una cita en ese rango horario
$sql_verificacion = "SELECT COUNT(*) AS total FROM solicitudes_cita 
  WHERE id_medico = ? 
  AND estado = 'asignada' 
  AND (
    (CONCAT(fecha_asignada, ' ', hora_asignada) < ? AND DATE_ADD(CONCAT(fecha_asignada, ' ', hora_asignada), INTERVAL 90 MINUTE) > ?)
  )";

$stmt_verificacion = $conexion->prepare($sql_verificacion);
$stmt_verificacion->bind_param("sss", $id_medico, $fechaHoraFin, $fechaHoraInicio);
$stmt_verificacion->execute();
$result_verificacion = $stmt_verificacion->get_result();
$row_verificacion = $result_verificacion->fetch_assoc();

if ($row_verificacion['total'] > 0) {
    echo "<script>alert('El médico ya tiene una cita en ese rango horario. Por favor, elige otro horario.'); window.location.href='administrador.php';</script>";
    exit();
}

// Actualizar la solicitud con los datos asignados
$sql = "UPDATE solicitudes_cita SET 
            id_medico = ?, 
            fecha_asignada = ?, 
            hora_asignada = ?, 
            estado = 'asignada'
        WHERE id_solicitud = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("issi", $id_medico, $fecha, $hora, $id_solicitud);

if ($stmt->execute()) {
    // Obtener el correo del administrador (desde la sesión)
    $id_admin = $_SESSION['id_login'];
    $sql_admin = "SELECT correo FROM login WHERE id_login = ?";
    $stmt_admin = $conexion->prepare($sql_admin);
    $stmt_admin->bind_param("i", $id_admin);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin = $result_admin->fetch_assoc();
    $correo_admin = $admin['correo'];

    // Obtener el correo del paciente que solicitó la cita
    $sql_paciente = "SELECT correo FROM login WHERE id_login = (SELECT id_paciente FROM solicitudes_cita WHERE id_solicitud = ?)";
    $stmt_paciente = $conexion->prepare($sql_paciente);
    $stmt_paciente->bind_param("i", $id_solicitud);
    $stmt_paciente->execute();
    $result_paciente = $stmt_paciente->get_result();
    $paciente = $result_paciente->fetch_assoc();
    $correo_paciente = $paciente['correo'];

    // Obtener el nombre del médico
    $sql_medico = "SELECT nombre FROM login WHERE id_login = ?";
    $stmt_medico = $conexion->prepare($sql_medico);
    $stmt_medico->bind_param("i", $id_medico);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();
    $medico = $result_medico->fetch_assoc();
    $nombre_medico = $medico['nombre'];

    // Crear una instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurar el servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sasukeuchiha7777.xyz@gmail.com'; // Usar el correo de la empresa
        $mail->Password   = 'mdky cnyw qarf vedx';   // Contraseña de la aplicación generada

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Configurar el correo del administrador (si es necesario)
        $mail->setFrom('narutouzumaki9999.abc@gmail.com', 'Sistema Citas Médicas');

        // Enviar correo al paciente
        $mail->addAddress($correo_paciente, 'Paciente');
        $mail->isHTML(true);
        $mail->Subject = 'Tu cita médica ha sido asignada';
        $mail->Body    = "Hola,<br><br>Tu cita médica ha sido asignada correctamente.<br><br><b>Fecha:</b> $fecha<br><b>Hora:</b> $hora<br><b>Médico:</b> $nombre_medico<br><br>¡Gracias por utilizar nuestro sistema de citas!";

        // Enviar el correo al paciente
        $mail->send();

        // Limpiar las direcciones
        $mail->clearAddresses();

        // Enviar correo al administrador
        $mail->addAddress($correo_admin, 'Administrador');
        $mail->Subject = 'Cita Asignada';
        $mail->Body    = "Hola,<br><br>Has asignado una cita correctamente.<br><br><b>Fecha:</b> $fecha<br><b>Hora:</b> $hora<br><b>Médico:</b> $nombre_medico<br><br>¡Gracias por tu gestión!";

        // Enviar el correo al administrador
        $mail->send();

        echo "<script>alert('Cita asignada y correos enviados al administrador y al paciente.'); window.location.href='administrador.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Cita asignada, pero no se pudo enviar el correo. Error: {$mail->ErrorInfo}'); window.location.href='administrador.php';</script>";
    }
} else {
    echo "<script>alert('Error al asignar la cita.'); window.location.href='administrador.php';</script>";
}

$stmt->close();
$conexion->close();
?>
