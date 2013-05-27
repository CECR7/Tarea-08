<?php

function validarrut($rut, $digito) {
	$rut = intval($rut);

	if ($rut != 0 && (intval($digito) > 0 || (intval($digito) == 0 && $digito == "0") || $digito == "k" || $digito == "K")) {

		$multiplicador = 2;
		$verificador = 0;

		do {
			$modulo = $rut % 10;
			if ($multiplicador > 7)
				$multiplicador = 2;
			$verificador += $modulo * $multiplicador;
			$multiplicador++;
			$rut = ($rut - $modulo) / 10;
		} while ($rut != 0);

		$verificador = 11 - ($verificador % 11);
		//calculo de guion

		switch ($verificador) {
			case 11 :
				$verificador = 0;
				break;
			case 10 :
				$verificador = "k";
				break;
		}

		if ($verificador == intval($digito))
			return true;
		elseif ($verificador == "k" && ($digito == "K" || $digito == "k"))
			return true;
		else
			return false;
	} else
		return false;
}

if (!empty($_POST["usuario"]) && !empty($_POST["contraseña"]) && is_numeric($_POST["usuario"]) && (is_numeric($_POST["verificador"]) || $_POST["verificador"] == "k" || $_POST["verificador"] == "K")) {
	$usuario = $_POST["usuario"];
	$verificador = $_POST["verificador"];

	if (validarrut($usuario, $verificador)) {
		$clave = $_POST["contraseña"];
		$clave = strtoupper($clave);
		$clave = hash("sha256", $clave);
		
		
		$soapclient = new soapclient('http://informatica.utem.cl:8011/dirdoc-auth/ws/auth?wsdl');
		
		$arreglo=array("rut"=>$usuario. "-" . $verificador,"password"=>$clave);
		
		$resultado=$soapclient->autenticar($arreglo);
		
		$mensaje=(string)$resultado->return->mensaje;
		
		$descripcion=(string)$resultado->return->descripcion;
		
		echo "mensaje:<br>$mensaje<br>";
		
		echo "descripcion:<br>$descripcion<br>";
		 
		
	} else {
		echo "rut invalido";
		//echo '<META HTTP-EQUIV="REFRESH" CONTENT="5;URL=Tarea8.html>';
		header("Location: Tarea8.html");
	}

} else {
	echo "ingrese los datos que faltan";
	//echo '<META HTTP-EQUIV="REFRESH" CONTENT="5;URL=Tarea8.html>';
	header("Location: Tarea8.html");
}
?>