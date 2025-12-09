<?php
// Archivo: Graficas/datos_estados.php

// Incluir la conexión a la base de datos
// Asegúrese de que la ruta a 'conexion.php' sea correcta desde esta ubicación
include('../../model/conexion.php'); 

// Establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// Consulta SQL para agrupar y contar los pedidos por su estado
$sql = "SELECT Estado_Pedido, COUNT(*) as total 
        FROM pedidos 
        GROUP BY Estado_Pedido";

$stmt = $db->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar el array de datos
$data = [
    'labels' => [], // Nombres de los estados (eje: Pendiente, Aceptado)
    'data' => []    // Cantidades (eje: 15, 8)
];

// Formatear los resultados para Chart.js
foreach ($resultados as $row) {
    // Usamos el nombre del estado directamente
    $data['labels'][] = $row['Estado_Pedido'];
    $data['data'][] = (int)$row['total'];
}

// Devolver el resultado codificado en JSON
echo json_encode($data);
?>