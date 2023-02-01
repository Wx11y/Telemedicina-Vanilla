<?php
require_once 'sesiones.php';
require_once 'bd.php';

comprobar_sesion();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $error = false;
    if (isset($_GET['id'])) {
        if (!$medico = listar_medicos($_GET['id'])) {
            $error = true;
        }
    } else {
        $error = true;
    }
    if ($error) {
        header('Location: lista_medicos.php');
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
</head>

<body>
    <?php require 'header&nav.php' ?>
    <h1>Perfil</h1>
    <div class="contenedor_perfil">
        <?php
        echo "<img src=\"";
        if ($medico[0]['foto']) {
            echo "foto.php?id=" . $medico[0]['id'];
        } else {
            echo "foto.png";
        }
        echo "\" alt=\Imagen Usuario\">";
        ?>
    </div>
    <table>
        <tr>
            <td><label for="nombre">Correo:</label></td>
            <td><input type="text" value="<?php echo $medico[0]['correo']; ?>" disabled></td>
        </tr>
        <tr>
            <td><label for="nombre">Nombre:</label></td>
            <td><input type="text" name="nombre" value="<?php echo $medico[0]['nombre']; ?>" disabled></td>
        </tr>
        <tr>
            <td><label for="num_col">Numero de colegiado:</label></td>
            <td><input type="text" name="num_col" value="<?php echo $medico[0]['colegiado']; ?>" disabled></td>
        </tr>
        <tr>
            <td><label for="especialidad">Especialidad:</label></td>
            <td><input type="text" name="especialidad" value="<?php echo $medico[0]['especialidad']; ?>" disabled></td>
        </tr>
        <tr>
            <td><label for="hospital">Hospital:</label></td>
            <td><input type="text" name="hospital" value="<?php echo $medico[0]['hospital']; ?>" disabled></td>
        </tr>
        <?php if ($medico[0]['cv']) echo "<tr><td>Curriculum: </td><td><a href=\"CV/cv" . $medico[0]['id'] . "/curriculum.pdf\" target=\"_blank\">curriculum.pdf</a></td></tr>";
        $nota = media_valoracion($medico[0]['colegiado']);
        if ($nota['media']) {
            echo "<tr><td>Puntuación media:</td><td>" . $nota['media'] . "</td></tr>";
        }
        ?>

    </table>






</body>

</html>