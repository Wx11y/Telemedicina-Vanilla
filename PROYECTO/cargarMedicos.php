<?php
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesionAdmin();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_FILES['fichero']['name']) {
        $formato = substr($_FILES['fichero']['name'], strlen($_FILES['fichero']['name']) - 4);
        if ($formato != '.xml') {
            $error = 1;
        } else {
            $config = new DOMDocument();
            $config->load($_FILES['fichero']['tmp_name']);
            $res = $config->schemaValidate("configuracion_usuarios.xsd");
            if ($res === FALSE) {
                $error = 2;
            } else {
                $medicos = simplexml_load_file($_FILES['fichero']['tmp_name']);
                $contador = 0;
                foreach ($medicos as $medico) {
                    $correo = $medico->correo;
                    $nombre = $medico->nombre;
                    $clave = $medico->clave;
                    $rol = $medico->rol;
                    $num_col = $medico->num_col;
                    $especialidad = $medico->especialidad;
                    $hospital = $medico->hospital;
                    if ($id = insertar_usuario($correo, $nombre, $clave, $rol)) {
                        if (!insertar_medico($num_col, $especialidad, $id, $hospital)) {
                            $contador++;
                            eliminar_usuario($id);
                        }
                    } else {
                        $contador++;
                    }
                }
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
    <h1>Cargar Médidos</h1>
    <?php
    if (isset($error)) {
        if ($error == 1) {
            echo "El fichero no cumple con el formato (.xml)";
        } else {
            echo "No cumple la validacion";
        }
    }
    if (isset($contador)) {
        if ($contador == 0) {
            echo "Los medicos se agregarón correctamente";
        } else {
            echo "Problema al agregar a $contador medicos";
        }
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fichero" accept=".xml"><br>
        <button type="submit">Cargar médicos</button>
    </form>
</body>

</html>