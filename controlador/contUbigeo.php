<?php 
require_once("../logica/clsUbigeo.php");
controlador($_POST['accion']);

function controlador($accion){
	//session_start();
	$objUbi=new clsUbigeo();
	switch ($accion){
		
		case "LIST_DEPARTAMENTO": 
				try{
					$data=$objUbi->consultarDepartamento();
					echo "<option value='0'>- Departamento -</option>";
					while($fila=$data->fetch(PDO::FETCH_NUM)){
						echo "<option value='".$fila[0]."'>".$fila[1]."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		case "LIST_PROVINCIA": 
				try{
					$data=$objUbi->consultarProvincia($_POST['iddepartamento']);
					echo "<option value='0'>- Provincia -</option>";
					while($fila=$data->fetch(PDO::FETCH_NUM)){
						echo "<option value='".$fila[0]."'>".$fila[1]."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		case "LIST_DISTRITO": 
				try{
					$data=$objUbi->consultarDistrito($_POST['idprovincia']);
					echo "<option value='0'>- Distrito -</option>";
					while($fila=$data->fetch(PDO::FETCH_NUM)){
						echo "<option value='".$fila[0]."'>".$fila[1]."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		case "LIST_DISTRITO": 
				try{
					$data=$objUbi->consultarDistrito($_POST['idprovincia']);
					echo "<option value='0'>- Distrito -</option>";
					while($fila=$data->fetch(PDO::FETCH_NUM)){
						echo "<option value='".$fila[0]."'>".$fila[1]."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
		break;
		//Lista todos los departamentos provincias y distritos
		case "LIST_UBIGEO": 
				try{
					$data=$objUbi->listarUbigeo();
					echo "<option value='0-0-0'>- Ciudad -</option>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						$value=$fila['iddepartamento'].'-'.$fila['idprovincia'].'-'.$fila['iddistrito'];
						$descripcion=$fila['departamento'].'/'.$fila['provincia'].'/'.$fila['distrito'];
						echo "<option value='".$value."'>".$descripcion."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
		break;
		
		case "GET_UBIGEO_COLEGIO":
			try{
					$data=$objUbi->consultarUbigeoColegio($_POST['idcolegio']);
					$datos=$data->fetch(PDO::FETCH_NUM);
					echo implode("#",$datos);
					
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>