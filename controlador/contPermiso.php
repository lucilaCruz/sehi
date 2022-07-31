<?php 
require_once("../logica/clsPermiso.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");

controlador($_POST['accion']);

function controlador($accion){

	$objPermi = new clsPermiso;
	$objCase = new clsCase;

	switch ($accion){
		
		case "NUEVO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$values = $objPermi->getColumnTablaAspermiso();
				$values[':tipo']=$_POST['tipo'];
				$values[':forma']=$_POST['forma'];
				$values[':desde']=formatoBDFecha($_POST['desde']);
				$values[':hasta']=formatoBDFecha($_POST['hasta']);
				$values[':desdehoras']=$_POST['desdehoras'];
				$values[':hastahoras']=$_POST['hastahoras'];
				$values[':nrodocumento']=$_POST['nrodocumento'];
				$values[':descripcion']=$_POST['descripcion'];
				$values[':idpersona']=$_POST['idpersona'];
				$values[':nrodocpersona']=$_POST['nrodoccliente'];
				$values[':nombrepersona']=$_POST['nombrecliente'];
				$objCase->insertar('aspermiso', $values);

				$cnx->commit();
				echo "Permiso registrado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al registrar el permiso. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_PERMISO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idpermiso=$_POST['idpermiso'];
				$estado=$_POST['estado'];
                    
				$objCase->cambiarEstado('aspermiso', $estado, 'idpermiso', $idpermiso);

		 		$cnx->commit();
				echo "Permiso Eliminado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al eliminar. ". $e->getMessage();
			}
			break;

		case "MODIFICAR":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$values = $objPermi->getColumnTablaAspermiso();
				$values[':idpermiso']=$_POST['idpermiso'];
				$values[':tipo']=$_POST['tipo'];
				$values[':forma']=$_POST['forma'];
				$values[':desde']=formatoBDFecha($_POST['desde']);
				$values[':hasta']=formatoBDFecha($_POST['hasta']);
				$values[':desdehoras']=$_POST['desdehoras'];
				$values[':hastahoras']=$_POST['hastahoras'];
				$values[':nrodocumento']=$_POST['nrodocumento'];
				$values[':descripcion']=$_POST['descripcion'];
				$values[':idpersona']=$_POST['idpersona'];
				$values[':nrodocpersona']=$_POST['nrodoccliente'];
				$values[':nombrepersona']=$_POST['nombrecliente'];
				$objCase->actualizar('aspermiso','idpermiso', $values);

				$cnx->commit();
				echo "Permiso actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar el Permiso. ". $e->getMessage();
			}
			break;

        default:
            echo "***Debe especificar alguna accion";
            break;
	}
	
}

?>