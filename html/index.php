<?php
include 'funciones.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<link rel='stylesheet' href='aspecto/estilo.css' type='text/css'>
<script>
	function mostrar()
	{
		document.getElementById("nuevo_usuario").className="visible";//Ocultar boton "Agregar usuario"
		document.getElementById("boton_nuevo_usuario").className="oculto";//Hacer visible formulario
	}
</script>
</head>
<body>
	<img src="aspecto/logo_ies_conselleria.png" alt="IES Conselleria" height="66" width="157">
<h1>Gestión de audios descansos</h1>
<?php
if (isset($_GET['salir'])/* && $_GET['salir'] == 'si'*/)//Comprobar si esciste variable y su valor es "si"
	{
	salir();//Función salir
	header("Location:index.php");
	}

if (isset($_SESSION['usuario_valido'])){
	
	if (isset($_POST['Enviar']))
		{
		subida();
		}
	
	if (isset($_POST['Subir']))
		{
		seleccion();
		}
	
	
	echo "<div id='usuario'>";
	echo "Cuenta de ".$_SESSION['usuario_valido'];
	echo ' | <a href="?salir">Salir</a>';
	echo "</div>";

$row = mysqli_fetch_array(consulta("SELECT * from musica"));


$directorio = "/var/www/musicasubida";//DIRECTORIO A CAMBIAR

$sdir  = array_diff(scandir($directorio), array('..', '.'));//Compara ambos arrays y muestra los valores que el segundo no tiene

	echo "<form action='index.php' method='post' class='form'>";
	echo "<table class='form'>";

		echo "<legend>Cambio de música</legend>";
		echo   "<tr><td>Primera hora:</td>"; //SELECCIÓN PRIMERA HORA
		echo   "<td><select name='primerahora'>";
	//	echo       "<option value='NONE' selected='selected'>".$row['Primera']."</option>";//BUCLE PARA QUE SALGAN TODAS LAS CANCIONES SUBIDAS
		foreach ($sdir as $key => $nom) //obtenemos un archivo y luego otro sucesivamente
			{
				$resaltar = "";
				if ($row['Primera'] == $nom)//Si la canción de la base de datos coincide con con el nombre de la canción del directorio
				{
					$resaltar = ' selected="selected"';//Establece esa canción como seleccionada
				}
				echo "<option value=\"$nom\"$resaltar>$nom</option>";//Agrega un valor para seleccionar al formulario
			   	
				}

		echo   "</select></td></tr>";


		echo   "<tr><td>Patios:</td>"; //SELECCIÓN PATIOS
		echo   "<td><select name='patios'>";
		foreach ($sdir as $key => $nom)
			{
				$resaltar = "";
				if ($row['Patio'] == $nom)
				{
					$resaltar = ' selected="selected"';
				}
				echo "<option value=\"$nom\"$resaltar>$nom</option>";
			   	
				}
		echo   "</select></td></tr>";



		echo   "<tr><td>Última hora:</td>"; //SELECCIÓN ÚLTIMA HORA
		echo   "<td><select name='ultimahora'>";
			foreach ($sdir as $key => $nom)
			{
				$resaltar = "";
				if ($row['Ultima'] == $nom)
				{
					$resaltar = ' selected="selected"';
				}
				echo "<option value=\"$nom\"$resaltar>$nom</option>";
			   	
				}
		echo   "</select></td></tr>";		
		echo "</table>";
		echo "<input type='submit' value='Enviar' name='Enviar'>";
		echo "</form>";
		
	
		echo "<hr>";
//REPRODUCIR AHORA
if (isset($_REQUEST['ahora'])){
echo "Reproduciendo...";
$ahoramus=$_REQUEST['ahora1'];
$musicaA="$directorio/$ahoramus";
$comando = 'play '. $musicaA .' trim 0 70';//comando para reproducir la música seleccionada desde el segundo "0" hasta el "70"
exec($comando);

echo '<div style="padding: 30px; border: 2px solid red;"><pre>';
echo $comando;
echo '</pre></div>';

echo "$ahoramus";
}

 echo "<form action='index.php' method='post'>";
 echo   "<legend>Reproducir ahora</legend>";
echo "Seleccionar música:";

                echo   "<select name='ahora1'>";
                echo       "<option value='NONE' selected='selected'>Selección</option>";
        foreach ($sdir as $numero => $nom) //obtenemos un archivo y luego otro $
                        {
                                echo "<option value=$nom>$nom</option>";

                                }
 echo   "</select>";
echo "<br><input type='submit' value='Reproducir' name='ahora'/><br>";
echo "</form>";

	
		echo "<hr>";
	
	echo "<form action='index.php' method='post' enctype='multipart/form-data'>";//SUBIR MÚSICA
	echo "<input type='hidden' name='max_file_size' value'1024000'>";//Tamaño máximo permitido
	echo "<legend>Subir música</legend>";
		echo "Fichero: <input type='file' name='musica'>";
		echo "<br>";
		echo "<input type='submit' name='Subir' value='Subir'/>";
	echo "</form>";

if ($_SESSION['usuario_valido']=="admin" && $_SESSION['pswd']=="1234"){//Comprobar usuario para mostrar botón de agregar
	echo "<hr>";
	echo "<legend>Añadir usuario</legend>";
	echo '<button id="boton_nuevo_usuario" onclick="mostrar();">Pulsa para añadir</button>';
}

	
	echo "<form id='nuevo_usuario' class='oculto' action='index.php' method='post' enctype='post'>";//Formulario para agregar usuario (Oculto)
	echo "Nombre: <input type='text' name='nombre'><br>";
	echo "Contraseña: <input type='password' name='psswd'><br>";
	echo "<input type='submit' name='agregar' value='Agregar'></form>";
	
			if (isset($_REQUEST['agregar'])){
		echo "Nuevo usuario añadido";
		$nombreb=$_REQUEST['nombre'];
		$pssw=$_REQUEST['psswd'];
		$anadir="INSERT INTO usuarios VALUES('$nombreb', '$pssw');";
		$result = consulta($anadir);
	}
	
}
else{
//	session_destroy();
//	echo "Usuario incorrecto";
//	echo "<a href='index.php'> Volver a intentar";
	login();
}
?>
</body>
</html>
