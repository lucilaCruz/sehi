<?php 
require_once("../logica/clsCompartido.php");
require_once("../sunat/autoload.php");


controlador($_POST['accion']);

function controlador($accion){
	//session_start();
	//$objCi=new clsCiclo();
	switch ($accion){	

		case "RUC_SUNAT": 
			try{
				$ruc = $_POST['ruc'];	
				/*
				$company = new Sunat( true, true );							
				$search1 = $company->search( $ruc );
				if( $search1->success == true ){
					echo $search1->json();
				}else{
					echo json_encode(array());
				}*/
				$url = "http://tst.taqini.pe/sunat/ws/wsRuc.php?accion=RUC&ruc=".$ruc;
				$resultado = @file_get_contents($url);
				echo $resultado;

			}catch(Exception $e){
				echo "Los sentimos, datos no pudieron ser obtenidos";
			}
			break;

		case "TC_SUNAT": 
			try{
				
				$company = new Sunat( true, true );
				$tipo = $_POST['tipo'];
				$fecha = explode('/',$_POST['fecha']);
				$dia = $fecha[0];
				$mes = $fecha[1];
				$anio= $fecha[2];				
				$resultado = $company->getTipoCambio($dia, $mes, $anio);
				echo json_encode($resultado);				
			}catch(Exception $e){
				echo "***Los sentimos, datos no pudieron ser obtenidos";
			}
			break;

		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>