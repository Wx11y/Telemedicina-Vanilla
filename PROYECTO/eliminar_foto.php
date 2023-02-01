<?php
require_once 'sesiones.php';
require_once 'bd.php';

comprobar_sesion();

modificar_usuario($_SESSION['usuario']['id'],"","","", 1);
header('Location: perfil.php');
