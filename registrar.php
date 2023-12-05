<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'trejo';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir datos del formulario
$nombre = $conn->real_escape_string($_POST['nombre']); // Evitar inyección SQL
$instrumento = $conn->real_escape_string($_POST['instrumento']);
$horario = $conn->real_escape_string($_POST['horario']);

// Verificar si el horario ya existe en la tabla Horarios
$sqlVerificarHorario = "SELECT id FROM horarios WHERE horario = '$horario'";
$resultVerificarHorario = $conn->query($sqlVerificarHorario);

if ($resultVerificarHorario->num_rows == 0) {
    // Si el horario no existe, lo registramos en la tabla Horarios
    $sqlRegistroHorario = "INSERT INTO horarios (horario) VALUES ('$horario')";
    if ($conn->query($sqlRegistroHorario) !== TRUE) {
        echo "Error al registrar el horario: " . $conn->error;
        $conn->close();
        exit; // Salir si hay un error al registrar el horario
    }
}

// Verificar si el instrumento ya existe en la tabla Grupos
$sqlVerificarInstrumento = "SELECT id FROM grupos WHERE instrumento = '$instrumento'";
$resultVerificarInstrumento = $conn->query($sqlVerificarInstrumento);

if ($resultVerificarInstrumento->num_rows == 0) {
    // Si el instrumento no existe, lo registramos en la tabla Grupos
    $sqlRegistroInstrumento = "INSERT INTO grupos (instrumento) VALUES ('$instrumento')";
    if ($conn->query($sqlRegistroInstrumento) !== TRUE) {
        echo "Error al registrar el instrumento: " . $conn->error;
        $conn->close();
        exit; // Salir si hay un error al registrar el instrumento
    }
}

// Se puede registrar al alumno
$sqlRegistroAlumno = "INSERT INTO alumnos (nombre, instrumento, horario) VALUES ('$nombre', '$instrumento', '$horario')";
if ($conn->query($sqlRegistroAlumno) === TRUE) {
    echo "Registro exitoso";

    // Lógica para generar los reportes
    generarReportePorGrupo($conn);
    generarReportePorTurno($conn);
    generarReportePorNombre($conn);
} else {
    echo "Error al registrar al alumno: " . $conn->error;
}

$conn->close();

// Funciones para generar reportes
function generarReportePorGrupo($conn)
{
    $sqlReporteGrupo = "SELECT * FROM alumnos ORDER BY instrumento";
    $resultReporteGrupo = $conn->query($sqlReporteGrupo);

    if ($resultReporteGrupo->num_rows > 0) {
        echo "<h2>Reporte por Grupo:</h2>";
        echo "<ul>";
        while ($row = $resultReporteGrupo->fetch_assoc()) {
            echo "<li>{$row['nombre']} - {$row['instrumento']} - {$row['horario']}</li>";
        }
        echo "</ul>";
    } else {
        echo "No hay datos para el reporte por grupo.";
    }
}

function generarReportePorTurno($conn)
{
    $sqlReporteTurno = "SELECT * FROM alumnos ORDER BY horario";
    $resultReporteTurno = $conn->query($sqlReporteTurno);

    if ($resultReporteTurno->num_rows > 0) {
        echo "<h2>Reporte por Turno:</h2>";
        echo "<ul>";
        while ($row = $resultReporteTurno->fetch_assoc()) {
            echo "<li>{$row['nombre']} - {$row['instrumento']} - {$row['horario']}</li>";
        }
        echo "</ul>";
    } else {
        echo "No hay datos para el reporte por turno.";
    }
}

function generarReportePorNombre($conn)
{
    $sqlReporteNombre = "SELECT * FROM alumnos ORDER BY nombre";
    $resultReporteNombre = $conn->query($sqlReporteNombre);

    if ($resultReporteNombre->num_rows > 0) {
        echo "<h2>Reporte por Nombre (Alfabéticamente):</h2>";
        echo "<ul>";
        while ($row = $resultReporteNombre->fetch_assoc()) {
            echo "<li>{$row['nombre']} - {$row['instrumento']} - {$row['horario']}</li>";
        }
        echo "</ul>";
    } else {
        echo "No hay datos para el reporte por nombre.";
    }
}
?>

