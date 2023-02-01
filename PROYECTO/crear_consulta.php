<?php
require_once 'sesiones.php';
require_once 'bd.php';

comprobar_sesion();
if (isset($_SESSION['usuario']['medico'])) {
    header('Location: inicio.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['consultar_medicos']) || $_POST['asunto'] == "" || $_POST['mensaje'] == "") {
        $error = 1;
    } else {
        foreach ($_POST['consultar_medicos'] as $colegiado) {
            if ($num_consulta = insertar_consulta(substr($_POST['asunto'], 0, 20), $_SESSION['usuario']['id'], $colegiado)) {
                mkdir('Consultas/consulta' . $num_consulta);
                if ($codigo_mensaje = insertar_mensajes(substr($_POST['mensaje'], 0, 255), $num_consulta, $_SESSION['usuario']['id'], $_FILES['fichero_consulta']['name'])) {
                    if ($_FILES['fichero_consulta']['name']) {
                        mkdir('Consultas/consulta' . $num_consulta . '/mensaje' . $codigo_mensaje);
                        move_uploaded_file($_FILES["fichero_consulta"]["tmp_name"], 'Consultas/consulta' . $num_consulta . '/mensaje' . $codigo_mensaje . '/' . $_FILES["fichero_consulta"]["name"]);
                    }
                    header('Location: bandeja.php');
                }
            }
            $error = 2;
        }
    }
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
    <style>

    </style>
</head>

<body>
    <?php require 'header&nav.php';
    if (isset($error)) {
        if ($error == 0) {
            echo '<p>Complete los campos</p>';
        } else {
            echo '<p>Algo no salio bien, intentelo de nuevo más tarde</p>';
        }
    }
    ?>
    <h1>Crear consulta</h1>
    <form enctype="multipart/form-data" action="" method="post">
        <label for="asunto">Asunto</label>
        <input type="text" id="asunto" name="asunto" maxlength="20" require><br>
        <label for="mensaje">Mensaje (Explica con todo detalle)</label><br>
        <textarea maxlength="255" name="mensaje" id="mensaje" cols="30" rows="10" require></textarea><br>
        <span>Adjuntar:</span>
        <input type="file" name="fichero_consulta"><br>
        <?php
        if ($lista_medicos = listar_medicos()) {
            echo '<table>';
            echo "<tr><th>Médico</th><th>Especialidad</th><th>Hospital</th></tr>";
            foreach ($lista_medicos as $medico) {
                echo '<tr>';
                $desac = "";
                if (5 <= comprobar_carga($medico['colegiado'])) {
                    $desac = "disabled";
                } else {
                    if (comprobar_consulta_previa($medico['colegiado'], $_SESSION['usuario']['id'])) {
                        $desac = "disabled";
                    }
                }
                echo "<td><input type=\"checkbox\" name='consultar_medicos[]' value='" . $medico['colegiado'] . "' $desac>" . $medico['nombre'] . "</td>";
                echo "<td>" . $medico['especialidad'] . "</td>";
                echo "<td>" . $medico['hospital'] . "</td>";
                echo '</tr>';
            }
            echo '</table>';
        } else {
            header('Location: inicio.php?error');
        }
        ?>
        <button type="submit">Enviar</button>
        <button type="reset">Borrar</button>
    </form>
</body>

</html>