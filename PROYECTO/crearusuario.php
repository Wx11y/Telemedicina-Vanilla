<?php

require_once 'bd.php';
require "correo.php";

function pasar_a_comprobacion($tipo = 0)
{
    session_start();
    $usu['correo'] = $_POST['correo'];
    $usu['nombre'] = $_POST['usuario'];
    $usu['contraseña'] = $_POST['password'];
    if ($tipo == 1) {
        $usu['colegiado'] = $_POST['colegiado'];
        $usu['especialidad'] = $_POST['especialidad'];
        $usu['hospital'] = $_POST['hospital'];
    }
    $_SESSION['usuario'] = $usu;
    $_SESSION['verificar'] = [];
    $_SESSION['verificar']['codigo'] = rand(1, 9999);
    $_SESSION['verificar']['tipo'] = 0;
    if (verificacionCorreo($usu['correo'], $_SESSION['verificar']['codigo'])) {
        header('Location: verificacion_necesaria.php');
    } else {
        unset($_SESSION['verificar']);
        unset($_SESSION['usuario']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!comprobar_usuario($_POST['correo'])) {
        if (filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            if (tipo_correo($_POST['correo'])) {
                $correo = $_POST['correo'];
                $usuario = $_POST['usuario'];
                $password = $_POST['password'];
                $datos_medico = true;
            } else {
                pasar_a_comprobacion();
            }
            if (isset($_POST['colegiado']) && isset($_POST['especialidad']) && isset($_POST['hospital'])) {
                pasar_a_comprobacion(1);
            }
        } else {
            $error = 2;
        }
    } else {
        $error = 1;
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
    <h1><a href="login.php">Telemedicina</a></h1>
    <?php
    if (isset($error)) {
        if ($error == 1) {
            echo "Correo ya registrado";
        } else if ($error == 2) {
            echo "Correo no valido";
        }
    }
    ?>
    <form action="" method="POST">
        <table>
            <tr>
                <td><label for="correo">Correo:</label></td>
                <td><input type="email" id="correo" name="correo" value="<?php if (isset($correo)) echo $correo; ?>" require></td>
            </tr>
            <tr>
                <td><label for="usuario">Nombre y Apellidos:</label></td>
                <td><input type="text" id="usuario" name="usuario" value="<?php if (isset($usuario)) echo $usuario; ?>" require></td>
            </tr>
            <tr>
                <td><label for="password">Contraseña:</label></td>
                <td><input type="password" id="password" name="password" value="<?php if (isset($password)) echo $password; ?>" require></td>
            </tr>

            <?php
            if (isset($datos_medico)) {
            ?>
                <tr>
                    <td><label for="colegiado">Numero de colegiado:</label></td>
                    <td><input type="tel" id="colegiado" name="colegiado" pattern="[0-9]{9}" required></td>
                </tr>
                <tr>
                    <td><label for="especialidad">Especialidad:</label></td>
                    <td><input type="text" id="especialidad" name="especialidad" require></td>
                </tr>
                <tr>
                    <td><label for="hospital">Hospital</label></td>
                    <td><input type="text" id="hospital" name="hospital"></td>
                </tr>
            <?php
            }
            ?>
        </table>
        <button type="submit">Registrarse</button>
    </form>
</body>

</html>