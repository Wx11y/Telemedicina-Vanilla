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
    <?php require 'header&nav.php';
    echo "<h1>Consultas de  " . $_SESSION['usuario']['nombre'] . "</h1>";

    if (isset($_SESSION['usuario']['medico'])) {
        $identificador = $_SESSION['usuario']['medico']['num_col'];
        $tipo = 1;
    } else {
        $identificador = $_SESSION['usuario']['id'];
        $tipo = 0;
    }
    if ($lista_consultas = listar_consultas($identificador, $tipo)) {
        echo "<table class=\"bandeja\">";
        echo "<tr><th>Consulta</th>";
        if ($tipo) {
            echo "<th>Paciente</th></tr>";
        } else {
            echo "<th>Medico</th></tr>";
        }
        foreach ($lista_consultas as $consulta) {
            echo '<tr>';
            echo "<td><dl><dt><a href='consulta.php?id=" . $consulta['codigo'] . "'>" . $consulta['asunto'] . "</a></dt>";
            echo "<dd>";
            if ($_SESSION['usuario']['id'] == $consulta['id']) {
                if ($consulta['leido']) {
                    echo "✓✓ ";
                }
                echo "Tú: ";
                if (strlen($consulta['mensaje']) < 15) {
                    echo $consulta['mensaje'];
                } else {
                    echo substr($consulta['mensaje'], 0, 15) . "...";
                }
                echo "</dd></dl></td>";
            } else if ($consulta['leido'] == 0) {
                echo "<strong>";
                if (strlen($consulta['mensaje']) < 15) {
                    echo $consulta['mensaje'];
                } else {
                    echo substr($consulta['mensaje'], 0, 15) . "...";
                }
                echo "</strong></dd></dl></td>";
            } else {
                if (strlen($consulta['mensaje']) < 15) {
                    echo $consulta['mensaje'];
                } else {
                    echo substr($consulta['mensaje'], 0, 15) . "...";
                }
                echo "</dd></dl></td>";
            }
            echo "<td>" . $consulta['nombre'] . "</td>";
            echo '</tr>';
        }
        echo "</table>";
    } else {
        echo "No tienes consultas";
    }
    ?>


</body>

</html>