<?php
require_once "bd.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['val']) && isset($_POST['consulta']) && isset($_POST['num_medico']) && isset($_POST['id_paciente'])){
        var_dump($_POST);
        insertar_valoracion((int)$_POST['val'],(int)$_POST['consulta'],(int)$_POST['num_medico'],(int)$_POST['id_paciente']);
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }else{
        header("Location: inicio.php");
    }
}
?>