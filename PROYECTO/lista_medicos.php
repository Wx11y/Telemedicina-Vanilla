<?php
require_once 'sesiones.php';
require_once 'bd.php';

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
    <img src="" alt="">
    <?php require 'header&nav.php';
    if (isset($_SESSION['usuario']['medico'])) {
        $lista_medicos = listar_medicos();
    } else {
        $lista_medicos = listar_medicos();
    }
    if ($lista_medicos) {
        echo '<table class="medicos">';
        echo "<tr><th>Nombre</th><th>Especialidad</th><th>Hospital</th><th>Foto</th><th>Puntuación</th></tr>";
        foreach ($lista_medicos as $medico) {
            echo '<tr>';
            echo "<td><a href=\"medico.php?id=" . $medico['id'] . "\">" . $medico['nombre'] . "</a></td>";
            echo "<td> " . $medico['especialidad'] . "</td>";
            echo "<td> " . $medico['hospital'] . "</td>";
            if ($medico['foto']) {
                echo "<td><img src=\"foto.php?id=" . $medico['id'] . "\" alt=\"Imagen Usuario\"></td>";
            } else {
                echo "<td><img src=\"foto.png\" alt=\"Imagen Usuario\"></td>";
            }
            echo "<td>" . media_valoracion($medico['colegiado'])['media'] . "</td>";
        }
    } else {
        echo "no hay medicos registrados";
    }
    ?>

</body>

</html>