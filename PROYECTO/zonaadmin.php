<?php
require_once 'sesiones.php';
comprobar_sesionAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <?php require 'header&nav.php' ?>
    <h1>Zona de Administración</h1>
    <a href="modificarUsuarios.php">Modificar Usuarios</a><br>
    <a href="cargarMedicos.php">Cargar Médicos</a><br>
</body>

</html>