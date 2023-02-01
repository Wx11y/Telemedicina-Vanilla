<?php
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesionAdmin();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['opcion']) && isset($_POST['usuarios'])) {
        foreach ($_POST['usuarios'] as $usuario) {
            switch ($_POST['opcion']) {
                case 'bloquear':
                    bloquear_usuario($usuario, 1);
                    break;
                case 'desbloquear':
                    bloquear_usuario($usuario);
                    break;
                case 'borrar':
                    eliminar_usuario($usuario);
                    break;
            }
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

</head>

<body>
    <?php require 'header&nav.php' ?>
    <h1>Modificar Usuarios</h1>
    <form action="" method="post">
        <table>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Bloquedo</th>
                <th>Foto</th>

            </tr>
            <?php

            if ($lista_usuarios = listar_usuarios($_SESSION['usuario']['id'])) {
                foreach ($lista_usuarios as $usuario) {
                    echo '<tr>';
                    echo "<td><input type=\"checkbox\" name='usuarios[]' value='" . $usuario['id'] . "'>" . $usuario['nombre'] . "</td>";
                    echo "<td>" . $usuario['correo'] . "</td>";
                    if ($usuario['rol']) {
                        echo "<td>Admin</td>";
                    } else {
                        echo "<td>Normal</td>";
                    }
                    if ($usuario['bloquear']) {
                        echo "<td>Si</td>";
                    } else {
                        echo "<td>No</td>";
                    }
                    echo "<td><img src=\"";
                    if ($usuario['foto']) {
                        echo "foto.php?id=" . $usuario['id'];
                    } else {
                        echo "foto.png";
                    }
                    echo "\" alt=\Imagen Usuario\" width=\"50px\">";
                    echo "</td>";
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <input type="radio" name="opcion" value="bloquear">Bloquear<br>
        <input type="radio" name="opcion" value="desbloquear">Desbloquear<br>
        <input type="radio" name="opcion" value="borrar">Borrar<br>
        <button type="submit">Actualizar</button>
    </form>
</body>

</html>