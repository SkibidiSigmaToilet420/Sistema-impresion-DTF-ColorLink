<?php
// Archivo: admin/Graficas/datos_ingresos_mensuales.php

// Incluir la conexión a la base de datos (ruta ajustada)
include('../../model/conexion.php'); 

// Establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// La consulta obtiene el mes/año y la suma total de COSTO (Ingresos)
$sql = "SELECT 
            DATE_FORMAT(Fecha_Solicitud, '%Y-%m') as mes_iso,
            DATE_FORMAT(Fecha_Solicitud, '%b %Y') as mes_nombre, 
            SUM(Costo) as total_ingresos
        FROM pedidos
        WHERE Estado_Pedido = 'Finalizado'
          -- Limita a los últimos 6 meses para consistencia con Producción
          AND Fecha_Solicitud >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
        GROUP BY mes_iso, mes_nombre
        ORDER BY mes_iso ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar el array de datos
$data = [
    'labels' => [], // Meses (eje: Ene 2025, Feb 2025)
    'data' => []    // Cantidad total de Ingresos (Costo)
];

// Formatear los resultados para Chart.js
foreach ($resultados as $row) {
    $data['labels'][] = $row['mes_nombre'];
    $data['data'][] = (float)$row['total_ingresos'];
}

// Devolver el resultado codificado en JSON
echo json_encode($data);
?>