<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsPETS.php");

controlador($_POST['accion']);

function controlador($accion){

	$objPETS = new clsPETS();
	$objCase = new clsCase();

	switch ($accion){	
		case "NUEVO_PETS":
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				$carritoitempets = array();
				if (isset($_SESSION['carritoitempets'])) {
					$carritoitempets = $_SESSION['carritoitempets'];
				}
				//registrar la cebecera
				$valuesCabecera = $objPETS->getColumnRegprocedimiento_trabajo_seguro();
				$valuesCabecera[':fecha'] = formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':idempresa'] = $_POST['cboEmpresa'];
				$valuesCabecera[':idtitulardeactividad'] = $_POST['cboTitular'];
				$valuesCabecera[':idpersonalejecutador'] = $_POST['cboEjecutador'];
				$valuesCabecera[':idactividad'] = $_POST['cboActividad'];
				$valuesCabecera[':idarea'] = $_POST['cboArea'];
				if (isset($_POST['cboEPP'])) {
					$valuesCabecera[':equipoproteccion'] = implode(',', $_POST['cboEPP']);
				}
				if (isset($_POST['cboEHM'])) {
					$valuesCabecera[':equipoherramienta'] = implode(',',$_POST['cboEHM']);
				}
				
				$objCase->insertarWithoutUpper('regprocedimiento_trabajo_seguro', $valuesCabecera);
				$idprocedimiento = $objCase->getLastIdInsert('regprocedimiento_trabajo_seguro','idprocedimiento');
				if (count($carritoitempets)>0) {
					foreach ($carritoitempets as $k => $v) {
						$valuesDetalle = $objPETS->getColumnRegprocedimiento_trabajo_seguro_detalle();
						$valuesDetalle[':idprocedimiento'] = $idprocedimiento;
						$valuesDetalle[':titulo'] = $v['titulo'];
						$valuesDetalle[':descripcion'] = $v['descripcion'];
						$valuesDetalle[':tipo'] = $v['tipo'];
						$objCase->insertarWithoutUpper('regprocedimiento_trabajo_seguro_detalle', $valuesDetalle);
					}
				}

				//registrar detalle


				$cnx->commit();
				$mensaje="Registrado corrctamente";
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
			} catch (Exception $e) {
				$cnx->rollBack();
				$mensaje="*** Error al registrar PETS. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
		break;
		case 'MODIFICAR_PETS':
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				$carritoitempets = array();
				if (isset($_SESSION['carritoitempets'])) {
					$carritoitempets = $_SESSION['carritoitempets'];
				}
				//eliminar el detalle procesiminetos y recomendaciones
				$idpets=$_POST['idpets'];
				$detalle = $objCase->getListTableFiltroSimple("regprocedimiento_trabajo_seguro_detalle","idprocedimiento",$idpets);
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('regprocedimiento_trabajo_seguro_detalle', 'estado','E', 'idprocedimiento_detalle', $value['idprocedimiento_detalle']);
				}
				// actualizar la cabecera
				$valuesCabecera = $objPETS->getColumnRegprocedimiento_trabajo_seguro();
				$valuesCabecera[':idprocedimiento'] = $idpets;
				$valuesCabecera[':fecha'] = formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':idempresa'] = $_POST['cboEmpresa'];
				$valuesCabecera[':idtitulardeactividad'] = $_POST['cboTitular'];
				$valuesCabecera[':idpersonalejecutador'] = $_POST['cboEjecutador'];
				$valuesCabecera[':idactividad'] = $_POST['cboActividad'];
				$valuesCabecera[':idarea'] = $_POST['cboArea'];
				if (isset($_POST['cboEPP'])) {
					$valuesCabecera[':equipoproteccion'] = implode(',', $_POST['cboEPP']);
				}
				if (isset($_POST['cboEHM'])) {
					$valuesCabecera[':equipoherramienta'] = implode(',',$_POST['cboEHM']);
				}
				$objCase->actualizarWithoutUpper('regprocedimiento_trabajo_seguro', 'idprocedimiento', $valuesCabecera);
				//registrar nuevo detalle
				if (count($carritoitempets)>0) {
					foreach ($carritoitempets as $k => $v) {
						$valuesDetalle = $objPETS->getColumnRegprocedimiento_trabajo_seguro_detalle();
						$valuesDetalle[':idprocedimiento'] = $idpets;
						$valuesDetalle[':titulo'] = $v['titulo'];
						$valuesDetalle[':descripcion'] = $v['descripcion'];
						$valuesDetalle[':tipo'] = $v['tipo'];
						$objCase->insertarWithoutUpper('regprocedimiento_trabajo_seguro_detalle', $valuesDetalle);
					}
				}

				$cnx->commit();
				$mensaje="Actualizado corrctamente";
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
				
			} catch (Exception $e) {
				$cnx->rollBack();
				$mensaje="*** Error al modificar PETS. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
		break;
		case "GET_ACTIVIDADES_AREA":
			try{	
				$idarea = intval($_POST['idarea']);
				$data=$objCase->getListTableFiltroSimple('orgactividad', 'idarea', $idarea, 'estado', 'N');
				echo "<option value='0'>- Seleccione uno -</option>";
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					echo "<option value='".$fila['idactividad']."'>".$fila['nombre']."</option>";
				}

			}catch(Exception $e){
				echo "*** Error al obtener Data. ". $e->getMessage();
			}
			
		break;
		case 'CAMBIAR_ESTADO_PETS':
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idpets = $_POST['idpets'];
				$detalle = $objCase->getListTableFiltroSimple("regprocedimiento_trabajo_seguro_detalle","idprocedimiento",$idpets,'','','','','','','','','estado','E');
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('regprocedimiento_trabajo_seguro_detalle', 'estado', $_POST['estado'], 'idprocedimiento_detalle', $value['idprocedimiento_detalle']);
				}
	            $objCase->actualizarDatoSimple('regprocedimiento_trabajo_seguro', 'estado', $_POST['estado'], 'idprocedimiento', $idpets);

				$cnx->commit();
				echo "PETS actualizado de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar PETS. ". $e->getMessage();
			}
			break;

		default: 
				echo "Debe especificar alguna accion"; 
		break;
	}
	
}


?>