<?php
if(!empty($_GET['id'])){

    require_once 'bd.php';
    $res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd", 1);
    
    //Create connection and select DB
    $db = new mysqli($res[0], $res[2], $res[3], $res[1]);
    
    //Check connection
    if($db->connect_error){
       die("Connection failed: " . $db->connect_error);
    }
    
    //Get image data from database
    $result = $db->query("SELECT foto FROM usuarios WHERE id = {$_GET['id']}");
    
    if($result->num_rows > 0){
        $imgData = $result->fetch_assoc();
        
        //Render image
        header("Content-type: image/jpg"); 
        echo $imgData['foto']; 
    }else{
        echo 'Image not found...';
    }
}
