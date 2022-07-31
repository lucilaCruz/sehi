<?php 
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsEquiposdeSeguridad.php");

controlador($_POST['accion']);

function controlador($accion){

	$objEquiposdeSeguridad = new clsEquiposdeSeguridad();
	$objCase = new clsCase();

	switch ($accion){	
		case "NUEVO_EQUIPOS_SEGURIDAD":
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				
				//inicio registrar detalle
				$carritoitemtrabajador = array();
				if (isset($_SESSION['carritoitemtrabajador'])) {
					$carritoitemtrabajador = $_SESSION['carritoitemtrabajador'];
				}
				$valuesCabecera = $objEquiposdeSeguridad->getColumnRegequipos_de_seguridad();
				$valuesCabecera[':fecha'] = formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':idempresa'] = $_POST['cboEmpresa'];
				$valuesCabecera[':tipodeequipo'] = $_POST['cboTiposdeEquipos'];
				$valuesCabecera[':equipos'] = implode(',', $_POST['cboEquipos']);
				$objCase->insertarWithoutUpper('regequipos_de_seguridad', $valuesCabecera);
				$idequiposseguridad = $objCase->getLastIdInsert('regequipos_de_seguridad','idequiposseguridad');
				if (count($carritoitemtrabajador)>0) {
					foreach ($carritoitemtrabajador as $k => $v) {
						$valuesDetalle = $objEquiposdeSeguridad->getColumnRegequipos_de_seguridad_detalle();
						$valuesDetalle[':idequiposseguridad'] = $idequiposseguridad;
						$valuesDetalle[':idpersona'] = $v['idpersona'];
						$valuesDetalle[':fecha_entrega'] = formatoBDFecha($v['fechaentrega']);
						$valuesDetalle[':fecha_renovacion'] = formatoBDFecha($v['fecharenovacion']);
						$valuesDetalle[':firma'] = $v['firma'];
						$objCase->insertarWithoutUpper('regequipos_de_seguridad_detalle', $valuesDetalle);
					}
				}
				
				//registrar detalle

				$cnx->commit();
				$mensaje="Registrado correctamente";
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
			} catch (Exception $e) {
				$cnx->rollBack();
				$mensaje="*** Error al registrar . ". $e->getMessage();
				$resultado = array("mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
		break;
		case 'MODIFICAR_EQUIPOS_SEGURIDAD':
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idequiposseguridad = $_POST['idequiposseguridad'];
				
				$carritoitemtrabajador = array();
				if (isset($_SESSION['carritoitemtrabajador'])) {
					$carritoitemtrabajador = $_SESSION['carritoitemtrabajador'];
				}
				//eliminar detalle
				$detalle = $objCase->getListTableFiltroSimple("regequipos_de_seguridad_detalle","idequiposseguridad",$idequiposseguridad);
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('regequipos_de_seguridad_detalle', 'estado','E', 'idequiposseguridad_detalle', $value['idequiposseguridad_detalle']);
				}
				//fin eliminar
				//inicio editar registro
				$valuesCabecera = $objEquiposdeSeguridad->getColumnRegequipos_de_seguridad();
				$valuesCabecera[':idequiposseguridad'] = $idequiposseguridad;
				$valuesCabecera[':fecha'] = formatoBDFecha($_POST['txtFecha']);
				$valuesCabecera[':idempresa'] = $_POST['cboEmpresa'];
				$valuesCabecera[':tipodeequipo'] = $_POST['cboTiposdeEquipos'];
				$valuesCabecera[':equipos'] = implode(',', $_POST['cboEquipos']);

				$objCase->actualizarWithoutUpper('regequipos_de_seguridad', 'idequiposseguridad', $valuesCabecera);

				if (count($carritoitemtrabajador)>0) {
					foreach ($carritoitemtrabajador as $k => $v) {
						$valuesDetalle = $objEquiposdeSeguridad->getColumnRegequipos_de_seguridad_detalle();
						$valuesDetalle[':idequiposseguridad'] = $idequiposseguridad;
						$valuesDetalle[':idpersona'] = $v['idpersona'];
						$valuesDetalle[':fecha_entrega'] = formatoBDFecha($v['fechaentrega']);
						$valuesDetalle[':fecha_renovacion'] = formatoBDFecha($v['fecharenovacion']);
						$valuesDetalle[':firma'] = $v['firma'];
						$objCase->insertarWithoutUpper('regequipos_de_seguridad_detalle', $valuesDetalle);
					}
				}
				//fin
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
		case "GET_DATA_EMPRESA":
			try{	
				$idempresa = intval($_POST['idempresa']);
				$data=$objCase->getListTableFiltroSimple('orgempresa', 'idempresa', $idempresa, 'estado', 'N');
				$tabla = "<tr>";
			
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$tabla .= "<td>".$fila['razon_social']."</td>";
					$tabla .= "<td>".$fila['ruc']."</td>";
					$tabla .= "<td>".$fila['direccion']."</td>";
					$tabla .= "<td>"."Actividad economica"."</td>";
					$tabla .= "<td>".$fila['nro_trabajadores']."</td>";
				}
				$tabla .="</tr>";
				echo $tabla;

			}catch(Exception $e){
				echo "*** Error al obtener Data. ". $e->getMessage();
			}
			
		break;
		case "GET_DATA_EQUIPOS":
			try{	
				$tipoequipo = $_POST['tipoequipo'];
				$idempresa =intval($_POST['idempresa']);
				$data=$objCase->getListTableFiltroSimple('orgequipo', 'tipo', $tipoequipo,'idempresa',$idempresa, 'estado', 'N');
				$return = "<option value='0'>- Seleccione uno -</option>";
			
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$return .= "<option value='".$fila['idequipo']."' > ".$fila['equipo']." </option>";
				}
				echo $return;

			}catch(Exception $e){
				echo "*** Error al obtener Data. ". $e->getMessage();
			}
			
		break;
		case 'CAMBIAR_ESTADO':
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				//inicio cambiar estado
				$idequiposseguridad = $_POST['idequiposseguridad'];
				$estado=$_POST['estado'];

				$detalle = $objCase->getListTableFiltroSimple("regequipos_de_seguridad_detalle","idequiposseguridad",$idequiposseguridad,'','','','','','','','','estado','E');
				$detalle = $detalle->fetchAll(PDO::FETCH_NAMED);
				foreach ($detalle as $key => $value) {
					$objCase->actualizarDatoSimple('regequipos_de_seguridad_detalle', 'estado',$estado, 'idequiposseguridad_detalle', $value['idequiposseguridad_detalle']);
				}
				$objCase->actualizarDatoSimple('regequipos_de_seguridad', 'estado', $estado, 'idequiposseguridad', $idequiposseguridad);
				//fin cambiar estado

				$cnx->commit();
				echo " actualizado de forma satisfactoria.";

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar . ". $e->getMessage();
			}
			break;

		default: 
				echo "Debe especificar alguna accion"; 
		break;
	}
	
}


?>