<?php

use PHPMailer\PHPMailer\PHPMailer;

require "vendor/autoload.php";


function config_correo($nombre, $esquema)
{
    $config = new DOMDocument();
    $config->load($nombre);
    $res = $config->schemaValidate($esquema);
    if ($res === FALSE) {
        throw new InvalidArgumentException("Revise fichero de configuración");
    }
    $datos = simplexml_load_file($nombre);
    $host = $datos->xpath("//host");
    $puerto = $datos->xpath("//puerto");
    $usu = $datos->xpath("//usuario");
    $clave = $datos->xpath("//clave");
    $resul = [];
    $resul[] = $host[0];
    $resul[] = $puerto[0];
    $resul[] = $usu[0];
    $resul[] = $clave[0];
    return $resul;
}

function verificacionCorreo($dirdest, $codigo, $tipo = 0)
{
    $res = config_correo("configuracion_correo.xml", "configuracion_correo.xsd");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    // cambiar a 0 para no ver mensajes de error
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "tls";
    $mail->Host       = $res[0];
    $mail->Port       = $res[1];
    $mail->Username   = $res[2];
    $mail->Password   = $res[3];

    //correo de verificacion
    $dirorg = 'noreply@verificacion.com';
    $mail->SetFrom($dirorg, 'Test');
    // asunto
    $asunto = "verificar correo";
    $mail->Subject    = $asunto;
    // cuerpo
    if (!$tipo) {
        $mensaje = "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/PROYECTO/verificacion_correo.php?codigo=$codigo\">Abre este enlace para crear tu cuenta</a>";
    } else if ($tipo == 1) {
        $mensaje = "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/PROYECTO/cambio_clave.php?codigo=$codigo\">Abre este enlace para cambiar tu clave</a>";
    } else {
        $mensaje = "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/PROYECTO/eliminar_usuario.php?codigo=$codigo\">Abre este enlace para eliminar tu cuenta</a>";
    }
    $mail->MsgHTML($mensaje);
    // adjuntos
    //$mail->addAttachment("empleado.xsd");
    // destinatario
    $address = $dirdest;
    $mail->AddAddress($address, "Test");
    // enviar
    $resul = $mail->Send();
    if ($resul) {
        return true;
    } else {
        return false;
    }
}
