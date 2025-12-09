<?php
// Archivo: admin/Graficas/datos_materiales.php

// Ruta ajustada. Asumiendo que estás en admin/Graficas/ y el modelo está en /model/
include('../../model/conexion.php'); 

// Establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// Consulta SQL para obtener el Nombre_material y su Cantidad
// Opcional: Podrías querer filtrar por Estado_Material = 'Activo' si tienes ese campo.
$sql = "SELECT Nombre_material, Cantidad 
        FROM materiales
        ORDER BY Cantidad DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar el array de datos
$data = [
    'labels' => [], // Nombres de los materiales (eje: Vinil, Sublimación)
    'data' => []    // Cantidades (eje: 50, 20)
];

// Formatear los resultados para Chart.js
foreach ($resultados as $row) {
    $data['labels'][] = $row['Nombre_material'];
    $data['data'][] = (float)$row['Cantidad']; // Usamos float por si la cantidad es decimal (cm, m, etc.)
}

// Devolver el resultado codificado en JSON
echo json_encode($data);
?>