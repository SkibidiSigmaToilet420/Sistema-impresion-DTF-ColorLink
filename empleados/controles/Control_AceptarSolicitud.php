<?php
include '../../model/conexion.php';
include '../../plantilla/sesion.php';
$IdPedido = $_GET['Codigo'];

$consulta = $db->prepare("SELECT COUNT(*) FROM pedidos WHERE Empleado_Encargado = :cedula AND Estado_Pedido = 'Pendiente'");
$consulta->bindParam(':cedula', $CedulaSesion);
$consulta->execute();
$pedidosAsignados = $consulta->fetchColumn();


if ($pedidosAsignados >= 3) {
    // No permitir aceptar más pedidos
    $_SESSION['PedidoRechazado'] = '¡No puedes aceptar más pedidos! Ya tienes 3 asignados.';
    header("Location:" .$URL. "empleados/Solicitudes.php");
    exit;
}else{
    $sentencia = $db->prepare('UPDATE pedidos 
                            SET Empleado_Encargado = :Empleado_Encargado 
                            WHERE Id_pedido = :Id_pedido;');
    $sentencia->bindParam(':Empleado_Encargado', $CedulaSesion);
    $sentencia->bindParam(':Id_pedido', $IdPedido);
    $sentencia->execute();


    // Obtener correo del cliente y detalles del pedido
    $stmtCorreo = $db->prepare('SELECT u.Correo, u.Nombre, u.Apellido, p.Id_pedido, p.Cantidades, p.Costo, p.Fecha_Entrega, p.Centimetros, p.Estado_Pedido FROM pedidos p INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :IdPedido');
    $stmtCorreo->bindParam(':IdPedido', $IdPedido);
    $stmtCorreo->execute();
    $fila = $stmtCorreo->fetch(PDO::FETCH_ASSOC);
    $correoCliente = $fila['Correo'] ?? null;

    // Preparar detalles para el correo
    $detalle = null;
    if ($fila) {
        $detalle = [
            'Id_pedido' => $fila['Id_pedido'],
            'Cantidades' => $fila['Cantidades'],
            'Centimetros' => $fila['Centimetros'],
            'Costo' => $fila['Costo'],
            'Fecha_Entrega' => $fila['Fecha_Entrega'],
            'Estado_Pedido' => $fila['Estado_Pedido'],
            'NombreCliente' => $fila['Nombre'] . ' ' . $fila['Apellido']
        ];
    }

    // Nota: el envío de la notificación de pedido en revisión se realizará
    // ahora desde la pantalla de PedidosEncargados cuando el empleado
    // confirme 'Aceptado' en la ventana emergente. Aquí ya no se envía el correo.

    $_SESSION['PedidoAceptado'] = 'Solicitud aceptada exitosamente, ahora puedes verificar sus diseños y cambiar su estado';
    header('Location:'.$URL. 'empleados/Solicitudes.php');
}