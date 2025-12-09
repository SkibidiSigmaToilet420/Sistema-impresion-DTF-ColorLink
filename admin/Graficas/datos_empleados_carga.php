<?php
// Archivo: admin/Graficas/datos_empleados_carga.php

include('../../model/conexion.php'); 
header('Content-Type: application/json');

// Estados que queremos visualizar en la gráfica (ajusta estos según tu sistema)
$estados_interes = ['Produccion', 'Pendiente', 'Verificado', 'Finalizado', 'Rechazado'];

// Consulta SQL: Une pedidos con usuarios (empleados) y cuenta los pedidos por estado.
$sql = "SELECT 
            CONCAT(u.Nombre, ' ', u.Apellido) as empleado_nombre, 
            p.Estado_Pedido, 
            COUNT(p.Id_pedido) as total
        FROM pedidos p
        INNER JOIN usuarios u ON p.Empleado_Encargado = u.Cedula
        WHERE u.Tipo_Usuario = '2' 
          AND p.Estado_Pedido IN ('" . implode("', '", $estados_interes) . "')
        GROUP BY empleado_nombre, p.Estado_Pedido
        ORDER BY empleado_nombre ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Formateo de Datos para Gráfico Apilado (Chart.js) ---

$empleados = []; // Almacena todos los nombres de los empleados (Labels del eje X)
$data_por_estado = []; // Almacena las series de datos (los estados)

// 1. Recopilar todos los empleados únicos
foreach ($resultados as $row) {
    if (!in_array($row['empleado_nombre'], $empleados)) {
        $empleados[] = $row['empleado_nombre'];
    }
}

// 2. Construir la estructura de datos apilados
foreach ($estados_interes as $estado) {
    // Inicializar el dataset para este estado
    $dataset = [
        'label' => $estado,
        'data' => array_fill(0, count($empleados), 0), // Llenar con ceros, uno por empleado
        'backgroundColor' => '#6C757D' // Color de fallback (se ajustará abajo)
    ];

    // Llenar el dataset con los conteos reales
    foreach ($resultados as $row) {
        if ($row['Estado_Pedido'] === $estado) {
            $empleado_index = array_search($row['empleado_nombre'], $empleados);
            if ($empleado_index !== false) {
                $dataset['data'][$empleado_index] = (int)$row['total'];
            }
        }
    }
    
    // Asignar color específico al estado (ajustar estos colores)
    $color_map = [
        'Produccion' => '#0D6EFD',
        'Pendiente' => '#FFC107',
        'Verificado' => '#6F42C1',
        'Finalizado' => '#198754',
        'Rechazado' => '#DC3545'
    ];
    $dataset['backgroundColor'] = $color_map[$estado] ?? '#6C757D';

    $data_por_estado[] = $dataset;
}

// Devolver el resultado final
echo json_encode(['labels' => $empleados, 'datasets' => $data_por_estado]);
?>