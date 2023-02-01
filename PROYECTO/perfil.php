<?php
require_once 'sesiones.php';
require_once 'bd.php';
require_once 'correo.php';

comprobar_sesion();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['eliminar'])) {
        if (isset($_POST['nombre']) && isset($_POST['contraseña']) && isset($_POST['confirmar']) && isset($_FILES['image'])) {
            if ($_POST['contraseña'] == $_POST['confirmar']) {
                if ($_FILES['image']['name']) {
                    $formato = substr($_FILES['image']['name'], strlen($_FILES['image']['name']) - 3);
                    if ($formato != 'jpg') {
                        $error = 2;
                    } else {
                        modificar_usuario($_SESSION['usuario']['id'], $_POST['nombre'], $_POST['contraseña'], $_FILES);
                    }
                } else {
                    modificar_usuario($_SESSION['usuario']['id'], $_POST['nombre'], $_POST['contraseña'], "");
                }
            } else {
                $error = 1;
            }
            if (isset($_SESSION['usuario']['medico'])) {
                if ($_FILES['cv']['name']) {
                    $formato = substr($_FILES['cv']['name'], strlen($_FILES['cv']['name']) - 3);
                    if ($formato != 'pdf') {
                        $error = 3;
                    } else {
                        if (file_exists('CV/cv' . $_SESSION['usuario']['id'])) {
                            unlink('CV/cv' . $_SESSION['usuario']['id'] . "/curriculum.pdf");
                        } else {
                            mkdir('CV/cv' .  $_SESSION['usuario']['id']);
                        }
                        move_uploaded_file($_FILES["cv"]["tmp_name"], 'CV/cv' . $_SESSION['usuario']['id'] . '/curriculum.pdf');
                        modificar_medico($_POST['especialidad'], $_POST['hospital'], $_FILES['cv']['name'], $_SESSION['usuario']['id']);
                    }
                } else {
                    modificar_medico($_POST['especialidad'], $_POST['hospital'], $_FILES['cv']['name'], $_SESSION['usuario']['id']);
                }
            }
        }
    } else {
        $eliminar_cuenta = true;
        $_SESSION['verificar']['codigo'] = rand(1, 9999);
        verificacionCorreo($_SESSION['usuario']['correo'], $_SESSION['verificar']['codigo'], 2);
    }
}
$_SESSION['usuario'] = comprobar_usuario($_SESSION['usuario']['correo'], "", 1);

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
    <?php require 'header&nav.php';
    if (!isset($eliminar_cuenta)) {

    ?>
        <h1>Perfil</h1>
        <?php
        if (isset($error)) {
            if ($error == 1) {
                echo "<strong>Las contraseñas no se corresponden</strong>";
            } else if ($error == 2) {
                echo "La imagen no cumple con el formato (.jpg)";
            } else {
                echo "El curriculum no cumple con el formato (.pdf)";
            }
        }
        ?>
        <form action="" class="perfil" method="post" enctype="multipart/form-data">
            <div class="contenedor_perfil">
                <table>
                    <tr>
                        <td>
                            <?php
                            echo "<img src=\"";
                            if ($_SESSION['usuario']['foto']) {
                                echo "foto.php?id=" . $_SESSION['usuario']['id'];
                            } else {
                                echo "foto.png";
                            }
                            echo "\" alt=\Imagen Usuario\">";
                            ?></td>
                        <?php if (isset($_SESSION['usuario']['medico'])) {
                            $nota = media_valoracion($_SESSION['usuario']['medico']['num_col']);
                            if ($nota['media']) {
                                echo "<td>Puntuación media:</td><td>" . $nota['media'] . "</td>";
                            }
                        }
                        ?>
                    </tr>
                </table>
            </div>
            <table>
                <tr>
                    <td><label for="nombre">Correo:</label></td>
                    <td><input type="text" value="<?php echo $_SESSION['usuario']['correo']; ?>" disabled><br></td>
                </tr>
                <tr>
                    <td><label for="nombre">Nombre:</label></td>
                    <td><input type="text" name="nombre" value="<?php echo $_SESSION['usuario']['nombre']; ?>"></td>
                </tr>
                <?php
                if (isset($_SESSION['usuario']['medico'])) {
                    echo "<tr><td><label for=\"num_col\">Numero de colegiado:</label></td><td><input type=\"text\" name=\"num_col\" value=\"" . $_SESSION['usuario']['medico']['num_col'] . "\" disabled></td></tr>";
                    echo "<tr><td><label for=\"especialidad\">Especialidad:</label></td><td><input type=\"text\" name=\"especialidad\" value=\"" . $_SESSION['usuario']['medico']['especialidad'] . "\"></td></tr>";
                    echo "<tr><td><label for=\"hospital\">Hospital:</label></td><td><input type=\"text\" name=\"hospital\" value=\"" . $_SESSION['usuario']['medico']['hospital'] . "\"></td></tr>";
                    if ($_SESSION['usuario']['medico']['cv']) {
                        echo "<tr><td><label for=\"cv\">Actualizar curriculum:</label></td><td><input type=\"file\" name=\"cv\"></td><td><a href=\"CV/cv" . $_SESSION['usuario']['id'] . "/curriculum.pdf\" target=\"_blank\">" . $_SESSION['usuario']['medico']['cv'] . "</a></td></tr>";
                    } else {
                        echo "<tr><td><label for=\"cv\">Subir curriculum:</label></td><td><input type=\"file\" name=\"cv\" accept=\"application/pdf\"></td></tr>";
                    }
                }
                ?>
                <tr>
                    <td><label for="contraseña">Nueva contraseña:</label></td>
                    <td><input type="password" name="contraseña"></td>
                </tr>
                <tr>
                    <td><label for="confirmar">Confirmar contraseña:</label></td>
                    <td><input type="password" name="confirmar"></td>
                </tr>
                <tr>
                    <td><label for="foto">Foto de perfil:</label></td>
                    <td><input type="file" name="image" accept=".jpg"></td>
                    <td><?php if ($_SESSION['usuario']['foto']) echo "<a href=\"eliminar_foto.php\">Eliminar foto</a>"; ?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="eliminar" value="eliminar">Eliminar cuenta</td>
                </tr>
                <tr>
                    <td><button type="submit">Guardar cambios</button></td>
                </tr>
            </table>
        </form>
    <?php
    } else {
        echo "Se ha enviado un correo de verificacion a <strong>" . $_SESSION['usuario']['correo'] . "</strong>";
    }
    ?>
</body>

</html>