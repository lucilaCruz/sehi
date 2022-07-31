<?php 
require_once("../logica/clsTablaGeneral.php");
controlador($_POST['accion']);

function controlador($accion){
	$objTG=new clsTablaGeneral();
	switch ($accion){
		
		case "LIST_TG": 
				try{
					$data=$objTG->consultarTablaByCodigo($_POST['codigo']);
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['codelemento']."'>".$fila['descripcion']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "LIST_TG_UT": 
				try{
					$data=$objTG->consultarTablaByCodigo($_POST['codigo']);
                    
                    if($_POST['codigo']=='006'){
					   echo "<option value='-9'>- Todos los días -</option>";
                    }else{
                        echo "<option value='0'>- Todos -</option>";
                    }
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['codelemento']."'>".$fila['descripcion']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "LIST_TG_SU": 
				try{
					$data=$objTG->consultarTablaByCodigo($_POST['codigo']);
					$factore='';
					if(isset($_POST['factore'])){
						$factore=$_POST['factore'];
					}
					echo "<option value='0'>- Seleccione uno -</option>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						if($factore=='' || $factore==$fila['factore']){
							echo "<option value='".$fila['codelemento']."'>".$fila['descripcion']."</option>";
						}
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "LIST_ICON":
				try{
					$data=$objTG->consultarTablaByCodigo('013');
					echo "<div class='no-padding' style='max-height:150px; overflow-x: auto; margin: -9px -14px;'>";
					echo "<table class='table table-bordered table-hover' style='cursor:pointer'>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<tr onClick='SeleccionarIcono(\"".$fila['descripcion2']."\",\"".$fila['descripcion']."\")'><td align='center'><li class='fa ".$fila['descripcion2']."'></li></td><td width='150'>".$fila['descripcion']."</td></tr>";
					}
					echo "</table>";
					echo "</div>";
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		
		case "LIST_COLOR":
				try{
					$data=$objTG->consultarTablaByCodigo('009');
					echo "<div class='no-padding' style='max-height:150px; overflow-x: auto; margin: -9px -14px;'>";
					echo "<table class='table table-bordered table-hover' style='cursor:pointer'>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<tr onClick='SeleccionarColor(\"".$fila['descripcion2']."\",\"".$fila['descripcion']."\")'><td align='center'><span class='text-".$fila['descripcion2']."'><li class='fa fa-circle'></li></span></td><td width='150'>".$fila['descripcion']."</td></tr>";
					}
					echo "</table>";
					echo "</div>";
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