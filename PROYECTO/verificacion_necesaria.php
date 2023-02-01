<?php
session_start();
if (!isset($_SESSION['verificar'])) {
    header("Location: login.php?redirigido=true");
}
$usuario = $_SESSION['usuario']['correo'];
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
    <p>Se ha enviado un correo de verificacion a <strong><?php echo $usuario; ?></strong> </p>
</body>

</html>