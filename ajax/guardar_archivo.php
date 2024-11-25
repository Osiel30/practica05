<?php
require "../config.php"; // Archivo de configuración
header("Content-Type: application/json");

$uploadDir = DIR_UPLOAD; // Ruta para guardar los archivos
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$resObj = ["error" => null, "mensaje" => null];

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error" => "Error al subir el archivo"]);
    exit;
}

// Validar archivo
$archivo = $_FILES['archivo'];
$otroDato = $_POST['otroDato'] ?? '';
$tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
$maxSize = 2 * 1024 * 1024;

if (!in_array($archivo['type'], $tiposPermitidos)) {
    echo json_encode(["error" => "Tipo de archivo no permitido"]);
    exit;
}

if ($archivo['size'] > $maxSize) {
    echo json_encode(["error" => "El archivo supera el tamaño permitido"]);
    exit;
}

// Generar nombre único
$nombreUnico = uniqid() . "-" . basename($archivo['name']);
$rutaArchivo = $uploadDir . $nombreUnico;

// Guardar archivo
if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
    // Guardar en base de datos
    $pesoKB = round($archivo['size'] / 1024, 2);
    $fechaHora = date('Y-m-d H:i:s');
    $userId = $_SESSION['usuario_id']; // Ajusta según cómo manejas sesiones

    // Insertar en tabla archivos
    $stmt = $db->prepare("INSERT INTO archivos (usuario_id, nombre_original, nombre_archivo_guardado, peso_kb, fecha_hora_subida, es_publico, cant_descargas) 
                           VALUES (?, ?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("issds", $userId, $archivo['name'], $nombreUnico, $pesoKB, $fechaHora);
    $stmt->execute();

    // Insertar en log general
    $stmtLog = $db->prepare("INSERT INTO archivos_log_general (usuario_id, operacion, nombre_archivo, fecha_hora) 
                             VALUES (?, 'subida', ?, ?)");
    $stmtLog->bind_param("iss", $userId, $archivo['name'], $fechaHora);
    $stmtLog->execute();

    $resObj['mensaje'] = "Archivo subido con éxito";
} else {
    $resObj['error'] = "Error al guardar el archivo";
}

echo json_encode($resObj);
