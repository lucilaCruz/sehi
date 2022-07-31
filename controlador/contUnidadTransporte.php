<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsUnidadTransporte.php");

controlador($_POST['accion']);

function controlador($accion){

	$objUnidad = new clsUnidadTransporte();
	$objCase = new clsCase();

	switch ($accion){	

		case "NUEVA_UNIDAD":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objUnidad->getColumnUnidadTransporte();
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idunidad_transporte']= $_POST['cboUnidad'];
				$valuesCabecera[':idresponsable']= $_POST['cboResponsable'];
				$valuesCabecera[':fecha']= formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':razon_social']= $_POST['txtRazonSocial'];
				$valuesCabecera[':ruc']= $_POST['txtRUC'];
				$valuesCabecera[':domicilio']= $_POST['txtDomicilio'];
				$valuesCabecera[':actividad']= $_POST['txtActividad'];
				$valuesCabecera[':trabajadores']= $_POST['txtNroTrabajadores'];
				$objCase->insertarWithoutUpper('regunidad_transporte', $valuesCabecera);

				if($_POST['licencia_r']!=null || $_POST['licencia_r']!=''){
					$objCase->actualizarDatoSimple("persona","licencia",$_POST['licencia_r'],"idpersona",$_POST['cboResponsable']);
				}

				$idunidad = $objUnidad->getUltimaUnidadTransporte();

				$cnx->commit();
				$mensaje="Unidad de Transporte registrada de forma satisfactoria.";
				$resultado = array("mensaje"=>$mensaje, "idunidad"=>$idunidad);
				echo json_encode($resultado);

			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje="*** Error al registrar Unidad de Transporte. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje, "idunidad"=>$idunidad);
				echo json_encode($resultado);
			}
			break;
		
		case "MODIFICAR_UNIDAD":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idunidad = $_POST['idunidad'];

				$valuesCabecera = $objUnidad->getColumnUnidadTransporte();
				$valuesCabecera[':idunidad']= $idunidad;
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idunidad_transporte']= $_POST['cboUnidad'];
				$valuesCabecera[':idresponsable']= $_POST['cboResponsable'];
				$valuesCabecera[':fecha']= formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':razon_social']= $_POST['txtRazonSocial'];
				$valuesCabecera[':ruc']= $_POST['txtRUC'];
				$valuesCabecera[':domicilio']= $_POST['txtDomicilio'];
				$valuesCabecera[':actividad']= $_POST['txtActividad'];
				$valuesCabecera[':trabajadores']= $_POST['txtNroTrabajadores'];
				$objCase->actualizarWithoutUpper('regunidad_transporte', 'idunidad', $valuesCabecera);

				if($_POST['licencia_r']!=null || $_POST['licencia_r']!=''){
					$objCase->actualizarDatoSimple("persona","licencia",$_POST['licencia_r'],"idpersona",$_POST['cboResponsable']);
				}

				$cnx->commit();
				$mensaje="Unidad de Transporte actualizada de forma satisfactoria.";
				$resultado = array("mensaje"=>$mensaje, "idunidad"=>$idunidad);
				echo json_encode($resultado);

			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje="*** Error al actualizar Unidad de Transporte. ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje, "idunidad"=>$idunidad);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_UNIDAD_TRANSPORTE":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idunidad = $_POST['idunidad'];
				$detalle = $objCase->getListTableFiltroSimple("regunidad_transporte_detalle","idunidad",$idunidad);
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('regunidad_transporte_detalle', 'estado', $_POST['estado'], 'idunidad_detalle', $value['idunidad_detalle']);
				}
	            $objCase->actualizarDatoSimple('regunidad_transporte', 'estado', $_POST['estado'], 'idunidad', $idunidad);

				$cnx->commit();
				echo "Unidad de Transporte actualizada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar Unidad de Transporte. ". $e->getMessage();
			}
			break;

		case "GUARDAR_UNIDAD_DETALLE":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$epp = $objCase->getRowTableFiltroSimple('regunidad_transporte_detalle','idunidad',$_POST['idunidad'],'idtipo',$_POST['idtipo'],'tipo',$_POST['tipo'],'estado','N');

				if(empty($epp)){
					//NUEVO
					$valuesCabecera = $objUnidad->getColumnUnidadTransporteDetalle();
					$valuesCabecera[':idunidad']= $_POST['idunidad'];
					$valuesCabecera[':idtipo']= $_POST['idtipo'];
					$valuesCabecera[':tipo']= $_POST['tipo'];
					$valuesCabecera[':valor']= $_POST['valor'];
					$valuesCabecera[':valor2']= '';
					$valuesCabecera[':descripcion']= $_POST['descripcion'];
					$objCase->insertarWithoutUpper('regunidad_transporte_detalle', $valuesCabecera);
				}else{
					//MODIFICAR
					$valuesCabecera = $objUnidad->getColumnUnidadTransporteDetalle();
					$valuesCabecera[':idunidad_detalle']= $epp['idunidad_detalle'];
					$valuesCabecera[':idunidad']= $_POST['idunidad'];
					$valuesCabecera[':idtipo']= $_POST['idtipo'];
					$valuesCabecera[':tipo']= $_POST['tipo'];
					$valuesCabecera[':valor']= $_POST['valor'];
					$valuesCabecera[':valor2']= '';
					$valuesCabecera[':descripcion']= $_POST['descripcion'];
					$objCase->actualizarWithoutUpper('regunidad_transporte_detalle', 'idunidad_detalle', $valuesCabecera);
				}
				

				$cnx->commit();
				echo "Unidad registrada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar Unidad. ". $e->getMessage();
			}
			break;

		case "GUARDAR_COMENTARIO_UNIDAD_DETALLE":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$epp = $objCase->getRowTableFiltroSimple('regunidad_transporte_detalle','idunidad',$_POST['idunidad'],'idtipo',$_POST['idtipo'],'tipo',$_POST['tipo'],'estado','N');

				if(!empty($epp)){

					$objCase->actualizarDatoSimple("regunidad_transporte_detalle","descripcion",$_POST['descripcion'],"idunidad_detalle",$epp['idunidad_detalle']);

					$cnx->commit();
					echo "OK";
				}else{
					$cnx->commit();
					echo "*** ERROR";
				}
				
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar Unidad. ". $e->getMessage();
			}
			break;

		case "REGISTRAR_FIRMA_UNIDAD_TRANSPORTE":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$firma = $objCase->getRowTableFiltroSimple("multimedia_unidad_firma", "idunidad", $_POST['idunidad'],"idtrabajador",$_POST['idtrabajador']);
				if($firma==null){
					$persona = $objCase->getRowTableFiltroSimple("persona", "idpersona",$_POST['idtrabajador']);

					$valuesCabecera = $objUnidad->getColumnUnidadTransporteFirma();
					$valuesCabecera[':idunidad']= $_POST['idunidad'];
					$valuesCabecera[':idtrabajador']= $_POST['idtrabajador'];
					$valuesCabecera[':firma']= $persona['firma'];
					$objCase->insertarWithoutUpper('multimedia_unidad_firma', $valuesCabecera);
					
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

				$idunidad_firma = $_POST['idunidad_firma'];
	            $objCase->actualizarDatoSimple('multimedia_unidad_firma', 'estado', $_POST['estado'], 'idunidad_firma', $idunidad_firma);

				$cnx->commit();
				echo "Firma actualizada de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar firma. ". $e->getMessage();
			}
			break;

		case "GET_UNIDAD":
		        
			$unidad = $objUnidad->consultarUnidadPorID($_POST['idunidad_transporte']);
			if($unidad->rowCount()>0){
				$unidad = $unidad->fetch(PDO::FETCH_ASSOC);
				$unidad['unidad']=$unidad['unidad'];
				$unidad['marca']=$unidad['marca'];
				$unidad['modelo']=$unidad['modelo'];
				$unidad['placa_tracto']=$unidad['placa_tracto'];
				$unidad['placa']=$unidad['placa'];
			}else{
				$unidad = array();
			}
			echo json_encode($unidad);	
			break;

		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>