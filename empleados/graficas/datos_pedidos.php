<?php
// Archivo: Graficas/datos_pedidos.php

// Incluir la conexión a la base de datos
include('../../model/conexion.php'); 
include('../../plantilla/sesion_datos.php');


$cedula_empleado = $cedulaSesionData;

// Establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// 1. Consulta para agrupar y contar los pedidos por su estado (para la gráfica)
$sql_estados = "SELECT Estado_Pedido, COUNT(*) as total FROM pedidos WHERE Empleado_Encargado = :cedula GROUP BY Estado_Pedido ORDER BY total DESC";

$stmt_estados = $db->prepare($sql_estados);
$stmt_estados->bindParam(':cedula', $cedula_empleado);
$stmt_estados->execute();
$resultados_estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

// 2. Consulta para obtener el TOTAL de pedidos asignados al empleado
$sql_total = "SELECT COUNT(*) as total_pedidos FROM pedidos WHERE Empleado_Encargado = :cedula";
$stmt_total = $db->prepare($sql_total);
$stmt_total->bindParam(':cedula', $cedula_empleado);
$stmt_total->execute();
$resultado_total = $stmt_total->fetch(PDO::FETCH_ASSOC);


// Inicializar el array de datos
$data = [
    'labels' => [], // Nombres de los estados
    'data' => [],    // Cantidades por estado
    'total_pedidos' => (int)$resultado_total['total_pedidos'] // Nuevo: El total general
];

// Formatear los resultados de estados para Chart.js
foreach ($resultados_estados as $row) {
    $data['labels'][] = $row['Estado_Pedido'];
    $data['data'][] = (int)$row['total'];
}

// Devolver el resultado codificado en JSON
echo json_encode($data);
?>