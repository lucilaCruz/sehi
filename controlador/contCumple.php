<?php 
require_once("../logica/clsCumple.php");

controlador($_POST['accion']);

function controlador($accion){
	$objCumple= new clsCumple();
	
	switch ($accion){	
		case "SET_COMPARTIDO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
				$cnx->beginTransaction();
				$objCumple->setCompartidoSaludo($_SESSION["idusuario"],$_SESSION["idciclo_actual"]);
				$cnx->commit();		
				echo "OK";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** ".$e->getMessage();	
			}
			break;			
		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
}
?>