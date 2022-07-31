<?php 
require_once("../logica/clsAcceso.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	$objAcc=new clsAcceso();
	switch ($accion){
		
		case "REGISTRAR_ACCESO": 
				try{
					$existencia = $objAcc->consultarAccesoByIdUsuario($_POST["txtIdUsuario"]);

					if($existencia->rowCount()>0){
						$acceso = $existencia->fetch(PDO::FETCH_NAMED);
						$objAcc->actualizarAcceso($acceso["idacceso"], $_POST["cboSucursal"],  $_POST["cboCiclo"], $_POST["cboTurno"], $_POST["idaulas"]);
					}else{
						$objAcc->insertarAcceso($_POST["txtIdUsuario"], $_POST["cboSucursal"], $_POST["cboCiclo"], $_POST["cboTurno"], $_POST["idaulas"], $_SESSION["idusuario"]);
					}

					echo "Datos registrados satisfactoriamente";
						
				}catch(Exception $e){
					echo "Lo sentimos datos no han podido ser registrados, intentelo nuevamente";
				}
				break;

			case "VERIFICAR_CLAVERAPIDA": 
				try{
					$existe=$objAcc->consultarClaveRapida($_POST['clave']);
					if($existe->rowCount()>0){
						echo "Clave Correcta";
					}else{
						
						echo "***** Clave Incorrecta *****";
						
					}
				}catch(Exception $e){
					echo "Lo sentimos cuenta no ha podido ser registrada, intentelo nuevamente";
				}
				break;		
		
		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}

?>