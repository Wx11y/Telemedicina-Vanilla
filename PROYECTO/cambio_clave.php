<?php
require_once 'bd.php';
session_start();
if (!(isset($_SESSION['verificar']) && isset($_SESSION['usuario']) && isset($_GET['codigo']))) {
    header("Location: login.php?redirigido=true");
}
if ($_SESSION['verificar']['codigo'] != $_GET['codigo']) {
    header("inicio.php");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clave']) && isset($_POST['confirmar'])) {
        if ($usu = comprobar_usuario($_SESSION['usuario'], "",  $_SESSION['verificar']['tipo'])) {
            $_SESSION['usuario'] = $usu;
            $_SESSION['usuario']['contraseña'] = $_POST['clave'];
            header("Location: verificacion_correo.php?codigo=0");
        }
    }
    $error = true;
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
    <h3>Cambio de contraseña</h3>
    <?php
    if (isset($error)) {
        echo "<h4>Las contraseñas no coinciden</h4>";
    }
    ?>
    <form action="" method="post">
        <label for="contraseña">Nueva contraseña:</label>
        <input type="password" name="clave" require><br>
        <label for="confirmar">Confirmar contraseña:</label>
        <input type="password" name="confirmar" require><br>
        <button type="submit">Cambiar Contraseña</button>
    </form>
</body>

</html>