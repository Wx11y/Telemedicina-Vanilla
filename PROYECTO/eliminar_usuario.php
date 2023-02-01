<?php
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesion();
if (!(isset($_SESSION['verificar']['codigo']))){
    header("Location: login.php?redirigido=true");
}
if ($_SESSION['verificar']['codigo'] != $_GET['codigo']) {
    header("inicio.php");
}
eliminar_usuario($_SESSION['usuario']['id']);
session_destroy();
header("Location: login.php");
?>