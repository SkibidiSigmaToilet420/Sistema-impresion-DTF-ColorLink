<?php
include('model/conexion.php');
session_start();
if (isset($_SESSION['incio_sesion'])) {
    unset($_SESSION['incio_sesion']);
    session_destroy();
    header("Location:".$URL);
}