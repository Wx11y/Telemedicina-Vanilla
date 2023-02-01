<?php
function comprobar_sesion(){
	session_start();
	if(!isset($_SESSION['usuario']['id'])){	
		header("Location: login.php?redirigido=true");
	}		
}
function comprobar_sesionAdmin(){
	session_start();
	if(!isset($_SESSION['usuario']['id'])){	
		header("Location: login.php?redirigido=true");
	}else{
		if($_SESSION['usuario']['rol'] != 1){
			header("Location: login.php?redirigido=true");
		}
	}	
}
