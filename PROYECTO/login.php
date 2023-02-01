<?php
require_once 'bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$usu = comprobar_usuario($_POST['usuario'], $_POST['clave']);
	if ($usu === false) {
		$err = true;
		$usuario = $_POST['usuario'];
	} else {
		if ($usu['bloquear'] == 0) {
			session_start();
			$_SESSION['usuario'] = $usu;
			header("Location: inicio.php");
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Formulario de login</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/estilos.css">

</head>

<body>
	<h1><a href="login.php">Telemedicina</a></h1>
	<?php if (isset($_GET["redirigido"])) {
		echo "<p>Haga login para continuar</p>";
	} ?>
	<?php if (isset($err) and $err == true) {
		echo "<p> Revise usuario y contraseña</p>";
	} ?>
	<?php if (isset($usu['bloquear'])) {
		echo "<p>El usuario ha sido bloqueado</p>";
	} ?>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
		<table>
			<tr>
				<td><label for="usuario">Usuario</label></td>
				<td><input value="<?php if (isset($usuario)) echo $usuario; ?>" id="usuario" name="usuario" type="text"></td>
			</tr>
			<tr>
				<td><label for="clave">Clave</label></td>
				<td><input id="clave" name="clave" type="password"></td>
			</tr>
		</table>
		<input type="submit">
	</form>
	<a href="recuperarclave.php">Recuperar Contraseña</a><br>
	<a href="crearusuario.php">Registrarse</a>
</body>

</html>