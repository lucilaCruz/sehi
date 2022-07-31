<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsIPERC.php");

controlador($_POST['accion']);

function controlador($accion){

	$objIPERC = new clsIPERC();
	$objCase = new clsCase();

	switch ($accion){	

		case "NUEVO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objIPERC->getColumnTablaIPERC();
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idarea']= $_POST['cboArea'];
				$valuesCabecera[':idpuesto_trabajo']= $_POST['cboPuesto'];
				$valuesCabecera[':idactividad']= $_POST['cboActividad'];
				$valuesCabecera[':peligro']=$_POST['txtPeligro'];
				$valuesCabecera[':requisito_legal']=$_POST['txtRequisitoLegal'];
				$valuesCabecera[':descripcion']=$_POST['txtDescripcion'];
				$valuesCabecera[':persona_expuesta']=$_POST['persona_expuesta'];
				$valuesCabecera[':procedimiento_existente']=$_POST['procedimiento_existente'];
				$valuesCabecera[':indice_capacitacion']=$_POST['indice_capacitacion'];
				$valuesCabecera[':exposicion_riesgo']=$_POST['exposicion_riesgo'];
				$valuesCabecera[':indice_probabilidad']=$_POST['indice_probabilidad'];
				$valuesCabecera[':indice_severidad']=$_POST['indice_severidad'];
				$valuesCabecera[':riesgo']=$_POST['riesgo'];
				$valuesCabecera[':nivel_riesgo_1']=$_POST['nivel_riesgo_1'];
				$valuesCabecera[':significativo']=$_POST['significativo'];
				$valuesCabecera[':eliminacion']=$_POST['eliminacion'];
				$valuesCabecera[':sustitucion']=$_POST['sustitucion'];
				$valuesCabecera[':control_ingenieria']=$_POST['control_ingenieria'];
				$valuesCabecera[':control_administrativo']=$_POST['control_administrativo'];
				$valuesCabecera[':epp']=$_POST['epp'];
				$valuesCabecera[':riesgo_residual']=$_POST['riesgo_residual'];
				$valuesCabecera[':nivel_riesgo_2']=$_POST['nivel_riesgo_2'];
				$objCase->insertarWithoutUpper('orgiperc', $valuesCabecera);

				$cnx->commit();
				echo "Peligro registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar peligro. ". $e->getMessage();
			}
			break;

		case "MODIFICAR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idiperc = $_POST['idiperc'];
				$iperc = $objIPERC->consultarIPERCPorID($idiperc);
				$iperc = $iperc->fetch(PDO::FETCH_NAMED);

				$valuesCabecera = $objIPERC->getColumnTablaIPERC();
				$valuesCabecera[':idiperc']= $idiperc;
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idarea']= $_POST['cboArea'];
				$valuesCabecera[':idpuesto_trabajo']= $_POST['cboPuesto'];
				$valuesCabecera[':idactividad']= $_POST['cboActividad'];
				$valuesCabecera[':peligro']=$_POST['txtPeligro'];
				$valuesCabecera[':requisito_legal']=$_POST['txtRequisitoLegal'];
				$valuesCabecera[':descripcion']=$_POST['txtDescripcion'];
				$valuesCabecera[':persona_expuesta']=$_POST['persona_expuesta'];
				$valuesCabecera[':procedimiento_existente']=$_POST['procedimiento_existente'];
				$valuesCabecera[':indice_capacitacion']=$_POST['indice_capacitacion'];
				$valuesCabecera[':exposicion_riesgo']=$_POST['exposicion_riesgo'];
				$valuesCabecera[':indice_probabilidad']=$_POST['indice_probabilidad'];
				$valuesCabecera[':indice_severidad']=$_POST['indice_severidad'];
				$valuesCabecera[':riesgo']=$_POST['riesgo'];
				$valuesCabecera[':nivel_riesgo_1']=$_POST['nivel_riesgo_1'];
				$valuesCabecera[':significativo']=$_POST['significativo'];
				$valuesCabecera[':eliminacion']=$_POST['eliminacion'];
				$valuesCabecera[':sustitucion']=$_POST['sustitucion'];
				$valuesCabecera[':control_ingenieria']=$_POST['control_ingenieria'];
				$valuesCabecera[':control_administrativo']=$_POST['control_administrativo'];
				$valuesCabecera[':epp']=$_POST['epp'];
				$valuesCabecera[':riesgo_residual']=$_POST['riesgo_residual'];
				$valuesCabecera[':nivel_riesgo_2']=$_POST['nivel_riesgo_2'];
				$objCase->actualizarWithoutUpper('orgiperc', 'idiperc', $valuesCabecera);

				$cnx->commit();
				echo "Peligro actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar peligro. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_IPERC":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idiperc = $_POST['idiperc'];
	            $objCase->actualizarDatoSimple('orgiperc', 'estado', $_POST['estado'], 'idiperc', $idiperc);

	 			$cnx->commit();
				echo "Peligro actualizado de forma satisfactoria.";
			}catch(Exception $e){
					$cnx->rollBack();
				echo "*** Error al editar la peligro. ". $e->getMessage();
			}
			break;

		case "CARGAR_PUESTOS_TRABAJO":
			try{	

				$idarea = intval($_POST['idarea']);

				$data=$objCase->getListTableFiltroSimple('orgpuesto_trabajo', 'idarea', $idarea, 'estado', 'N');
				echo "<option value='0'>- Seleccione uno -</option>";
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					echo "<option value='".$fila['idpuesto_trabajo']."'>".$fila['nombre']."</option>";
				}

			}catch(Exception $e){
				echo "*** Error al obtener Data. ". $e->getMessage();
			}
			break;

		case "CARGAR_ACTIVIDADES":
			try{	
				
				$idpuesto = intval($_POST['idpuesto']);

				$data=$objCase->getListTableFiltroSimple('orgactividad', 'idpuesto_trabajo', $idpuesto, 'estado', 'N');
				echo "<option value='0'>- Seleccione uno -</option>";
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					echo "<option value='".$fila['idactividad']."'>".$fila['nombre']."</option>";
				}

			}catch(Exception $e){
				echo "*** Error al obtener Data. ". $e->getMessage();
			}
			break;

		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>