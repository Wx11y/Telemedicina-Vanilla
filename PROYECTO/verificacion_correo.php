<?php
require_once 'bd.php';

session_start();
if (!(isset($_SESSION['verificar']) && isset($_SESSION['usuario']) && isset($_GET['codigo']))) {
    header("Location: login.php?redirigido=true");
}
if ($_SESSION['verificar']['codigo'] == $_GET['codigo']) {
    if ($id = insertar_usuario($_SESSION['usuario']['correo'], $_SESSION['usuario']['nombre'], $_SESSION['usuario']['contraseña'])) {
        if (isset($_SESSION['usuario']['colegiado']) && isset($_SESSION['usuario']['especialidad']) && isset($_SESSION['usuario']['hospital'])) {
            insertar_medico($_SESSION['usuario']['colegiado'], $_SESSION['usuario']['especialidad'], $id, $_SESSION['usuario']['hospital']);
        }
        unset($_SESSION['verificar']);
        $_SESSION['usuario'] = comprobar_usuario($_SESSION['usuario']['correo'], $_SESSION['usuario']['contraseña']);
        header("Location: inicio.php");
    }
} else if ($_SESSION['verificar']['tipo']) {
    if (modificar_usuario($_SESSION['usuario']['id'], $_SESSION['usuario']['nombre'], $_SESSION['usuario']['contraseña'], $_SESSION['usuario']['foto'])) {
        $_SESSION['usuario'] = comprobar_usuario($_SESSION['usuario']['correo'], $_SESSION['usuario']['contraseña']);
        unset($_SESSION['verificar']);
        header("Location: inicio.php");
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
</head>

<body>
    <h1>Telemedicina</h1>
    <h3>Oops, algo salió mal</h3>
    <p>intentalo otra vez más tarde</p>
</body>

</html>