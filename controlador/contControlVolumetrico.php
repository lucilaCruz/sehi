<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsControlVolumetrico.php");

controlador($_POST['accion']);

function controlador($accion){

	$objControl = new clsControlVolumetrico();
	$objCase = new clsCase();

	switch ($accion){	

		case "REGISTRAR_CONTROL_VOLUMETRICO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$valuesCabecera = $objControl->getColumnControlVolumetrico();

				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idresponsable']= $_POST['cboResponsable'];
				$valuesCabecera[':fecha']= formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':caudal']=  $_POST['txtCaudal'];
				$valuesCabecera[':medicion']= $_POST['txtMedicion'];
				$valuesCabecera[':producto']= $_POST['txtProducto'];
				$valuesCabecera[':observacion']= $_POST['txtObservacion'];
				$objCase->insertarWithoutUpper('regcontrol_volumetrico', $valuesCabecera);

				$idcontrol_volumetrico = $objControl->getUltimaControlVolumetrico();

				$cnx->commit();
				echo "Control volumétrico registrado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar control volumétrico. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_CONTROL_VOLUMETRICO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idcontrol_volumetrico = $_POST['idcontrol_volumetrico'];
				$control_volumetrico = $objControl->consultarControlVolumetricoPorID($idcontrol_volumetrico);
				$control_volumetrico = $control_volumetrico->fetch(PDO::FETCH_NAMED);

				$valuesCabecera = $objControl->getColumnControlVolumetrico();
				$valuesCabecera[':idcontrol_volumetrico']=$idcontrol_volumetrico;
				$valuesCabecera[':idempresa']= $_POST['cboEmpresa'];
				$valuesCabecera[':idresponsable']= $_POST['cboResponsable'];
				$valuesCabecera[':fecha']= formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':caudal']=  $_POST['txtCaudal'];
				$valuesCabecera[':medicion']= $_POST['txtMedicion'];
				$valuesCabecera[':producto']= $_POST['txtProducto'];
				$valuesCabecera[':observacion']= $_POST['txtObservacion'];
				$objCase->actualizarWithoutUpper('regcontrol_volumetrico', 'idcontrol_volumetrico', $valuesCabecera);

				$cnx->commit();
				echo "Control volumétrico actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar control volumétrico. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_CONTROL_VOLUMETRICO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idcontrol_volumetrico = $_POST['idcontrol_volumetrico'];
	            $objCase->actualizarDatoSimple('regcontrol_volumetrico', 'estado', $_POST['estado'], 'idcontrol_volumetrico', $idcontrol_volumetrico);

				$cnx->commit();
				echo "Control volumétrico actualizado de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar control volumétrico. ". $e->getMessage();
			}
			break;

		case "REGISTRAR_FIRMA_CONTROL":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$firma = $objCase->getRowTableFiltroSimple("multimedia_control_firma", "idcontrol_volumetrico", $_POST['idcontrol_volumetrico'],"idtrabajador",$_POST['idtrabajador']);
				if($firma==null){
					$persona = $objCase->getRowTableFiltroSimple("persona", "idpersona",$_POST['idtrabajador']);

					$valuesCabecera = $objControl->getColumnControlVolumetricoFirma();
					$valuesCabecera[':idcontrol_volumetrico']= $_POST['idcontrol_volumetrico'];
					$valuesCabecera[':idtrabajador']= $_POST['idtrabajador'];
					$valuesCabecera[':firma']= $persona['firma'];
					$objCase->insertarWithoutUpper('multimedia_control_firma', $valuesCabecera);
					
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

		case "CAMBIAR_ESTADO_FIRMA_CONTROL":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idcontrol_firma = $_POST['idcontrol_firma'];
	            $objCase->actualizarDatoSimple('multimedia_control_firma', 'estado', $_POST['estado'], 'idcontrol_firma', $idcontrol_firma);

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