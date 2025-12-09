<?php
// Ruta: /empleados/descargar_diseno.php
// Sube un nivel para encontrar la carpeta 'model'
include('../../model/conexion.php'); 
include('../../plantilla/sesion.php'); 
echo $TipoSesion;

// 1. Obtener ID del Diseño
$ID_Diseno = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($ID_Diseno === 0) {
    die('Error: ID de diseño no válido.');
}

$stmt = $db->prepare("SELECT URL_Diseno, Nombre_Diseno FROM disenos WHERE ID_Diseno = :id");
$stmt->bindParam(':id', $ID_Diseno, PDO::PARAM_INT);
$stmt->execute();
$diseno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diseno) {
    die('Diseño no encontrado.');
}

$url_relativa = $diseno['URL_Diseno']; 
$nombre_archivo = $diseno['Nombre_Diseno']; 


$ruta_fisica = $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/' . $url_relativa;

if (!file_exists($ruta_fisica)) {
    die('Error: El archivo no se encuentra en el servidor. Ruta: ' . $ruta_fisica);
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($nombre_archivo) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($ruta_fisica));

ob_clean();
flush();
readfile($ruta_fisica);
exit;
?>