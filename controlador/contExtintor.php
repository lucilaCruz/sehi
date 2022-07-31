<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsExtintor.php");

controlador($_POST['accion']);

function controlador($accion){

	$objExtintor = new clsExtintor();
	$objCase = new clsCase();

	switch ($accion){	

		case "REGISTRAR_EXTINTOR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objExtintor->getColumnExtintor();
				$valuesCabecera[':idempresa']=$_POST['cboEmpresa'];
				$valuesCabecera[':idarea']=$_POST['cboArea'];
				$valuesCabecera[':nro_extintor']=$_POST['txtNroExtintor'];
				$valuesCabecera[':tipo']=$_POST['cboTipoExtintor'];
				$valuesCabecera[':clase_agente']=$_POST['idagentes'];
				$valuesCabecera[':capacidad']=$_POST['txtCapacidad'];
				$valuesCabecera[':ubicacion']=$_POST['txtUbicacion'];
				$valuesCabecera[':proveedor']= $_POST['txtProveedor'];
				$valuesCabecera[':descripcion']= $_POST['txtObservacion'];
				$objCase->insertarWithoutUpper('orgextintor', $valuesCabecera);

				$cnx->commit();
				echo "Extintor registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar extintor. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_EXTINTOR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idextintor = $_POST['idextintor'];
				$extintor = $objExtintor->consultarExtintorPorID($idextintor);
				$extintor = $extintor->fetch(PDO::FETCH_NAMED);

				$valuesCabecera = $objExtintor->getColumnExtintor();
				$valuesCabecera[':idextintor']=$idextintor;
				$valuesCabecera[':idempresa']=$extintor['idempresa'];
				$valuesCabecera[':idarea']=$_POST['cboArea'];
				$valuesCabecera[':nro_extintor']=$_POST['txtNroExtintor'];
				$valuesCabecera[':tipo']=$_POST['cboTipoExtintor'];
				$valuesCabecera[':clase_agente']=$_POST['idagentes'];
				$valuesCabecera[':capacidad']=$_POST['txtCapacidad'];
				$valuesCabecera[':ubicacion']=$_POST['txtUbicacion'];
				$valuesCabecera[':proveedor']= $_POST['txtProveedor'];
				$valuesCabecera[':descripcion']= $_POST['txtObservacion'];
				$objCase->actualizarWithoutUpper('orgextintor', 'idextintor', $valuesCabecera);

				$cnx->commit();
				echo "Extintor actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar extintor. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_EXTINTOR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idextintor = $_POST['idextintor'];
	            $objCase->actualizarDatoSimple('orgextintor', 'estado', $_POST['estado'], 'idextintor', $idextintor);

	 			$cnx->commit();
				echo "Recarga actualizado de forma satisfactoria.";
			}catch(Exception $e){
					$cnx->rollBack();
				echo "*** Error al editar la Recarga. ". $e->getMessage();
			}
			break;

		case "REGISTRAR_RECARGA_EXTINTOR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idextintor = $_POST['idextintor'];

				$valuesCabecera = $objExtintor->getColumnExtintorRecarga();
				$valuesCabecera[':idextintor']=$idextintor;
				$valuesCabecera[':f_recarga']= formatoBDFecha($_POST['txtFechaRecarga']);
				$valuesCabecera[':f_vencimiento']= formatoBDFecha($_POST['txtFechaVencimiento']);
				$valuesCabecera[':observacion']=$_POST['txtObservacion'];
				$objCase->insertarWithoutUpper('orgextintor_recarga', $valuesCabecera);

				$cnx->commit();
				echo "Recarga registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar recarga. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_RECARGA_EXTINTOR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idextintor_recarga = $_POST['idextintor_recarga'];
				$recarga = $objExtintor->consultarExtintorRecargaPorID($idextintor_recarga);
				$recarga = $recarga->fetch(PDO::FETCH_NAMED);

				$valuesCabecera = $objExtintor->getColumnExtintorRecarga();
				$valuesCabecera[':idextintor_recarga']=$idextintor_recarga;
				$valuesCabecera[':idextintor']=$recarga['idextintor'];
				$valuesCabecera[':f_recarga']= formatoBDFecha($_POST['f_recarga']);
				$valuesCabecera[':f_vencimiento']= formatoBDFecha($_POST['f_vencimiento']);
				$valuesCabecera[':observacion']=$_POST['observacion'];
				$objCase->actualizarWithoutUpper('orgextintor_recarga', 'idextintor_recarga', $valuesCabecera);

				$cnx->commit();
				echo "Recarga actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar recarga. ". $e->getMessage();
			}
			break;

		case "BORRAR_RECARGA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idextintor_recarga = $_POST['idextintor_recarga'];
	            $objCase->actualizarDatoSimple('orgextintor_recarga', 'estado', $_POST['estado'], 'idextintor_recarga', $idextintor_recarga);

	 			$cnx->commit();
				echo "Recarga actualizado de forma satisfactoria.";
			}catch(Exception $e){
					$cnx->rollBack();
				echo "*** Error al editar la Recarga. ". $e->getMessage();
			}
			break;

		case "NUEVA_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objExtintor->getColumnInspeccionExtintor();
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idarea']= $_POST['cboArea'];
				$valuesCabecera[':idtrabajador']= $_POST['cboResponsable'];
				$valuesCabecera[':locacion']= $_POST['txtLocacion'];
				$valuesCabecera[':anio']= $_POST['txtAnio'];
				$valuesCabecera[':mes']= $_POST['cboMes'];
				$objCase->insertarWithoutUpper('reginspeccion_extintor', $valuesCabecera);

				$idinspeccion_extintor = $objExtintor->getUltimaInspeccion();

				$cnx->commit();
				$mensaje="Inspección registrada de forma satisfactoria.";
				$resultado = array("mensaje"=>$mensaje, "idinspeccion_extintor"=>$idinspeccion_extintor);
				echo json_encode($resultado);

			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje="*** Error al registrar inspección. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje, "idinspeccion_extintor"=>$idinspeccion_extintor);
				echo json_encode($resultado);
			}
			break;
		
		case "MODIFICAR_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idinspeccion_extintor = $_POST['idinspeccion_extintor'];

				$valuesCabecera = $objExtintor->getColumnInspeccionExtintor();
				$valuesCabecera[':idinspeccion_extintor']= $idinspeccion_extintor;
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idarea']= $_POST['cboArea'];
				$valuesCabecera[':idtrabajador']= $_POST['cboResponsable'];
				$valuesCabecera[':locacion']= $_POST['txtLocacion'];
				$valuesCabecera[':anio']= $_POST['txtAnio'];
				$valuesCabecera[':mes']= $_POST['cboMes'];
				$objCase->actualizarWithoutUpper('reginspeccion_extintor', 'idinspeccion_extintor', $valuesCabecera);

				$cnx->commit();
				$mensaje="Inspección actualizada de forma satisfactoria.";
				$resultado = array("mensaje"=>$mensaje, "idinspeccion_extintor"=>$idinspeccion_extintor);
				echo json_encode($resultado);

			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje="*** Error al actualizar inspección. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje, "idinspeccion_extintor"=>$idinspeccion_extintor);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idinspeccion_extintor = $_POST['idinspeccion_extintor'];
				$detalle = $objCase->getListTableFiltroSimple("reginspeccion_extintor_detalle","idinspeccion_extintor",$idinspeccion_extintor);
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('reginspeccion_extintor_detalle', 'estado', $_POST['estado'], 'idinspeccion_extintor_detalle', $value['idinspeccion_extintor_detalle']);
				}
	            $objCase->actualizarDatoSimple('reginspeccion_extintor', 'estado', $_POST['estado'], 'idinspeccion_extintor', $idinspeccion_extintor);

				$cnx->commit();
				echo "Inspección actualizada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar inspección. ". $e->getMessage();
			}
			break;

		case "NUEVO_ITEM_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objExtintor->getColumnInspeccionExtintorDetalle();
				$valuesCabecera[':idinspeccion_extintor']= $_POST['idinspeccion_extintor'];
				$valuesCabecera[':idextintor']= $_POST['idextintor'];
				$valuesCabecera[':f_inspeccion']= formatoBDFecha($_POST['f_inspeccion']);
				$valuesCabecera[':sin_anomalia']= $_POST['sin_anomalia'];
				$valuesCabecera[':anomalia']= $_POST['lista_anomalias'];
				$objCase->insertarWithoutUpper('reginspeccion_extintor_detalle', $valuesCabecera);

				$cnx->commit();
				echo "Inspección registrada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar inspección. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_ITEM_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idinspeccion_extintor_detalle = $_POST['idinspeccion_extintor_detalle'];
				$detalle = $objExtintor->consultarInspeccionExtintorDetallePorID($idinspeccion_extintor_detalle);
				$detalle = $detalle->fetch(PDO::FETCH_NAMED);

				$valuesCabecera = $objExtintor->getColumnInspeccionExtintorDetalle();
				$valuesCabecera[':idinspeccion_extintor_detalle']=$idinspeccion_extintor_detalle;
				$valuesCabecera[':idinspeccion_extintor']=$detalle['idinspeccion_extintor'];
				$valuesCabecera[':idextintor']=$detalle['idextintor'];
				$valuesCabecera[':f_inspeccion']= formatoBDFecha($_POST['f_inspeccion']);
				$valuesCabecera[':sin_anomalia']= $_POST['sin_anomalia'];
				if($_POST['sin_anomalia']=='S'){
					$valuesCabecera[':anomalia']='N,N,N,N,N,N,N,N,N,N,N,N,N,N,N,N';
				}else{
					$valuesCabecera[':anomalia']=$_POST['lista_anomalias'];
				}
				$objCase->actualizarWithoutUpper('reginspeccion_extintor_detalle', 'idinspeccion_extintor_detalle', $valuesCabecera);

				$cnx->commit();
				echo "Item actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar item. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_ITEM_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idinspeccion_extintor_detalle = $_POST['idinspeccion_extintor_detalle'];
	            $objCase->actualizarDatoSimple('reginspeccion_extintor_detalle', 'estado', $_POST['estado'], 'idinspeccion_extintor_detalle', $idinspeccion_extintor_detalle);

				$cnx->commit();
				echo "Inspección actualizada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar inspección. ". $e->getMessage();
			}
			break;

		case "REGISTRAR_FIRMA_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$firma = $objCase->getRowTableFiltroSimple("multimedia_inspeccion_firma", "idinspeccion_extintor", $_POST['idinspeccion_extintor'],"idtrabajador",$_POST['idtrabajador']);
				//var_dump($firma);
				if($firma==null){
					$persona = $objCase->getRowTableFiltroSimple("persona", "idpersona",$_POST['idtrabajador']);

					$valuesCabecera = $objExtintor->getColumnInspeccionFirma();
					$valuesCabecera[':idinspeccion_extintor']= $_POST['idinspeccion_extintor'];
					$valuesCabecera[':idtrabajador']= $_POST['idtrabajador'];
					$valuesCabecera[':firma']= $persona['firma'];
					$objCase->insertarWithoutUpper('multimedia_inspeccion_firma', $valuesCabecera);
					
					$cnx->commit();
					echo "Firma registrada de forma satisfactoria.";
				}else{
					$cnx->commit();
					echo "** Ya existe registrado la firma.";
				}

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar firma. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_FIRMA_INSPECCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idinspeccion_firma = $_POST['idinspeccion_firma'];
	            $objCase->actualizarDatoSimple('multimedia_inspeccion_firma', 'estado', $_POST['estado'], 'idinspeccion_firma', $idinspeccion_firma);

				$cnx->commit();
				echo "Firma actualizada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar firma. ". $e->getMessage();
			}
			break;

		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>