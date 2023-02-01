<?php
require_once 'sesiones.php';
comprobar_sesion();
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
    <?php require 'header&nav.php';
    echo "<h1>Bienvenido " . $_SESSION['usuario']['nombre'] . "</h1>";
    if (isset($_SESSION['usuario']['medico'])) {
        header("Location: bandeja.php");
    } else {
        echo "<a href='crear_consulta.php'>Nueva consulta</a>";
    }
    ?>

</body>

</html>