<?php

function leer_config($nombre, $esquema, $tipo = 0)
{
	$config = new DOMDocument();
	$config->load($nombre);
	$res = $config->schemaValidate($esquema);
	if ($res === FALSE) {
		throw new InvalidArgumentException("Revise fichero de configuración");
	}
	$datos = simplexml_load_file($nombre);
	$ip = $datos->xpath("//ip");
	$nombre = $datos->xpath("//nombre");
	$usu = $datos->xpath("//usuario");
	$clave = $datos->xpath("//clave");
	$resul = [];
	if ($tipo) {
		$resul[] = $ip[0];
		$resul[] = $nombre[0];
	} else {
		$cad = sprintf("mysql:dbname=%s;host=%s", $nombre[0], $ip[0]);
		$resul[] = $cad;
	}
	$resul[] = $usu[0];
	$resul[] = $clave[0];
	return $resul;
}

function tipo_correo($correo)
{
	$dominio_medico = '@comem.es';
	$correo = trim($correo);

	if (substr($correo, strlen($correo) - strlen($dominio_medico)) == $dominio_medico) {
		return true;
	} else {
		return false;
	}
}

function comprobar_medico($id)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	$bd = new PDO($res[0], $res[1], $res[2]);
	$ins = "select num_col, especialidad, hospital, cv from medicos where id_usuario = $id";
	$resul = $bd->query($ins);
	if ($resul->rowCount() === 1) {
		foreach ($resul as $usuario) {
			$usu['num_col'] = $usuario['num_col'];
			$usu['especialidad'] = $usuario['especialidad'];
			$usu['hospital'] = $usuario['hospital'];
			$usu['cv'] = $usuario['cv'];
			return $usu;
		}
	} else {
		return false;
	}
}

function comprobar_usuario($nombre, $clave = "", $tipo = 0)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$nombre = trim($nombre);

		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select id, correo, nombre, clave, rol, foto, bloquear from usuarios where correo = '$nombre'";
		$resul = $bd->query($ins);
		if ($resul->rowCount() === 1) {
			if ($clave) {
				foreach ($resul as $usuario) {
					$verified = password_verify($clave, $usuario['clave']);
					if ($verified) {
						$usu['id'] = $usuario['id'];
						$usu['correo'] = $usuario['correo'];
						$usu['nombre'] = $usuario['nombre'];
						$usu['rol'] = $usuario['rol'];
						$usu['foto'] = $usuario['foto'];
						$usu['bloquear'] = $usuario['bloquear'];
						if (($medico = comprobar_medico($usu['id']))) {
							$usu['medico'] = $medico;
						}
						return $usu;
					}
				}
			} elseif ($tipo) {
				foreach ($resul as $usuario) {
					$usu['id'] = $usuario['id'];
					$usu['correo'] = $usuario['correo'];
					$usu['nombre'] = $usuario['nombre'];
					$usu['clave'] = $usuario['clave'];
					$usu['rol'] = $usuario['rol'];
					$usu['foto'] = $usuario['foto'];
					$usu['bloquear'] = $usuario['bloquear'];
					if (($medico = comprobar_medico($usu['id']))) {
						$usu['medico'] = $medico;
					}
					return $usu;
				}
			} else {
				return true;
			}
		}
		return false;
	} catch (PDOException $e) {
		return false;
	}
}

function comprobar_consulta($codigo)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select asunto, leido, completado, id_paciente, num_medico, id_usuario from consultas JOIN medicos ON num_medico=num_col WHERE codigo=$codigo";
		$resul = $bd->query($ins);
		if ($resul->rowCount() === 1) {
			foreach ($resul as $consulta) {
				$cons['asunto'] = $consulta['asunto'];
				$cons['leido'] = $consulta['leido'];
				$cons['completado'] = $consulta['completado'];
				$cons['id_paciente'] = $consulta['id_paciente'];
				$cons['num_medico'] = $consulta['num_medico'];
				$cons['id_medico'] = $consulta['id_usuario'];
				return $cons;
			}
		}
		return false;
	} catch (PDOException $e) {
		return false;
	}
}

function datos_consulta($codigo, $tipo)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		if ($datos = comprobar_consulta($codigo)) {
			if ($tipo) {
				$codigo = $datos['id_paciente'];
			} else {
				$codigo = $datos['id_medico'];
			}
			$ins = "select id, nombre, foto from usuarios WHERE id=$codigo";
			$resul = $bd->query($ins);
			if ($resul->rowCount() === 1) {
				foreach ($resul as $consulta) {
					$cons['id'] = $consulta['id'];
					$cons['nombre'] = $consulta['nombre'];
					$cons['foto'] = $consulta['foto'];
					return $cons;
				}
			}
		}
		return false;
	} catch (PDOException $e) {
		return false;
	}
}

function comprobar_carga($num_medico)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select COUNT(*) AS cantidad from consultas where completado = 0 and num_medico=$num_medico";
		$resul = $bd->query($ins);
		if ($resul->rowCount() === 1) {
			foreach ($resul as $consulta) {
				return $consulta['cantidad'];
			}
		}
		return false;
	} catch (PDOException $e) {
		return false;
	}
}
function comprobar_consulta_previa($num_medico, $id_paciente)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select COUNT(*) AS cantidad from consultas where completado = 0 and num_medico=$num_medico and id_paciente = $id_paciente";
		$resul = $bd->query($ins);
		if ($resul->rowCount() === 1) {
			foreach ($resul as $consulta) {
				return $consulta['cantidad'];
			}
		}
		return false;
	} catch (PDOException $e) {
		return false;
	}
}

function insertar_usuario($correo, $nombre, $clave, $rol = 0)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$correo = trim($correo);
		$nombre = trim($nombre);
		$bd = new PDO($res[0], $res[1], $res[2]);
		$hash = password_hash($clave, PASSWORD_DEFAULT);
		$sql = "insert into usuarios(correo, nombre, clave, rol) values('$correo','$nombre', '$hash', $rol)";
		$resul = $bd->query($sql);
		if ($resul) {
			return $bd->lastInsertId();
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function insertar_medico($num_medico, $especialidad, $id_usuario, $hospital = "", $cv = "")
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$num_medico = trim($num_medico);
		$especialidad = trim($especialidad);
		$hospital = trim($hospital);
		$bd = new PDO($res[0], $res[1], $res[2]);
		$sql = "insert into medicos(num_col, especialidad, hospital, cv, id_usuario) values($num_medico,'$especialidad', '$hospital', '$cv', $id_usuario)";
		$resul = $bd->query($sql);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function insertar_consulta($asunto, $id_paciente, $num_medico)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$asunto = trim($asunto);
		$bd = new PDO($res[0], $res[1], $res[2]);
		$sql = "insert into consultas(asunto, id_paciente, num_medico) values('$asunto', $id_paciente, $num_medico)";
		$resul = $bd->query($sql);
		if ($resul) {
			return $bd->lastInsertId();
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function insertar_mensajes($mensaje, $codigo_consulta, $id_usuario, $adjunto = "")
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$mensaje = trim($mensaje);
		$bd = new PDO($res[0], $res[1], $res[2]);
		$sql = "insert into mensajes(mensaje, adjunto, codigo_consulta, id_usuario) values('$mensaje', '$adjunto', $codigo_consulta, $id_usuario)";
		$resul = $bd->query($sql);
		if ($resul) {
			return $bd->lastInsertId();
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function insertar_valoracion($valoracion, $codigo_consulta, $num_medico, $id_usuario)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		// insertar el usuario
		$sql = "insert into valoran(valoracion, codigo_consulta, num_medico, id_usuario) values($valoracion, $codigo_consulta, $num_medico, $id_usuario)";
		$resul = $bd->query($sql);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function existe_valoracion_consulta($codigo_consulta)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		// insertar el usuario
		$sql = "select valoracion from valoran where codigo_consulta=$codigo_consulta";
		$resul = $bd->query($sql);
		if ($resul) {
			foreach ($resul as $valoracion) {
				$val['valoracion'] = $valoracion['valoracion'];
				return $val;
			}
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function media_valoracion($num_medico)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		// insertar el usuario
		$sql = "select round(sum(valoracion)/COUNT(*)) AS media, COUNT(*) AS cantidad from valoran where num_medico=$num_medico";
		$resul = $bd->query($sql);
		if ($resul) {
			foreach ($resul as $valoracion) {
				$val['media'] = $valoracion['media'];
				$val['cantidad'] = $valoracion['cantidad'];
				return $val;
			}
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function listar_usuarios($id)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select id, correo, nombre, foto, rol, bloquear from usuarios where id!=" . $id;
		$resul = $bd->query($ins);
		if ($resul) {
			$lista = [];
			foreach ($resul as $usuario) {
				$usu['id'] = $usuario['id'];
				$usu['correo'] = $usuario['correo'];
				$usu['nombre'] = $usuario['nombre'];
				$usu['foto'] = $usuario['foto'];
				$usu['rol'] = $usuario['rol'];
				$usu['bloquear'] = $usuario['bloquear'];
				$lista[] = $usu;
			}
			return $lista;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function listar_medicos($id = 0, $tipo = 0)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		if ($id) {
			$ins = "select num_col, id, correo, nombre, foto, especialidad, hospital, cv from medicos join usuarios on id=id_usuario where id = $id";
		} else {
			$ins = "select num_col, id, correo, nombre, foto, especialidad, hospital, cv from medicos join usuarios on id=id_usuario order by especialidad";
		}
		if ($id && $tipo) {
			$ins = "select num_col, id, correo, nombre, foto, especialidad, hospital, cv from medicos join usuarios on id=id_usuario where id != $id order by especialidad";
		}
		$resul = $bd->query($ins);
		if ($resul) {
			$lista = [];
			foreach ($resul as $usuario) {
				$usu['colegiado'] = $usuario['num_col'];
				$usu['id'] = $usuario['id'];
				$usu['correo'] = $usuario['correo'];
				$usu['nombre'] = $usuario['nombre'];
				$usu['foto'] = $usuario['foto'];
				$usu['especialidad'] = $usuario['especialidad'];
				$usu['hospital'] = $usuario['hospital'];
				$usu['cv'] = $usuario['cv'];
				$lista[] = $usu;
			}
			return $lista;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function listar_consultas($identificador, $tipo)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		if ($tipo) {
			//mirada del medico
			$ins = "select codigo, asunto, leido, id_paciente, nombre, foto from consultas JOIN usuarios ON id_paciente=id where num_medico=$identificador ORDER BY fecha DESC";
		} else {
			//mirada del paciente
			$ins = "select codigo, asunto, leido, id_paciente, nombre, foto from consultas JOIN medicos ON num_medico=num_col JOIN usuarios ON id_usuario=id WHERE id_paciente=$identificador ORDER BY fecha DESC";
		}
		$resul = $bd->query($ins);

		if ($resul) {
			$lista = [];
			foreach ($resul as $consulta) {
				$cons['codigo'] = $consulta['codigo'];
				$cons['asunto'] = $consulta['asunto'];
				$cons['leido'] = $consulta['leido'];
				$ultimo_mensaje =  listar_mensajes($consulta['codigo']);
				$cons['mensaje'] = $ultimo_mensaje[count($ultimo_mensaje) - 1]['mensaje'];
				$cons['adjunto'] = $ultimo_mensaje[count($ultimo_mensaje) - 1]['adjunto'];
				$cons['id'] = $ultimo_mensaje[count($ultimo_mensaje) - 1]['id'];;
				$cons['nombre'] = $consulta['nombre'];
				$cons['foto'] = $consulta['foto'];
				$lista[] = $cons;
			}
			return $lista;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function listar_mensajes($codigo)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "select codigo, mensaje, adjunto, fecha_hora, id_usuario from mensajes where codigo_consulta = $codigo";
		$resul = $bd->query($ins);
		if ($resul) {
			$lista = [];
			foreach ($resul as $mensaje) {
				$mensj['codigo'] = $mensaje['codigo'];
				$mensj['mensaje'] = $mensaje['mensaje'];
				$mensj['adjunto'] = $mensaje['adjunto'];
				$mensj['fecha'] = $mensaje['fecha_hora'];
				$mensj['id'] = $mensaje['id_usuario'];
				$lista[] = $mensj;
			}
			return $lista;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function modificar_usuario($id, $nombre, $contraseña, $imagen, $tipo = 0)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$con = false;
		$im = false;
		if ($contraseña) {
			$contraseña = password_hash($contraseña, PASSWORD_DEFAULT);
			$con = true;
		}
		if ($imagen) {
			$image = $imagen['image']['tmp_name'];
			$foto = addslashes(file_get_contents($image));
			$im = true;
		}
		if ($con && $im) {
			$ins = "update usuarios set nombre = '$nombre', clave = '$contraseña', foto = '$foto' where id = $id";
		} else if ($con) {
			$ins = "update usuarios set nombre = '$nombre', clave = '$contraseña' where id = $id";
		} else if ($im) {
			$ins = "update usuarios set nombre = '$nombre', foto = '$foto' where id = $id";
		} else {
			$ins = "update usuarios set nombre = '$nombre' where id = $id";
		}
		if ($tipo) {
			$ins = "update usuarios set foto = '' where id = $id";
		}

		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}

function modificar_medico($especialidad, $hospital, $cv, $id_usuario)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		if ($cv) {
			$ins = "update medicos set especialidad = '$especialidad', hospital = '$hospital', cv = '$cv' where id_usuario = $id_usuario";
		} else {
			$ins = "update medicos set especialidad = '$especialidad', hospital = '$hospital' where id_usuario = $id_usuario";
		}
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function modificar_fecha_ultima_consulta($consulta, $fecha)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "update consultas set fecha = '$fecha' where codigo = $consulta";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function consulta_no_leida($consulta)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "update consultas set leido = 0 where codigo = $consulta";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function consulta_completada($consulta)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "update consultas set completado = 1 where codigo = $consulta";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function bloquear_usuario($id, $tipo = 0)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "update usuarios set bloquear = $tipo where id = $id";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function eliminar_usuario($id)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "delete from usuarios where id = $id";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
function leido_consulta($consulta)
{
	$res = leer_config("configuracion_bd.xml", "configuracion_bd.xsd");
	try {
		$bd = new PDO($res[0], $res[1], $res[2]);
		$ins = "update consultas set leido = 1 where codigo = $consulta";
		$resul = $bd->query($ins);
		if ($resul) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
}
