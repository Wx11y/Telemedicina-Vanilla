<?php

require_once 'bd.php';
require_once 'correo.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (comprobar_usuario($_POST['correo'])) {
        session_start();
        $_SESSION['usuario']['correo'] = $_POST['correo'];
        $_SESSION['verificar'] = [];
        $_SESSION['verificar']['codigo'] = rand(1, 9999);
        $_SESSION['verificar']['tipo'] = 1;
        if (verificacionCorreo($_POST['correo'], $_SESSION['verificar']['codigo'], $_SESSION['verificar']['tipo'])) {
            header("Location: verificacion_necesaria.php");
        } else {
            unset($_SESSION['verificar']);
            unset($_SESSION['usuario']);
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
    <h1><a href="login.php">Telemedicina</a></h1>
    <h2>Enviar correo de recuperación</h2>
    <form action="" method="post">
        <label for="correo">Correo</label>
        <input type="text" id="correo" name="correo">
        <button type="submit">Enviar Correo</button>
    </form>
</body>

</html>