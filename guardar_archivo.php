<?php

// Para obtener el archivo con las configuraciones de la app
require "config.php";

// Validación de que se envió el archivo
if (empty($_FILES) || !isset($_FILES["archivo"])) {
    // Si no se envió el archivo, redirect al App Root (Home)
    header("Location: " . APP_ROOT);
    exit();  // finalizamos la ejecución de este archivo PHP
}

// Obtención de los datos del archivo subido
$archivo = $_FILES["archivo"];  // Assoc array con los datos del archivo subido
$tamaño = $archivo["size"];  // tamaño del archivo en bytes
$nombreArchivo = $archivo["name"];  // nombre original del archivo subido
$rutaTemporal = $archivo["tmp_name"];  // Obtención de la ruta temporal del archivo

// Se determina la ruta donde se guardará el archivo subido
$rutaAGuardar = DIR_UPLOAD . $nombreArchivo;

// Guardamos el archivo del directorio temporal a la ruta final
$seGuardoArchivo = move_uploaded_file($rutaTemporal, $rutaAGuardar); 
if (!$seGuardoArchivo) {  // No se guardo?
    // TODO: Mejorar el control de errores para cuando no se guarda
    echo "NO SE GUARDO :(";
    exit();
}

// Además de archivos, podermos recibir más datos
$otroDato = filter_input(INPUT_POST, "otroDato");

// Redirect al Home después de guardar el archivo
header("Location: " . APP_ROOT );
