<?php
require_once 'sesiones.php';
require_once 'bd.php';

comprobar_sesion();
if (!(isset($_GET['id']) && is_numeric($_GET['id']))) {
    $error = true;
}
if ($consulta = comprobar_consulta($_GET['id'])) {
    $_SESSION['consulta'] = $_GET['id'];
    if (!($consulta['id_paciente'] == $_SESSION['usuario']['id'] || ($consulta['id_medico'] == $_SESSION['usuario']['id']))) {
        $error = true;
    }
} else {
    $error = true;
}
if (isset($error)) {
    header('Location: bandeja.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['fin_consulta'])) {
        consulta_completada($_SESSION['consulta']);
    }
    if ($_POST['mensaje'] || $_FILES['fichero']['name']) {
        if (!$codigo_mensaje = insertar_mensajes($_POST['mensaje'], $_SESSION['consulta'], $_SESSION['usuario']['id'], $_FILES['fichero']['name'])) {
            $error = true;
        } else {
            consulta_no_leida($_SESSION['consulta']);
            if ($_FILES['fichero']['name']) {
                mkdir('Consultas/consulta' . $_SESSION['consulta'] . '/mensaje' . $codigo_mensaje);
                move_uploaded_file($_FILES["fichero"]["tmp_name"], 'Consultas/consulta' . $_SESSION['consulta'] . '/mensaje' . $codigo_mensaje . '/' . $_FILES["fichero"]["name"]);
            }
        }
    }
    $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    header("Location: http://" . $url);
}

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

<body onload="location='#final';">


    <?php require 'header&nav.php';
    echo "<h1>" . $consulta['asunto'] . "</h1>";
    if ($consulta['id_paciente'] == $_SESSION['usuario']['id']) {
        $tipo = 0;
    } else {
        $tipo = 1;
    }
    $datos = datos_consulta($_SESSION['consulta'], $tipo);
    if ($datos['foto']) {
        echo "<table><tr>";
        echo "<td><img class=\"consulta_foto\" src=\"foto.php?id=" . $datos['id'] . "\" alt=\"imagen usuario\"></td>";
    } else {
        echo "<td><img class=\"consulta_foto\" src=\"foto.png\" alt=\"imagen usuario\"></td>";
    }
    if (!$tipo) {
        echo "<td><h3>Medico: <a href=\"medico.php?id=" . $datos['id'] .  "\">" . $datos['nombre'] . "</a></h3></td>";
    } else {
        echo "<td><h3>Paciente: " . $datos['nombre'] . "</h3></td>";
    }
    $nota = existe_valoracion_consulta($_SESSION['consulta']);
    if (!$nota && $consulta['completado'] && $consulta['id_paciente'] == $_SESSION['usuario']['id']) {

    ?>
        <form action="valorar.php" method="post">
            <label for="val">Valora tu consulta:</label><input type="radio" name="val" value="1">1<input type="radio" name="val" value="2">2<input type="radio" name="val" value="3">3<input type="radio" name="val" value="4">4<input type="radio" name="val" value="5">5
            <input type="hidden" name="consulta" value="<?php echo $_SESSION['consulta'] ?>">
            <input type="hidden" name="num_medico" value="<?php echo $consulta['num_medico'] ?>">
            <input type="hidden" name="id_paciente" value="<?php echo $consulta['id_paciente'] ?>">
            <input type="submit" value="Enviar">
        </form>
    <?php
    } else if ($nota) {
        echo "<td>Puntuación de la consulta: " . $nota['valoracion'] . "</td>";
    }
    echo "</tr></table>";



    if (isset($error)) {
        echo "<p>Algo no salio bien, intentelo de nuevo más tarde</p>";
    }
    if ($listar_mensajes = listar_mensajes($_SESSION['consulta'])) {
        echo "<div class=\"contenedor_mensajes\">";
        foreach ($listar_mensajes as $mensaje) {
            if ($mensaje['id'] == $_SESSION['usuario']['id']) {
                echo "<div class=\"der\"><p>" . $mensaje['mensaje'];
            } else {
                echo "<div class=\"izq\"><p>" . $mensaje['mensaje'];
            }
            if (is_dir('Consultas/consulta' . $_SESSION['consulta'] . '/mensaje' . $mensaje['codigo'] . '/')) {
                $formato = substr($mensaje['adjunto'], strlen($mensaje['adjunto']) - 3);
                if ($formato == 'png' || $formato == 'jpg') {
                    echo "<img src=\"Consultas/consulta" . $_SESSION['consulta'] . '/mensaje' . $mensaje['codigo'] . '/' . $mensaje['adjunto']  . "\" alt=\"imagen mensaje\" width=\"200px\">";
                } else {
                    echo ' <a href="' . 'Consultas/consulta' . $_SESSION['consulta'] . '/mensaje' . $mensaje['codigo'] . '/' . $mensaje['adjunto'] . "\" download>" . $mensaje['adjunto'] . "</a>";
                }
            }
            echo "</p></div>";
        }


        if ($consulta['leido'] && $listar_mensajes[count($listar_mensajes) - 1]['id'] == $_SESSION['usuario']['id']) {
            echo "<div class=\"leido\"><p>✓✓</p></div>";
        }
        echo "</div>";
        modificar_fecha_ultima_consulta($_SESSION['consulta'], $listar_mensajes[count($listar_mensajes) - 1]['fecha']);
        if ($listar_mensajes[count($listar_mensajes) - 1]['id'] != $_SESSION['usuario']['id']) {
            leido_consulta($_SESSION['consulta']);
        }
    }
    if (!$consulta['completado']) {
    ?>

        <form action="" method="post" enctype="multipart/form-data">
            <textarea maxlength="200" id="mensaje" name="mensaje" id="" cols="40" rows="4" placeholder="Escribe un mensaje"></textarea><br>
            <input type="file" name="fichero"><br>
            <input type="checkbox" name="fin_consulta" id="fin_consulta" value="terminar"><label for="fin_consulta">Finalizar consulta</label><br>
            <button id="env_mensaje" type="submit">Enviar mensaje</button>
        </form>
        <a href="" name="final"></a>
    <?php }
    ?>

</body>

</html>