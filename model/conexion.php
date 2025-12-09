<?php
$contrasena = '';
$usuario = 'root';
$nombrebd = 'sistema colorlink';
$host = 'localhost';

try{
   $db= new PDO("mysql:host=".$host.";dbname=".$nombrebd, $usuario, $contrasena);
   //$db->exec("SET CHARACTER SET utf8");
      // echo "Conexión exitosa";
   } catch (Exception $e) {
   echo "Error de conexión " . $e->getMessage();
}

$URL = "http://localhost/Sistema%20impresion%20DTF%20ColorLink/";

date_default_timezone_set("America/Caracas");
$fechaHora = date("Y-m-d h:i:s");