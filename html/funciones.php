<?php

function consulta($sql)
	{
	$link = mysqli_connect("localhost", "root", "iesconselleria");
	mysqli_select_db($link, "iesconselleria");
	$tildes = $link->query("SET NAMES 'UTF8'");
	$datos = mysqli_query($link, $sql);
	return $datos;
	}

function login()
	{
	if (!isset($_REQUEST['Enviar']))
		{
		$pagina = "<form action='index.php' method='post'>
Usuario: <input type='text' name='Usuario'><br>
Contraseña: <input type='password' name='pswd'>
<input type='submit' name='Enviar' value='Entrar'>
</form>";
		echo $pagina;
		}
	else
		{
		if (isset($_REQUEST['Enviar']))
			{
			$Usuario = $_REQUEST['Usuario'];
			$pswd = $_REQUEST['pswd'];
//			$cond = "SELECT * FROM usuarios where nombre='" . $Usuario . "' and pwd='" . $pswd . "';";
//			$result = mysqli_query($link, $cond);

			$cond = "SELECT * FROM usuarios where nombre='" . $Usuario . "' and pwd='" . $pswd . "';";
			$result = consulta($cond);

			if ($row = mysqli_fetch_array($result))
				{
				$_SESSION['usuario_valido'] = $Usuario;
				$_SESSION['pswd'] = $pswd;
				header('Location:index.php');
				}
			else
				{
				echo "usuario incorrecto";
				echo "<a href='index.php'> Volver a intentar";
				}
			mysqli_free_result($result);
			mysqli_close($link);
			}
		}
	}

function salir()
	{
	session_destroy();
	unset($_SESSION['usuario_valido']);//Borrar variables
	unset($_SESSION['pswd']);//Borrar variables
	}

function subida()
	{
// Almacenes de múscia
$dir="/var/www/musicasubida";
$rid="/var/www/musicaenuso";

	$resultado = "";//Variable vacia para los resultados de las operaciones


	$musica_elegida = mysqli_fetch_array(consulta("SELECT * from musica"));// Recoger estado actual de música


	$t = DIRECTORY_SEPARATOR;//Estable el separador por defecto del sistema / o \

	$primerahora = $_REQUEST['primerahora'];
	$patios = $_REQUEST['patios'];
	$ultimahora = $_REQUEST['ultimahora'];

	if ($primerahora != $musica_elegida['Primera'])//Si la música seleccionada es diferente de la última guardada
		{

		if (copy("$dir$t$primerahora", "$rid/PRIMERA.mp3"))//Copiar la canción al directorio de la musica en uso con el nombre apropiado
			{

			$result = consulta("UPDATE musica set Primera='" . $primerahora . "'");
			$resultado .= "<p>Primera hora: SE HA CAMBIADO</p>";
			}
		else
			{
			echo "Ha ocurrido un error";//Al no haberse copiado correctamente
			}
		}
	else
		{
		$resultado .= "<p>Primera hora: NO SE HA CAMBIADO</p>";
		}
	if ($patios != $musica_elegida['Patio'])
		{
		if (copy("$dir$t$patios", "$rid/PATIOS.mp3"))
			{

			$result = consulta("UPDATE musica set Patio='" . $patios . "'");
			$resultado .= "<p>Patios: SE HA CAMBIADO</p>";
			}
		else
			{
			echo "Ha ocurrido un error";
			}
		}
	else
		{
		$resultado .= "<p>Patios: NO SE HA CAMBIADO</p>";
		}
	if ($ultimahora != $musica_elegida['Ultima'])
		{
		if (copy("$dir$t$ultimahora", "$rid/ULTIMA.mp3"))
			{

			$result = consulta("UPDATE musica set Ultima='" . $ultimahora . "'");
			$resultado .= "<p>Última hora: SE HA CAMBIADO</p>";
			}
		else
			{
			echo "Ha ocurrido un error";
			}
		}
	else
		{
		$resultado .= "<p>Última hora: NO SE HA CAMBIADO</p>";
		}
	echo "<div id='resultado'>$resultado</div>";
	}

function seleccion()
	{
	$t = DIRECTORY_SEPARATOR;//Estable el separador por defecto del sistema / o \
	$dir = "/var/www/musicasubida";
	$file_mimes = ['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp3', 'audio/mpeg3', 'audio/x-mpeg-3'];//Array con los tipos de formato y/o extensión permitidos

	$resultado = "";

	if (in_array($_FILES['musica']['type'], $file_mimes/*['audio/mpeg', 'mpga', 'mp2', 'mp3', 'audio/wav', 'audio/x-wav']*/))//Comprobación de la extensión del archivo a subir
		{
		if (is_uploaded_file($_FILES['musica']['tmp_name']))
			{

			$finfo = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
			//		echo finfo_file($finfo, $_FILES['musica']['tmp_name']);
			if (in_array(finfo_file($finfo, $_FILES['musica']['tmp_name']), $file_mimes))//Comprobación headers/MIME Types archivos a subir
				{

				$Nombremp3 = basename($_FILES['musica']['name']);
				if (move_uploaded_file($_FILES['musica']['tmp_name'], "$dir$t$Nombremp3"))
					{
					$resultado = "<p>Música añadida a la lista</p>";
					}
				else
					{
					$resultado = "<p>El archivo " . $_FILES['musica']['name'] . " no se ha subido</p>";
					echo "<a href='index.php'> Volver";
					}
				}
			}
		else
			{
			$resultado = "<p>El archivo " . $_FILES['musica']['name'] . " no se ha subido</p>";
			echo "<a href='principal.php'> Volver";
			}
		}
	else
		{
		$resultado = "El tipo introducido no está soportado";
		}
	echo "<div id='resultado'>$resultado</div>";
	}
